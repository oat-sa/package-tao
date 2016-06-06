/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Make an object an event emitter.
 *
 * @example simple usage
 * var emitter = eventifier({});
 * emitter.on('hello', function(who){
 *      console.log('Hello ' + who);
 * });
 * emitter.trigger('hello', 'world');
 *
 * @example using before
 * emitter.before('hello', function(e, who){
 *      if(!who || who === 'nobody'){
 *          console.log('I am not saying Hello to nobody');
 *          return false;
 *      }
 * });
 *
 * @example using before asynchronously
 * emitter.before('hello', function(e, who){
 *
 *      //I am in an asynchronous context
 *      var done = e.done();
 *
 *      //ajax call
 *      fetch('do/I/know?who='+who).then(function(yes){
 *          if(yes){
 *              console.log('I know', who);
 *              e.done();
 *          }else{
 *              console.log('I don't talk to stranger');
 *              e.prevent();
 *          }
 *      })).catch(function(){
 *          console.log('System failure, I should quit now');
 *          e.preventNow();
 *      });
 * });
 *
 * TODO replace before done syntax by promises
 * TODO support flow control for all types of events not only before.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'async',
    'lib/uuid'
], function(_, async, uuid){
    'use strict';

    /**
     * All events have a namespace, this one is the default
     */
    var globalNs = '*';

    /**
     * Create an async callstack
     * @param {array} handlers - array of handlers to create the async callstack from
     * @param {object} context - the object that each handler will be applied on
     * @param {array} args - the arguments passed to each handler
     * @param {function} success - the success callback
     * @returns {array} array of aync call stack
     */
    function createAsyncCallstack(handlers, context, args, success){

        var callstack =  _.map(handlers, function(handler){

            //return an async call
            return function(cb){

                var result;
                var asyncMode = false;
                var _args = _.clone(args);
                var event = {
                    done : function asyncDone(){
                        asyncMode = true;
                        //returns the done function and wait until it is called to continue the async queue processing
                        return done;
                    },
                    prevent : function asyncPrevent(){
                        asyncMode = true;
                        //immediately call prevent()
                        prevent();
                    },
                    preventNow : function asyncPreventNow(){
                        asyncMode = true;
                        //immediately call preventNow()
                        preventNow();
                    }
                };

                /**
                 * Call success
                 * @private
                 */
                function done(){
                    //allow passing to next
                    cb(null, {success:true});
                }

                /**
                 * Call fail but can continue to next loop
                 * @private
                 */
                function prevent(){
                    cb(null, {success:false});
                }

                /**
                 * Call fail and must stop the execution of the stack right now
                 * @returns {undefined}
                 */
                function preventNow(){
                    //stop async processing queue right now
                    cb(new Error('prevent now'), {success:false, immediate:true});
                }

                //set the event object as the first argument
                _args.unshift(event);
                result = handler.apply(context, _args);

                if(!asyncMode){
                    if(result === false){
                        //if the call
                        prevent();
                    }else{
                        done();
                    }
                }
            };
        });

        async.series(callstack, function(err, results){
            var successes = _.pluck(results, 'success');
            if(_.indexOf(successes, false) === -1){
                success();
            }
        });
    }

    /**
     * Get the list of events from an eventName string (ie, separated by spaces)
     * @param {String} eventNames - the event strings
     * @returns {String[]} the event list (no empty, no duplicate)
     */
    function getEventNames(eventNames){
        if(!_.isString(eventNames) || _.isEmpty(eventNames)){
            return [];
        }
        return _(eventNames.split(/\s/g)).compact().uniq().value();
    }

    /**
     * Get the name part of an event name: the 'foo' of 'foo.bar'
     * @param {String} eventName - the name of the event
     * @returns {String} the name part
     */
    function getName(eventName){
        if(eventName.indexOf('.') > -1){
            return eventName.substr(0, eventName.indexOf('.'));
        }
        return eventName;
    }

    /**
     * Get the namespace part of an event name: the 'bar' of 'foo.bar'
     * @param {String} eventName - the name of the event
     * @returns {String} the namespace, that defaults to globalNs
     */
    function getNamespace(eventName){
        if(eventName.indexOf('.') > -1){
            return eventName.substr(eventName.indexOf('.') + 1);
        }
        return globalNs;
    }

    /**
     * Creates a new EventHandler object structure
     * @returns {Object} the handler structure
     */
    function getHandlerObject(){
        return {
            before : [],
            between: [],
            after  : []
        };
    }

    /**
     * Makes the target an event emitter by delegating calls to the event API.
     * @param {Object} [target = {}] - the target object, a new plain object is created when omited.
     * @param {logger} [logger] - a logger to trace events
     * @returns {Object} the target for conveniance
     */
    function eventifier(target, logger){

        var targetName;

        //it stores all the handlers under ns/name/[handlers]
        var eventHandlers  = {};

        /**
         * Get the handlers for an event type
         * @param {String} eventName - the event name, namespace included
         * @param {String} [type = 'between'] - the type of event in before, between and after
         * @returns {Function[]} the handlers
         */
        var getHandlers = function getHandlers(eventName, type){
            var name = getName(eventName);
            var ns = getNamespace(eventName);

            type = type || 'between';
            eventHandlers[ns] = eventHandlers[ns] || {};
            eventHandlers[ns][name] = eventHandlers[ns][name] || getHandlerObject();
            return eventHandlers[ns][name][type];
        };

       /**
        * The API itself is just a placeholder, all methods will be delegated to a target.
        */
        var eventApi = {

           /**
            * Attach an handler to an event.
            * Calling `on` with the same eventName multiple times add callbacks: they
            * will all be executed.
            *
            * @example target.on('foo', function(bar){ console.log('Cool ' + bar) } );
            *
            * @this the target
            * @param {String} eventNames- the name of the event, or multiple events separated by a space
            * @param {Function} handler - the callback to run once the event is triggered
            * @returns {Object} the target object
            */
            on : function on(eventNames, handler){
                if(_.isFunction(handler)){
                    _.forEach(getEventNames(eventNames), function(eventName){
                        getHandlers(eventName).push(handler);
                    });
                }
                return this;
            },

           /**
            * Remove ALL handlers for an event.
            *
            * @example target.off('foo');
            *
            * @this the target
            * @param {String} eventNames- the name of the event, or multiple events separated by a space
            * @returns {Object} the target object
            */
            off : function off(eventNames){

                _.forEach(getEventNames(eventNames), function(eventName){

                    var name = getName(eventName);
                    var ns = getNamespace(eventName);

                    if(ns && !name){
                        //off the complete namespace
                        eventHandlers[ns] = {};
                    } else {

                        _.forEach(eventHandlers, function(nsHandlers, namespace){
                            if(nsHandlers[name] && (ns === globalNs || ns === namespace)){
                                nsHandlers[name] = getHandlerObject();
                            }
                        });
                    }
                });
                return this;
            },

            /**
            * Trigger an event.
            *
            * @example target.trigger('foo', 'Awesome');
            *
            * @this the target
            * @param {String} eventNames- the name of the event to trigger, or multiple events separated by a space
            * @returns {Object} the target object
            */
            trigger : function trigger(eventNames){
                var self = this;
                var args = [].slice.call(arguments, 1);

                _.forEach(getEventNames(eventNames), function(eventName){
                    var ns = getNamespace(eventName);
                    var name = getName(eventName);

                    //check which ns needs to be executed and then merge the handlers to be executed
                    var mergedHandlers = _(eventHandlers)
                    .filter(function(nsHandlers, namespace){
                        return nsHandlers[name] && (ns === globalNs || ns === namespace);
                    })
                    .reduce(function(acc, nsHandlers){
                        acc.before  = acc.before.concat(nsHandlers[name].before);
                        acc.between = acc.between.concat(nsHandlers[name].between);
                        acc.after   = acc.after.concat(nsHandlers[name].after);
                        return acc;
                    }, getHandlerObject());

                    if(mergedHandlers){

                        //if there is something in before we delay the execution
                        if(mergedHandlers.before.length){
                            createAsyncCallstack(mergedHandlers.before, self, args, _.partial(triggerEvent, mergedHandlers));
                        } else {
                            triggerEvent(mergedHandlers);
                        }
                    }
                });

                /**
                 * Execute the given event handlers (between and then after)
                 *
                 * @private
                 * @param {Object} handlers - the event handler object to execute
                 */
                function triggerEvent(handlers){
                    //trigger the event handlers
                    _.forEach(handlers.between, function(handler){
                        handler.apply(self, args);
                    });

                    //trigger the after event handlers if applicable
                    _.forEach(handlers.after, function(handler){
                        handler.apply(self, args);
                    });
                }

                return this;
            },

            /**
            * Register a callback that is executed before the given event name
            * Provides an opportunity to cancel the execution of the event if one of the returned value is false
            *
            * @this the target
            * @param {String} eventName
            * @returns {Object} the target object
            */
            before : function before(eventNames, handler){
                if(_.isFunction(handler)) {
                    _.forEach(getEventNames(eventNames), function(eventName){
                        getHandlers(eventName, 'before').push(handler);
                    });
                }
                return this;
            },

            /**
            * Register a callback that is executed after the given event name
            * The handlers will all be executed, no matter what
            *
            * @this the target
            * @param {String} eventName
            * @returns {Object} the target object
            */
            after : function after(eventNames, handler){
                if(_.isFunction(handler)) {
                    _.forEach(getEventNames(eventNames), function(eventName){
                        getHandlers(eventName, 'after').push(handler);
                    });
                }
                return this;
            }
        };

        target = target || {};

        if(logger){
            //try to get something that looks like a name, an id or generate one only for logging purposes
            targetName = target.name || target.id || target.serial || uuid(6);
        }

        _(eventApi).functions().forEach(function(method){
            target[method] = function delegate(){
                var args =  [].slice.call(arguments);
                if(logger && logger.debug){
                    logger.debug.apply(logger, [targetName, method].concat(args));
                }
                return eventApi[method].apply(target, args);
            };
        });

        return target;
    }

    return eventifier;
});

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
 *
 * Logger API, highly inspired from https://github.com/trentm/node-bunyan
 *
 * TODO sprintf like messages
 * TODO
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['lodash'], function(_){
    'use strict';

    var defaultLevel = 'info';

    var levels = {
        fatal : 60, // The service/app is going to stop or become unusable now. An operator should definitely look into this soon.
        error : 50, // Fatal for a particular request, but the service/app continues servicing other requests. An operator should look at this soon(ish).
        warn  : 40, // A note on something that should probably be looked at by an operator eventually.
        info  : 30, // Detail on regular operation.
        debug : 20, // Anything else, i.e. too verbose to be included in "info" level.
        trace : 10  // Logging from external libraries used by your app or very detailed application logging.
    };

    var logQueue = [];

    /**
     * Creates a logger instance
     * @param {loggerProvider} provider - the logger provider
     * @param {String} [context] - add a context in all logged messages
     * @returns {logger} a new logger instance
     */
    var loggerFactory = function loggerFactory(context){


        /**
         * Exposes a log method and one by log level, like logger.trace()
         *
         * @typedef logger
         */
        var logger = {

            /**
             * Log messages by delegating to the provider
             *
             * @param {String|Number} [level] - the log level
             * @param {...String} messages - the messages
             * @returns {logger} chains
             */
            log : function log(level){
                var messages;
                var stack;
                var time = Date.now();

                //extract arguments : optional level and messages
                if(_.isString(level) && !_.isNumber(levels[level])){
                   messages = [].slice.call(arguments);
                   level = defaultLevel;
                }
                if(_.isNumber(level)){
                    level = _.findKey(levels, function(l){
                        return l === level;
                    }) || defaultLevel;
                }

                if(!messages){
                   messages = [].slice.call(arguments, 1);
                }

                if(levels[level] >= levels.error){
                    stack = new Error().stack || 'no stack infos';
                }

                //push the message to the queue
                logQueue.push({
                    time     : time,
                    level    : level,
                    messages : messages,
                    context  : context,
                    stack    : stack
                });

               this.flush();

                return this;
            },

            /**
             * Flush the message queue if there's at least on provider
             * @returns {logger} chains
             */
            flush : function flush(){
                if(loggerFactory.providers && loggerFactory.providers.length){
                    _.forEach(logQueue, function(message){
                        //forward to the providers
                        _.forEach(loggerFactory.providers, function(provider){
                            provider.log.call(provider, message);
                        });
                    });
                    //clear the queue
                    logQueue = [];
                }
                return this;
            }
        };

        //augment the logger by each level
        return _.reduce(levels, function reduceLogLevel(target, level, levelName){
            target[levelName] = _.partial(logger.log, level);
            return target;
        }, logger);
    };

    /**
     * A logger provider provides with a way to log
     * @typedef {Object} loggerProvider
     * @property {Function} log - called with the message in parameter
     * @throws TypeError
     */
    loggerFactory.register = function register(provider){

        if(!_.isPlainObject(provider) || !_.isFunction(provider.log)){
            throw new TypeError('A log provider is an object with a log method');
        }
        this.providers = this.providers || [];
        this.providers.push(provider);
    };

    return loggerFactory;
});

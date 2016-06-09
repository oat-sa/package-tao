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
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'lodash'], function($, _){
    'use strict';

    /**
     *
     * Builds a brand new {@link Scorer}.
     *
     * <strong>The factory is an internal mechanism to create encapsulated contexts.
     *  I suggest you to use directly the name <i>scorer</i> when you require this module.</strong>
     *
     * @example require(['scorer'], function(scorer){
     *      scorer(options)
     *          .on('error', function(err){
     *
     *          })
     *          .on('outcome', function(outcome){
     *
     *          })
     *          .process(responses, responseProcessing);
     *  });
     *
     * @exports scorer
     * @namespace scorerFactory
     *
     * @param {String} [providerName] - the name of a provider previously registered see {@link scorerFactory#register}
     * @param {Object} [options] - scorer options
     *
     * @returns {Scorer}
     */
    var scorerFactory = function scorerFactory(providerName, options){

        //optional params based on type
        if(_.isPlainObject(providerName)){
            options = providerName;
            providerName = undefined;
        }
        options = options || {};

        //contains the bound events.
        var events = {};

        /*
         * Select the provider
         */

        var provider;
        var providers = scorerFactory.providers;

        //check a provider is available
        if(!providers || _.size(providers) === 0){
            throw new Error('No provider regitered');
        }

        if(_.isString(providerName) && providerName.length > 0){
            provider = providers[providerName];
        } else if(_.size(providers) === 1) {

            //if there is only one provider, then we take this one
            providerName = _.keys(providers)[0];
            provider = providers[providerName];
        }

        //now we should have a provider
        if(!provider){
            throw new Error('No candidate found for the provider');
        }


       /**
        * The Scorer
        * @typedef {Object} Scorer
        */

        /**
        * @type {Scorer}
        * @lends scorerFactory
        */
        var Scorer = {

           /**
            * Process the response
            * @param {Object[]} responses - the responses to score
            * @param {Object} processingData - all the data needed to grade/process the response
            * @fires Scorer#outcome
            * @fires Scorer#error
            */
           process : function(responses, processingData){
                var self = this;
                if(_.isFunction(provider.process)){

                    /**
                     * Calls the provider's process
                     * @callback ProcessScoringProvider
                     * @param {Object[]} responses - the responses to score
                     * @param {Object} processingData - all the data needed to grade/process the response
                     * @param {Function} done - call once the render is done
                     */
                    provider.process.call(this, responses, processingData, function proceed(outcome){
                        if(!_.isPlainObject(outcome)){
                            return self.trigger('error', 'The given outcome is not formated correctly. An object is expected but a ' + (typeof outcome) + ' given');
                        }
                        self.trigger('outcome', outcome);
                    });
                }
           },

           /**
            * Attach an event handler.
            * Calling `on` with the same eventName multiple times add callbacks: they
            * will all be executed.
            *
            * @example scorer()
            *               .on('outcome', function(outcome){
            *
            *               });
            *
            * @param {String} name - the name of the event to listen
            * @param {Function} handler - the callback to run once the event is triggered. It's executed with the current scorer context (ie. this
            * @returns {Scorer}
            */
            on : function(name, handler){
                if(_.isString(name) && _.isFunction(handler)){
                    events[name] = events[name] || [];
                    events[name].push(handler);
                }
                return this;
            },

            /**
            * Remove handlers for an event.
            *
            * @example scorer().off('outcome');
            *
            * @param {String} name - the event name
            * @returns {Scorer}
            */
            off : function(name){
                if(_.isString(name)){
                    events[name] = [];
                }
                return this;
            },

            /**
            * Trigger an event manually.
            *
            * @example scorer().trigger('outcome', {SCORE : 12});
            *
            * @param {String} name - the name of the event to trigger
            * @param {*} data - arguments given to the handlers
            * @returns {Scorer}
            */
            trigger : function(name, data){
                var self = this;
                if(_.isString(name) && _.isArray(events[name])){
                    _.forEach(events[name], function(event){
                        event.call(self, data);
                    });
                }
                return this;
            }
        };

        return Scorer;
    };

    /**
     * Register an <i>Scoring Provider</i> into the scorer.
     * The provider provides the behavior required by the scorer.
     *
     * @param {String} name - the provider name will be used to select the provider.
     *
     * @param {Object} provider - the Scoring Provier as a plain object. The scorer forwards encapsulate and delegate calls to the provider.
     * @param {ProcessScoringProvider} provider.process - how to process the responses to create an outcome
     *
     * @throws TypeError when a wrong provider is given or an empty name.
     */
    scorerFactory.register = function registerProvider(name, provider){
        //type checking
        if(!_.isString(name) || name.length <= 0){
            throw new TypeError('It is required to give a name to your provider.');
        }
        if(!_.isPlainObject(provider) || !_.isFunction(provider.process)){
            throw new TypeError('A provider is an object that contains a process function.');
        }

        this.providers = this.providers || {};
        this.providers[name] = provider;
    };

    return scorerFactory;
});


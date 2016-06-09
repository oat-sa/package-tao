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
define([
    'jquery',
    'lodash',
    'core/eventifier',
    'taoItems/assets/manager'
], function($, _, eventifier, assetManagerFactory){
    'use strict';

     /**
     *
     * Builds a brand new {@link itemRunner}.
     *
     * <strong>The factory is an internal mechanism to create encapsulated contexts.
     *  I suggest you to use directly the name <i>itemRunner</i> when you require this module.</strong>
     *
     * @example require(['itemRunner'], function(itemRunner){
                    itemRunner({itemId : 12})
     *                    .on('statechange', function(state){
     *
     *                    })
     *                    .on('ready', function(){
     *
     *                    })
     *                    .on('response', function(){
     *
     *                    })
     *                   .init()
     *                   .render($('.item-container'));
     *          });
     *
     * @exports itemRunner
     * @namespace itemRunnerFactory
     *
     * @param {String} [providerName] - the name of a provider previously registered see {@link itemRunnerFactory#register}
     * @param {Object} [data] - the data of the item to run
     *
     * @returns {itemRunner}
     */
    var itemRunnerFactory = function itemRunnerFactory(providerName, data, options){

        var provider,
            providers,
            assetManager;

        //contains the bound events.
        var events = {};

        //flow structure to manage sync calls in an async context.
        var flow = {
            init : {
                done: false,
                pending : []
            },
            render : {
                done: false,
                pending : []
            }
        };

        //optional params based on type
        if(_.isPlainObject(providerName)){
            data = providerName;
            providerName = undefined;
        }

        data = data || {};
        options = options || {};

        /*
         * Select the provider
         */
        providers = itemRunnerFactory.providers;

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

        //set up a default assetManager using a "do nothing" strategy
        assetManager = options.assetManager || assetManagerFactory(function defaultStrategy(url){
            return url.toString();
        });


       /**
        * The itemRunner
        * @typedef {Object} itemRunner
        */

        /**
        * @type {itemRunner}
        * @lends itemRunnerFactory
        */
        return eventifier({

            /**
             * Items container
             * @type {HTMLElement}
             */
            container : null,

            /**
             * The asset manager used to resolve asset
             * @see taoItems/asset/manager
             * @type {AssetManager}
             */
            assetManager : assetManager,

            /**
             * To give options to the item runner provider
             * @type {Object}
             */
            options : options,

            /**
             * Initialize the runner.
             * @param {Object} [newData] - just in case you want to change item data (it should not occurs in most case)
             * @returns {itemRunner} to chain calls
             *
             * @fires itemRunner#init
             */
            init : function(newData){
                var self = this;

                /**
                 * Call back when init is done
                 */
                var initDone = function initDone(){

                   //manage pending tasks the first time
                    if(flow.init.done === false){
                        flow.init.done = true;

                        _.forEach(flow.init.pending, function(pendingTask){
                            if(_.isFunction(pendingTask)){
                                pendingTask.call(self);
                            }
                        });
                        flow.init.pending = [];
                    }

                    /**
                     * the runner has initialized correclty the item
                     * @event itemRunner#init
                     */
                    self.trigger('init');
                };

                //merge data
                if(newData){
                    data = _.merge(data, newData);
                }

                if(_.isFunction(provider.init)){

                    /**
                     * Calls provider's initialization with item data.
                     * @callback InitItemProvider
                     * @param {Object} data - the item data
                     * @param {Function} done - call once the initialization is done
                     */
                    provider.init.call(this, data, initDone);

                } else {
                    initDone();
                }

                return this;
            },

            /**
             * Configure the assetManager
             * @see taoItems/assets/manager
             * @param {AssetStrategy[]} strategies - the resolving strategies
             * @param {Object} [data] - the context data
             * @param {Object} [options] - the asset manager options
             * @returns {itemRunner} to chain calls
             */
            assets : function assets(strategies, data, options){

                try{
                    this.assetManager = assetManagerFactory(strategies, data, options);
                } catch( err ){
                    this.trigger('error', 'Something was wrong while configuring the asset manager : ' + err);
                }

                return this;
            },

            /**
             * Initialize the current item.
             *
             * @param {HTMLElement|jQueryElement} elt - the DOM element that is going to contain the rendered item.
             * @returns {itemRunner} to chain calls
             *
             * @fires itemRunner#ready
             * @fires itemRunner#render
             * @fires itemRunner#error if the elt isn't valid
             *
             * @fires itemRunner#statechange the provider is reponsible to trigger this event
             * @fires itemRunner#responsechange  the provider is reponsible to trigger this event
             */
           render : function(elt){
                var self = this;

                /**
                 * Call back when render is done
                 */
                var renderDone = function renderDone (){

                    //manage pending tasks the first time
                    if(flow.render.done === false){
                        flow.render.done = true;

                        _.forEach(flow.render.pending, function(pendingTask){
                            if(_.isFunction(pendingTask)){
                                pendingTask.call(self);
                            }
                        });
                        flow.render.pending = [];
                    }

                    /**
                     * The item is rendered
                     * @event itemRunner#render
                     */
                    self.trigger('render');

                    /**
                     * The item is ready.
                     * Alias of {@link itemRunner#render}
                     * @event itemRunner#ready
                     */
                    self.trigger('ready');
                };

                //check elt
                if( !(elt instanceof HTMLElement) && !(elt instanceof $) ){
                    return self.trigger('error', 'A valid HTMLElement (or a jquery element) at least is required to render the item');
                }

                if(flow.init.done === false){
                    flow.init.pending.push(function(){
                        this.render(elt);
                    });
                } else {

                    //we keep a reference to the container
                    if(elt instanceof $){
                        this.container = elt.get(0);
                    } else {
                        this.container = elt;
                    }

                    //the state will be applied only when the rendering is made

                    if(_.isFunction(provider.render)){

                        /**
                         * Calls the provider's render
                         * @callback RendertItemProvider
                         * @param {HTMLElement} elt - the element to render inside
                         * @param {Function} done - call once the render is done
                         */
                        provider.render.call(this, this.container, renderDone);

                    } else {
                        renderDone();
                    }
                }

                return this;
           },

           /**
            * Clear the running item.
            * @returns {itemRunner}
            *
            * @fires itemRunner#clear
            */
           clear : function(){
                var self = this;

                /**
                 * Call back when clear is done
                 */
                var clearDone = function clearDone (){

                    //events = {};

                    /**
                     * The item is ready.
                     * @event itemRunner#clear
                     */
                    self.trigger('clear');
                };
                if(_.isFunction(provider.clear)){

                    /**
                     * Calls the provider's clear
                     * @callback ClearItemProvider
                     * @param {HTMLElement} elt - item's container
                     * @param {Function} done - call once the initialization is done
                     */
                    provider.clear.call(this, this.container, clearDone);

                } else {
                    clearDone();
                }

                return this;
           },

           /**
            * Get the current state of the running item.
            *
            * @returns {Object|Null} state
            */
           getState : function(){
                var state = null;
                if(_.isFunction(provider.getState)){

                    /**
                     * Calls the provider's getState
                     * @callback GetStateItemProvider
                     * @returns {Object} the state
                     */
                    state = provider.getState.call(this);
                }
                return state;
           },

           /**
            * Set the current state of the running item.
            * This should have the effect to restore the item state.
            *
            * @param {Object} state - the new state
            * @returns {itemRunner}
            *
            * @fires itemRunner#error if the state type doesn't match
            */
           setState : function(state){

                if(!_.isPlainObject(state)){
                    return this.trigger('error', "The item's state must be a JavaScript Plain Object: " + (typeof state) + ' given');
                }

                //the state will be applied only when the rendering is made
                if(flow.render.done === false){
                    flow.render.pending.push(function(){
                        this.setState(state);
                    });
                } else {

                    if(_.isFunction(provider.setState)){

                        /**
                         * Calls the provider's setState
                         * @callback SetStateItemProvider
                         * @param {Object} state -  the state to set
                         */
                        provider.setState.call(this, state);
                    }
                }
                return this;
           },

           /**
            * Get the responses of the running item.
            *
            * @returns {Object} the item's responses
            */
           getResponses : function(){
                var responses = {};
                if(_.isFunction(provider.getResponses)){

                    /**
                     * Calls the provider's getResponses
                     * @callback GetResponsesItemProvider
                     * @returns {Object} the responses
                     */
                    responses = provider.getResponses.call(this);
                }
                return responses;
           }
        });
    };

    /**
     * Register an <i>Item Runtime Provider</i> into the item runner.
     * The provider provides the behavior required by the item runner.
     *
     * @param {String} name - the provider name will be used to select the provider while instantiating the runner
     *
     * @param {Object} provider - the Item Runtime Provider as a plain object. The itemRunner forwards encapsulate and delegate calls to the provider.
     * @param {InitItemProvider} provider.init - the provider initializes the item from it's data, for example loading libraries, add some listeners, etc.
     * @param {RenderItemProvider} provider.render - the provider renders the item within the given container element.
     * @param {ClearItemProvider} [provider.clear] - the provider clears the item.
     * @param {GetStateItemProvider} [provider.getState] - the provider get the item's state.
     * @param {SetStateItemProvider} [provider.setState] - the provider restore the item to the given state.
     * @param {GetRespnsesItemProvider} [provider.getResponses] - the provider gives the current responses.
     *
     * @throws TypeError when a wrong provider is given or an empty name.
     */
    itemRunnerFactory.register = function registerProvider(name, provider){
        //type checking
        if(!_.isString(name) || name.length <= 0){
            throw new TypeError('It is required to give a name to your provider.');
        }
        if(!_.isPlainObject(provider) || (!_.isFunction(provider.init) && !_.isFunction(provider.render))){
            throw new TypeError('A provider is an object that contains at least an init function or a render function.');
        }

        this.providers = this.providers || {};
        this.providers[name] = provider;
    };

    return itemRunnerFactory;
});

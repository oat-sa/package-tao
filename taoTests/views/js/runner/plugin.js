/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 *
 * Runner plugin
 *
 * TODO usage example
 *
 * @author Sam <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/promise'
], function (_, Promise){
    'use strict';

    /**
     * Meta factory for plugins. Let's you create a plugin definition.
     *
     * @param {Object} provider - the list of implemented methods
     * @param {String} provider.name - the plugin name
     * @param {Function} provider.init - the plugin initialization method
     * @param {Function} [provider.render] - plugin render behaviorV
     * @param {Function} [provider.finish] - plugin render behaviorV
     * @param {Function} [provider.destroy] - plugin destroy behavior
     * @param {Function} [provider.show] - plugin show behavior
     * @param {Function} [provider.hide] - plugin hide behavior
     * @param {Function} [provider.enable] - plugin enable behavior
     * @param {Function} [provider.disable] - plugin disable behavior
     * @param {Object} defaults - default configuration to be assigned
     * @returns {Function} - the generated plugin factory
     */
    function pluginFactory(provider, defaults){
        var pluginName;

        if(!_.isPlainObject(provider) || !_.isString(provider.name) || _.isEmpty(provider.name) || !_.isFunction(provider.init)){
            throw new TypeError('A plugin should be defined at least by a name property and an init method');
        }

        //TODO verify the name isn't already in use
        pluginName = provider.name;

        defaults = defaults || {};


        /**
         * The configured plugin factory
         *
         * @param {testRunner} runner - a test runner instance
         * @param {areaBroker} areaBroker - an instance of an areaBrokee
         * @param {Object} [config] - plugin configuration
         * @returns {plugin} the plugin instance
         */
        return function instanciatePlugin(runner, areaBroker, config){
            var plugin;

            var states = {};

            /**
             * Delegate a function call to the provider
             *
             * @param {String} fnName - the function name
             * @param {...} args - additional args are given to the provider
             * @returns {*} up to the provider
             */
            function delegate(fnName){
                var args = [].slice.call(arguments, 1);
                return new Promise(function(resolve){
                    if(!_.isFunction(provider[fnName])){
                        return resolve();
                    }
                    return resolve(provider[fnName].apply(plugin, args));
                });
            }


            config = _.defaults(config || {}, defaults);

            /**
             * The plugin instance.
             * @typedef {plugin}
             */
            plugin = {

                /**
                 * Called when the testRunner is initializing
                 * @returns {Promise} to resolve async delegation
                 */
                init : function init(){
                    var self = this;
                    states = {};

                    return delegate('init').then(function(){
                        self.setState('init', true)
                            .trigger('init');
                    });
                },

                /**
                 * Called when the testRunner is rendering
                 * @returns {Promise} to resolve async delegation
                 */
                render : function render(){
                    var self = this;

                    return delegate('render').then(function(){
                        self.setState('ready', true)
                            .trigger('render')
                            .trigger('ready');
                    });
                },

                /**
                 * Called when the testRunner is finishing
                 * @returns {Promise} to resolve async delegation
                 */
                finish : function finish(){
                    var self = this;

                    return delegate('finish').then(function(){
                        self.setState('finish', true)
                            .trigger('finish');
                    });
                },

                /**
                 * Called when the testRunner is destroying
                 * @returns {Promise} to resolve async delegation
                 */
                destroy : function destroy(){
                    var self = this;

                    return delegate('destroy').then(function(){

                        config = {};
                        states = {};

                        self.setState('init', false);
                        self.trigger('destroy');
                    });
                },

                /**
                 * Triggers the events on the test runner using the pluginName as namespace
                 * and prefixed by plugin-
                 * For example trigger('foo') will trigger('plugin-foo.pluginA') on the runner.
                 *
                 * @param {String} name - the event name
                 * @param {...} args - additional args are given to the event
                 * @returns {plugin} chains
                 */
                trigger : function trigger(name){
                    var args = [].slice.call(arguments, 1);
                    runner.trigger.apply(runner, ['plugin-' + name + '.' + pluginName, plugin].concat(args));
                    return this;
                },

                /**
                 * Get the test runner
                 * @returns {testRunner} the plugins's testRunner
                 */
                getTestRunner : function getTestRunner(){
                    return runner;
                },

                /**
                 * Get the test runner
                 * @returns {testRunner} the plugins's testRunner
                 */
                getAreaBroker : function getAreaBroker(){
                    return areaBroker;
                },

                /**
                 * Get the config
                 * @returns {Object} config
                 */
                getConfig : function getConfig(){
                    return config;
                },

                /**
                 * Set a config entry
                 * @param {String|Object} name - the entry name or an object to merge
                 * @param {*} [value] - the config value if name is an entry
                 * @returns {plugin} chains
                 */
                setConfig : function setConfig(name, value){
                    if(_.isPlainObject(name)){
                        config = _.defaults(name, config);
                    }else{
                        config[name] = value;
                    }
                    return this;
                },

                /**
                 * Get a state of the plugin
                 *
                 * @param {String} name - the state name
                 * @returns {Boolean} if active, false if not set
                 */
                getState : function getState(name){
                    return !!states[name];
                },

                /**
                 * Set a state to the plugin
                 *
                 * @param {String} name - the state name
                 * @param {Boolean} active - is the state active
                 * @returns {plugin} chains
                 * @throws {TypeError} if the state name is not a valid string
                 */
                setState : function setState(name, active){
                    if(!_.isString(name) || _.isEmpty(name)){
                        throw new TypeError('The state must have a name');
                    }
                    states[name] = !!active;

                    return this;
                },

                /**
                 * Get the plugin name
                 *
                 * @returns {String} the name
                 */
                getName : function getName(){
                    return pluginName;
                },

                /**
                 * Shows the component related to this plugin
                 * @returns {Promise} to resolve async delegation
                 */
                show : function show(){
                    var self = this;

                    return delegate('show').then(function(){
                        self.setState('visible', true)
                            .trigger('show');
                    });
                },

                /**
                 * Hides the component related to this plugin
                 * @returns {Promise} to resolve async delegation
                 */
                hide : function hide(){
                    var self = this;

                    return delegate('hide').then(function(){
                        self.setState('visible', false)
                            .trigger('hide');
                    });
                },

                /**
                 * Enables the plugin
                 * @returns {Promise} to resolve async delegation
                 */
                enable : function enable(){
                    var self = this;

                    return delegate('enable').then(function(){
                        self.setState('enabled', true)
                            .trigger('enable');
                    });
                },

                /**
                 * Disables the plugin
                 * @returns {Promise} to resolve async delegation
                 */
                disable : function disable(){
                    var self = this;

                    return delegate('disable').then(function(){
                        self.setState('enabled', false)
                            .trigger('disable');
                    });
                }
            };

            return plugin;
        };
    }

    return pluginFactory;
});

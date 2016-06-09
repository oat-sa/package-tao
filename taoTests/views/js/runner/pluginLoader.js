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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */

/**
 * Loads the test runner plugins.
 * It provides 2 distinct way of loading plugins :
 *  1. The "required" plugins that are necessary to the runner, they are provided as plugins.
 *  2. The "dynamic" plugins that are loaded on demand, they are provided as AMD modules. The module is loaded using the load method.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/promise'
], function(_, Promise) {
    'use strict';

    /**
     * Creates a loader with the list of required plugins
     * @param {String: Function[]} requiredPlugins - where the key is the category and the value are an array of plugins
     * @returns {loader} the plugin loader
     * @throws TypeError if something is not well formated
     */
    return function pluginLoader(requiredPlugins) {

        var plugins = {};
        var modules = {};

        /**
         * The plugin loader
         * @typedef {loader}
         */
        var loader = {

            /**
             * Add a new dynamic plugin
             * @param {String} module - AMD module name of the plugin
             * @param {String} category - the plugin category
             * @param {String|Number} [position = 'append'] - append, prepend or plugin position within the category
             * @returns {loader} chains
             * @throws {TypeError} misuse
             */
            add: function add(module, category, position) {
                if(!_.isString(module)){
                    throw new TypeError('An AMD module must be defined');
                }
                if(!_.isString(category)){
                    throw new TypeError('Plugins must belong to a category');
                }

                modules[category] = modules[category] || [];

                if(_.isNumber(position)){
                    modules[category][position] = module;
                }
                else if(position === 'prepend' || position === 'before'){
                    modules[category].unshift(module);
                } else {
                    modules[category].push(module);
                }

                return this;
            },

            /**
             * Append a new dynamic plugin to a category
             * @param {String} module - AMD module name of the plugin
             * @param {String} category - the plugin category
             * @returns {loader} chains
             * @throws {TypeError} misuse
             */
            append: function append(module, category) {
                return this.add(module, category);
            },

            /**
             * Prepend a new dynamic plugin to a category
             * @param {String} module - AMD module name of the plugin
             * @param {String} category - the plugin category
             * @returns {loader} chains
             * @throws {TypeError} misuse
             */
            prepend: function prepend(module, category) {
                return this.add(module, category, 'before');
            },

            /**
             * Loads the dynamic plugins : trigger the dependency resolution
             * @returns {Promise}
             */
            load: function load() {
                return new Promise(function(resolve, reject){

                    var dependencies = _(modules).values().flatten().uniq().value();

                    if(dependencies.length){

                        require(dependencies, function(){
                            var loadedModules = [].slice.call(arguments);
                            _.forEach(dependencies, function(dependency, index){
                                var plugin = loadedModules[index];
                                var category = _.findKey(modules, function(val){
                                    return _.contains(val, dependency);
                                });
                                if(_.isFunction(plugin) && _.isString(category)){
                                    plugins[category] = plugins[category] || [];
                                    plugins[category].push(plugin);
                                }
                            });
                            resolve();

                        }, reject);
                        return;
                    }
                    resolve();
                });
            },

            /**
             * Get the resolved plugin list.
             * Load needs to be called before to have the dynamic plugins.
             * @param {String} [category] - to get the plugins for a given category, if not set, we get everything
             * @returns {Function[]} the plugins
             */
            getPlugins: function getPlugins(category) {
                if(_.isString(category)){
                    return plugins[category] || [];
                }
                return _(plugins).values().flatten().uniq().value();
            },

            /**
             * Get the plugin categories
             * @returns {String[]} the categories
             */
            getCategories: function getCategories(){
                return _.keys(plugins);
            }
        };

        //verify and add the required plugins
        _.forEach(requiredPlugins, function(pluginList, category){
            if(!_.isString(category)){
                throw new TypeError('Plugins must belong to a category');
            }

            if(!_.isArray(pluginList) || !_.all(pluginList, _.isFunction)){
                throw new TypeError('A plugin must be an array of function');
            }

            if(plugins[category]){
                plugins[category] = plugins[category].concat(pluginList);
            } else {
                plugins[category] = pluginList;
            }
        });

        return loader;
    };
});

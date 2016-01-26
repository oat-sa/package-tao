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
 * The asset manager proposes you to resolve your asset URLs for you!
 * You need to add the resolution strategies, it will then evaluate each strategy until the right one is found
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'util/url'
], function(_, urlUtil){
    'use strict';

    /**
     * @typedef AssetStrategy Defines a way to resolve an asset path
     * @property {String} name - the strategy name
     * @property {assetStrategyHandle} handle - how to resolve the strategy.
     */

    /**
     * @callback assetStrategyHandle
     * @param {String|Object} url - the URL to resolved. If parseUrl, it's an object that contains host, port, search, queryString, etc.
     * @param {Object} data - the context data
     * @returns {String?} falsy if not resolved otherwise the resolved URL
     */


    /**
     * The assetManagerFactory create a new assetManager with the given resolution stratgies and a data context.
     *
     * @example
     *   //define AssetStrategies with a name and a handle method
     *   var strategies = [{
     *       name : 'external',
     *       handle : function(url, data){
     *           if(/^http/.test(url)){
     *               return path;
     *           }
     *       }
     *   }, {
     *       name : 'relative',
     *       handle : function(url, data){
     *           if(/^((\.\/)|(\w\/))/){
     *               return data.baseUrl + '/' + url ;
     *           }
     *       }
     *   }]);
     *
     *   var assetManager = assetManagerFactory(strategies, { baseUrl : 'http://t.oa/public/assets/' });
     *   assetManager.resolve('http://foo/bar.png'); //will resolved using external
     *   assetManager.resolve('bar.png'); //will resolved using relative strategy
     *
     * @param {AssetStrategy[]} strategies - the strategies
     * @param {Object} data - the context data
     * @param {Object} [options] - the manager options
     * @param {Boolean} [options.parseUrl = true] - If the URL to give to the stragies should be parsed or given as it is.
     * @param {Boolean} [options.cache] - resolve the same URL only once and store the result in memory.
     *
     * @exports taoItems/assets/manager
     * @namespace assetManagerFactory
     */
    var assetManagerFactory = function assetManagerFactory(strategies, data, options) {

        var cache  = {};

        strategies = _.isArray(strategies) ? strategies : [strategies];
        data       = data || {};
        options    = _.defaults(options || {}, {
            parseUrl : true
        });

        /**
         * A brand new asset manager is created by the factory
         */
        var assetManager = {

            /**
             * The stack of strategies that would be used to resolve the asset path
             * @type {AssetStrategy[]}
             */
            _strategies : [],

            /**
             * Add an asset resolution strategy.
             * The strategies will be evaluated in the order they've been added.
             * @param {AssetStrategy} strategy - the strategy to add
             * @throws {TypeError} if the strategy isn't defined correctly
             */
            addStrategy : function addStrategy (strategy){

                if(!_.isPlainObject(strategy) || !_.isFunction(strategy.handle) || !_.isString(strategy.name)){
                    throw new TypeError('An asset resolution strategy is an object with a handle method and a name');
                }

                this._strategies.push(strategy);
            },

            /**
             * Change the strategies
             * @param {AssetStrategy[]} strategies - the strategies
             * @throws {TypeError} if the strategy isn't defined correctly
             */
            setStrategies : function setStrategies(newStrategies){
                var self = this;

                this._strategies = [];

                //assign the strategies to the assetManager
                _.forEach(newStrategies, function(strategy){

                    //if it's an object we add it directly
                    if(_.isPlainObject(strategy)){
                        assetManager.addStrategy(strategy);

                    //if it's a function, we create the strategy with a generated name
                    } else if(_.isFunction(strategy)){
                        self.addStrategy({
                            name   : 'strategy_' + (self._strategies.length + 1),
                            handle : strategy
                        });
                    }
                });
            },

            /**
             * Set context data
             * @param {String|Object} [key] - the key of the data to set or the data values if it's an object
             * @param {*} [value] - the value to set if a key is given
             */
            setData : function setData(key, value){
                if(_.isString(key) && typeof value !== 'undefined'){
                    data[key] = value;
                } else if(_.isPlainObject(key)){
                    data = key;
                }
            },

            /**
             * Get context data
             * @param {String} [key] - if we want the value of a particuar key
             * @returns {Object|*} all the data or the proprety value if key is given
             */
            getData : function getData(key){
                if(_.isString(key)){
                    return data[key];
                }
                return data;
            },

            /**
             * Resolve the given URL against the strategies
             * @param {String} url - the URL to resolve
             * @returns {String?} the resolved URL or nothing
             */
            resolve : function resolve(url){
                var resolved;
                var inputUrl;

                //if caching try to load the value from the cache
                if(options.cache && cache.hasOwnProperty(url)){
                    return cache[url];
                }

                //parse the url ?
                inputUrl = options.parseUrl ? urlUtil.parse(url) : url;

                //call strategies handlers, in their order until once returns somethin
                _.forEach(this._strategies, function(strategy){
                    var result = strategy.handle(inputUrl, data);
                    if(result){
                        resolved = result;
                        return false;
                    }
                });

                resolved = resolved || '';

                if(options.cache){
                    cache[url] = resolved;
                }

                return resolved;
            },

            /**
             * Resolve the given URL against the strategy identified by the given name
             * @param {String} name - the strategy name
             * @param {String} url - the URL to resolve
             * @returns {String?} the resolved URL or nothing
             */
            resolveBy : function resolveBy(name, url){
                var inputUrl;
                var resolved = '';
                var strategy = _.find(this._strategies, {name : name});
                if(strategy){
                    //parse the url ?
                    inputUrl = options.parseUrl ? urlUtil.parse(url) : url;
                    resolved = strategy.handle(inputUrl, data);
                }
                return resolved;
            },

            /**
             * When the cache is used, it could be useful to clear the cache
             */
            clearCache : function clearCache(){
                if(options.cache){
                    cache = {};
                }
            }
        };

        assetManager.setStrategies(strategies);

        return assetManager;
    };

    return assetManagerFactory;
});

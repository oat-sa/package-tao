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
 * LocalStorage backend of the client store
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['lodash', 'core/promise'], function(_, Promise){
    'use strict';

    /**
     * Prefix all databases
     */
    var prefix = 'tao-store-';
    var storage = window.localStorage;

    /**
     * Open and access a store
     * @param {String} storeName - the store name to open
     * @returns {Object} the store backend
     * @throws {TypeError} without a storeName
     */
    var localStorageBackend = function localStorageBackend(storeName){

        var name;
        if(_.isEmpty(storeName) || !_.isString(storeName)){
            throw new TypeError('The store name is required');
        }

        //prefix all storage entries to avoid global keys confusion
        name = prefix + storeName + '.';

        /**
         * The store
         */
        return {

            /**
             * Get an item with the given key
             * @param {String} key
             * @returns {Promise} with the result in resolve, undefined if nothing
             */
            getItem : function getItem(key){
                return new Promise(function(resolve, reject){
                    var value;
                    try{
                        value = storage.getItem(name + key);
                        if(value === null){
                            resolve();
                        } else {
                            resolve(JSON.parse(value));
                        }
                    } catch(ex){
                        reject(ex);
                    }
                });
            },

            /**
             * Set an item with the given key
             * @param {String} key - the item key
             * @param {*} value - the item value
             * @returns {Promise} with true in resolve if added/updated
             */
            setItem :  function setItem(key, value){
                return new Promise(function(resolve, reject){
                    try{
                        storage.setItem(name + key, JSON.stringify(value));
                        resolve(true);
                    } catch(ex){
                        reject(ex);
                    }
                });
            },

            /**
             * Remove an item with the given key
             * @param {String} key - the item key
             * @returns {Promise} with true in resolve if removed
             */
            removeItem : function removeItem(key){
                return new Promise(function(resolve, reject){
                    try{
                        storage.removeItem(name + key);
                        resolve(true);
                    } catch(ex){
                        reject(ex);
                    }
                });
            },

            /**
             * Clear the current store
             * @returns {Promise} with true in resolve once cleared
             */
            clear : function clear(){
                var keyPattern = new RegExp('^' + name);
                return new Promise(function(resolve, reject){
                    var i;
                    try{
                        _(storage)
                            .map(function(entry, index){
                                return storage.key(index);
                            })
                            .filter(function(key){
                                return keyPattern.test(key);
                            })
                            .forEach(function(key){
                                storage.removeItem(key);
                            });
                        resolve(true);
                    } catch(ex){
                        reject(ex);
                    }
                });
            }
        };
    };

    return localStorageBackend;
});

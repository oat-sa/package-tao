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
 * Browser storage, multiple backends
 *
 * @example
 *      store('foo', store.backends.indexDb);
 *         .setItem('hello', { who : 'world'))
 *         .then(function(added){
 *              //yeah!
 *         })
 *         .catch(function(err){
 *              //OOops!
 *         });
 *
 *
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/store/localstorage',
    'core/store/indexdb'
], function(_, localStorageBackend, indexDbBackend){
    'use strict';

    //does the browser have indexDB ?
    var hasIndexDb = !! (window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB );

    /**
     * Create a new store
     *
     * @param {String} storeName - the name of the store
     * @param {Function} backend - the storage
     * @returns {Storage} a Storage Like instance
     * @throws {TypeError} if the backend isn't correct
     */
    var store = function store(storeName, backend) {
        var storeInstance;
        backend = backend || store.backends.indexDb;
        if(!hasIndexDb){
            backend = store.backends.localStorage;
        }
        if(!_.isFunction(backend)){
            throw new TypeError('No backend, no storage!');
        }
        storeInstance = backend(storeName);

        if(_.some(['getItem', 'setItem', 'removeItem', 'clear'], function(method){
            return !_.isFunction(storeInstance[method]);
        })){
            throw new TypeError('The backend does not comply with the Storage interface');
        }

        return storeInstance;
    };

    /**
     * The available backends,
     * exposed.
     */
    store.backends = {
        localStorage : localStorageBackend,
        indexDb      : indexDbBackend
    };

    return store;
});

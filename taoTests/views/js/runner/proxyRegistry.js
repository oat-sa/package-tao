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
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define(['lodash'], function(_) {
    'use strict';

    /**
     * List of API a proxy must provide to be validated
     * @type {String[]}
     * @private
     */
    var _proxyApi = [
        'init',
        'destroy',
        'getTestData',
        'getTestContext',
        'getTestMap',
        'callTestAction',
        'getItem',
        'submitItem',
        'callItemAction',
        'telemetry'
    ];

    /**
     * Makes an object a test runner proxies registry
     * @param {Object} target - The object to extend
     * @param {Function} [validator] - A function to validate the proxy
     * @returns {target} - The extended object
     */
    function proxyRegistry(target, validator) {
        // list of registered proxies
        var _proxies = {};

        // define the registry
        var _registry = {
            /**
             * Registers a test runner proxy.
             * @param {String} name - The name of the proxy to be registered. This name must be unique.
             * @param {Object} proxy - The test runner proxy
             * @returns {_registry} - Provides a fluent interface
             * @throws TypeError when a wrong proxy or an empty name is given.
             */
            registerProxy : function registerProxy(name, proxy) {
                var valid;

                // type check
                if (!_.isString(name) || !name.length) {
                    throw new TypeError('It is required to give a name to your test runner proxy!');
                }
                if (!_.isPlainObject(proxy)) {
                    throw new TypeError('A test runner proxy must be an object!');
                }

                // API check
                valid = _.all(_proxyApi, function(apiName) {
                    return _.isFunction(proxy[apiName]);
                });
                if (!valid) {
                    throw new TypeError('A test runner proxy must provide all required API!');
                }

                // custom check
                if (validator && _.isFunction(validator) && !validator(proxy)) {
                    throw new TypeError('The test runner proxy is not valid!');
                }

                // all checks succeed, register
                _proxies[name] = proxy;

                return this;
            },

            /**
             * Gets a registered test runner proxy by its name
             * @param {String} name
             * @returns {*}
             * @throws Error when an empty name is given or when no proxy is found.
             */
            getProxy : function getProxy(name) {
                var proxy;

                // check for proxies
                if (!_proxies || !_.size(_proxies)) {
                    throw new Error('No test runner proxies registered');
                }

                // if no name has been provided, and only one proxy is registered, take this one
                if (!_.isString(name) || !name.length) {
                    if (_.size(_proxies) === 1) {
                        name = _.keys(_proxies)[0];
                    } else {
                        name = null;
                    }
                }

                // try to get the proxy by name
                proxy = _proxies[name];
                if (!proxy) {
                    throw new Error('No test runner proxy found with name ' + name);
                }

                return proxy;
            }

        };

        return _.assign(target || {}, _registry);
    }

    return proxyRegistry;
});

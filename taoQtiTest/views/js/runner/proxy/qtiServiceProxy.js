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
define([
    'jquery',
    'lodash',
    'i18n',
    'core/promise',
    'core/store',
    'helpers',
    'taoQtiTest/runner/config/qtiServiceConfig'
], function($, _, __, Promise, store, helpers, configFactory) {
    'use strict';

    /**
     * Proxy request function. Returns a promise
     * Applied options: asynchronous call, JSON data, no cache
     * @param {proxy} proxy
     * @param {String} url
     * @param {Object} [params]
     * @param {String} [contentType] - to force the content type
     * @param {Boolean} [noToken] - to disable the token
     * @returns {Promise}
     */
    function request(proxy, url, params, contentType, noToken) {

        //run the request Promise
        var requestPromise = function requestPromise(){
            return new Promise(function(resolve, reject) {
                var headers = {};
                var tokenHandler = proxy.getTokenHandler();
                var token;
                if (!noToken) {
                    token = tokenHandler.getToken();
                    if (token) {
                        headers['X-Auth-Token'] = token;
                    }
                }
                $.ajax({
                    url: url,
                    type: params ? 'POST' : 'GET',
                    cache: false,
                    data: params,
                    headers: headers,
                    async: true,
                    dataType: 'json',
                    contentType : contentType || undefined
                })
                .done(function(data) {
                    if (data && data.token) {
                        tokenHandler.setToken(data.token);
                    }

                    if (data && data.success) {
                        resolve(data);
                    } else {
                        reject(data);
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    var data;
                    try {
                        data = JSON.parse(jqXHR.responseText);
                    } catch (e) {
                        data = {
                            success: false,
                            code: jqXHR.status,
                            type: textStatus || 'error',
                            message: errorThrown || __('An error occurred!')
                        };
                    }

                    if (data.token) {
                        tokenHandler.setToken(data.token);
                    }

                    reject(data);
                });
            });
        };

        //no token protection, run the request
        if(noToken === true){
            return requestPromise();
        }

        //first promise, keep the ref
        if(!proxy._runningPromise){
            proxy._runningPromise = requestPromise();
            return proxy._runningPromise;
        }

        //create a wrapping promise
        return new Promise(function(resolve, reject){
            //run the current request
            var runRequest = function(){
                var p = requestPromise();
                proxy._runningPromise = p; //and keep the ref
                p.then(resolve).catch(reject);
            };

            //wait the previous to resolve or fail and run the current one
            proxy._runningPromise.then(runRequest).catch(runRequest);
        });
    }

    /**
     * QTI proxy definition
     * Related to remote services calls
     * @type {Object}
     */
    var qtiServiceProxy = {

        /**
         * Keep a reference of the last running promise to
         * ensure the tokened protected called are chained
         * @type {Promise}
         */
        _runningPromise : null,


        /**
         * Initializes the proxy
         * @param {Object} config - The config provided to the proxy factory
         * @param {String} config.testDefinition - The URI of the test
         * @param {String} config.testCompilation - The URI of the compiled delivery
         * @param {String} config.serviceCallId - The URI of the service call
         * @returns {Promise} - Returns a promise. The proxy will be fully initialized on resolve.
         *                      Any error will be provided if rejected.
         */
        init: function init(config) {
            var initConfig = config || {};

            // store config in a dedicated configStorage
            this.configStorage = configFactory(initConfig);

            // request for initialization
            return request(this, this.configStorage.getTestActionUrl('init'));
        },

        /**
         * Uninstalls the proxy
         * @returns {Promise} - Returns a promise. The proxy will be fully uninstalled on resolve.
         *                      Any error will be provided if rejected.
         */
        destroy: function destroy() {
            var self = this;
            // the method must return a promise
            return new Promise(function(resolve) {
                // no request, just a resources cleaning
                self.configStorage = null;
                self._runningPromise = null;
                resolve();
            });
        },

        /**
         * Gets the test definition data
         * @returns {Promise} - Returns a promise. The test definition data will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        getTestData: function getTestData() {
            return request(this, this.configStorage.getTestActionUrl('getTestData'));
        },

        /**
         * Gets the test context
         * @returns {Promise} - Returns a promise. The context object will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        getTestContext: function getTestContext() {
            return request(this, this.configStorage.getTestActionUrl('getTestContext'));
        },

        /**
         * Gets the test map
         * @returns {Promise} - Returns a promise. The test map object will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        getTestMap: function getTestMap() {
            return request(this, this.configStorage.getTestActionUrl('getTestMap'));
        },

        /**
         * Calls an action related to the test
         * @param {String} action - The name of the action to call
         * @param {Object} [params] - Some optional parameters to join to the call
         * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        callTestAction: function callTestAction(action, params) {
            return request(this, this.configStorage.getTestActionUrl(action), params);
        },

        /**
         * Gets an item definition by its URI, also gets its current state
         * @param {String} uri - The URI of the item to get
         * @returns {Promise} - Returns a promise. The item data will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        getItem: function getItem(uri) {
            return request(this, this.configStorage.getItemActionUrl(uri, 'getItem'));
        },

        /**
         * Submits the state and the response of a particular item
         * @param {String} uri - The URI of the item to update
         * @param {Object} state - The state to submit
         * @param {Object} response - The response object to submit
         * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        submitItem: function submitItem(uri, state, response, params) {
            var body = JSON.stringify( _.merge({
                itemState : state,
                itemResponse : response
            }, params || {}));

            return request(this, this.configStorage.getItemActionUrl(uri, 'submitItem'), body, 'application/json');
        },

        /**
         * Calls an action related to a particular item
         * @param {String} uri - The URI of the item for which call the action
         * @param {String} action - The name of the action to call
         * @param {Object} [params] - Some optional parameters to join to the call
         * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        callItemAction: function callItemAction(uri, action, params) {
            return request(this, this.configStorage.getItemActionUrl(uri, action), params);
        },

        /**
         * Sends a telemetry signal
         * @param {String} uri - The URI of the item for which sends the telemetry signal
         * @param {String} signal - The name of the signal to send
         * @param {Object} [params] - Some optional parameters to join to the signal
         * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
         *                      Any error will be provided if rejected.
         * @fires telemetry
         */
        telemetry: function telemetry(uri, signal, params) {
            return request(this, this.configStorage.getTelemetryUrl(uri, signal), params, null, true);
        }
    };

    return qtiServiceProxy;
});

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
    'lodash',
    'core/promise'
], function(_, Promise) {
    'use strict';

    /**
     * Sample proxy definition
     * @type {Object}
     */
    var sampleProxy = {
        /**
         * Initializes the proxy
         * @param {Object} config - The config provided to the proxy factory
         * @returns {Promise} - Returns a promise. The proxy will be fully initialized on resolve.
         *                      Any error will be provided if rejected.
         */
        init: function init(config) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // do initialisation
                // once the proxy has been fully initialized notify the success by resolving the promise
                resolve();

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Uninstalls the proxy
         * @returns {Promise} - Returns a promise. The proxy will be fully uninstalled on resolve.
         *                      Any error will be provided if rejected.
         */
        destroy: function destroy() {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // do uninstall actions
                // once the proxy has been fully uninstalled notify the success by resolving the promise
                resolve();

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Gets the test definition data
         * @returns {Promise} - Returns a promise. The test definition data will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        getTestData: function getTestData() {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // get the test definition data

                // once the action has been processed notify the success by resolving the promise
                resolve(/* the test definition data */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Gets the test context
         * @returns {Promise} - Returns a promise. The context object will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        getTestContext: function getTestContext() {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // get the test context object

                // once the action has been processed notify the success by resolving the promise
                resolve(/* the test context object */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Calls an action related to the test
         * @param {String} action - The name of the action to call
         * @param {Object} [params] - Some optional parameters to join to the call
         * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
         *                      Any error will be provided if rejected.
         */
        callTestAction: function callTestAction(action, params) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // call the action

                // once the action has been processed notify the success by resolving the promise
                resolve(/* the action response */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Gets an item definition by its URI, also gets its current state
         * @param {String} uri - The URI of the item to get
         * @returns {Promise} - Returns a promise. The item data will be provided on resolve.
         *                      Any error will be provided if rejected.
         * @fires getItem
         */
        getItem: function getItem(uri) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // get the definition data and the state of the item
                // once the item data is loaded provide the data by resolving the promise
                resolve(/* the item data */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Submits the state and the response of a particular item
         * @param {String} uri - The URI of the item to update
         * @param {Object} state - The state to submit
         * @param {Object} response - The response object to submit
         * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
         *                      Any error will be provided if rejected.
         * @fires submitItem
         */
        submitItem: function submitItem(uri, state, response) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // submit the state and the response of the item

                // once the data has been processed notify the success by resolving the promise
                resolve(/* the action response */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
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
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // call the action

                // once the action has been processed notify the success by resolving the promise
                resolve(/* the action response */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
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
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // send the signal

                // once the signal has been processed notify the success by resolving the promise
                resolve(/* the signal response */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        }
    };

    return sampleProxy;
});

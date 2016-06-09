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
    'helpers'
], function(_, helpers) {
    'use strict';

    /**
     * Some default config values
     * @type {Object}
     * @private
     */
    var _defaults = {
        serviceController : 'Runner',
        serviceExtension : 'taoQtiTest'
    };

    /**
     * The list of handled config entries. Each required entry is set to true, while optional entries are set to false.
     * @type {Object}
     * @private
     */
    var _entries = {
        testDefinition : true,
        testCompilation : true,
        serviceCallId : true,
        serviceController : false,
        serviceExtension : false
    };

    /**
     * Extract the handled config entries.
     * @param {Object} config
     * @returns {Object}
     * @throws Error if a required entry is missing
     */
    function getConfig(config) {
        var storage = {};
        _.forEach(_entries, function(value, name) {
           if ('undefined' !== typeof config[name]) {
               storage[name] = config[name];
           } else if (value) {
               throw new Error('The config entry "' + name + '" is required!');
           }
        });
        return _.defaults(storage, _defaults);
    }

    /**
     * Creates a config object for the proxy implementation
     * @param {Object} config - Some required and optional config
     * @param {String} config.testDefinition - The URI of the test
     * @param {String} config.testCompilation - The URI of the compiled delivery
     * @param {String} config.serviceCallId - The URI of the service call
     * @param {String} [config.serviceController] - The name of the service controller
     * @param {String} [config.serviceExtension] - The name of the extension containing the service controller
     * @returns {Object}
     */
    function configFactory(config) {
        // protected storage
        var storage = getConfig(config);

        // returns only a proxy storage object : no direct access to data is provided
        return {
            /**
             * Gets the URI of the test
             * @returns {String}
             */
            getTestDefinition : function getTestDefinition() {
                return storage.testDefinition;
            },

            /**
             * Gets the URI of the compiled delivery
             * @returns {String}
             */
            getTestCompilation : function getTestCompilation() {
                return storage.testCompilation;
            },

            /**
             * Gets the URI of the service call
             * @returns {String}
             */
            getServiceCallId : function getServiceCallId() {
                return storage.serviceCallId;
            },

            /**
             * Gets the name of the service controller
             * @returns {String}
             */
            getServiceController : function getServiceController() {
                return storage.serviceController;
            },

            /**
             * Gets the name of the extension containing the service controller
             * @returns {String}
             */
            getServiceExtension : function getServiceExtension() {
                return storage.serviceExtension;
            },

            /**
             * Gets an URL of a service action related to the test
             * @param {String} action - the name of the action to request
             * @returns {String} - Returns the URL
             */
            getTestActionUrl : function getTestActionUrl(action) {
                return helpers._url(action, this.getServiceController(), this.getServiceExtension(), {
                    testDefinition : this.getTestDefinition(),
                    testCompilation : this.getTestCompilation(),
                    serviceCallId : this.getServiceCallId()
                });
            },

            /**
             * Gets an URL of a service action related to a particular item
             * @param {String} uri - The URI of the item
             * @param {String} action - the name of the action to request
             * @returns {String} - Returns the URL
             */
            getItemActionUrl : function getItemActionUrl(uri, action) {
                return helpers._url(action, this.getServiceController(), this.getServiceExtension(), {
                    testDefinition : this.getTestDefinition(),
                    testCompilation : this.getTestCompilation(),
                    testServiceCallId : this.getServiceCallId(),
                    itemDefinition : uri
                });
            },

            /**
             * Gets an URL of a telemetry signal related to a particular item
             * @param {String} uri - The URI of the item
             * @param {String} signal - the name of the signal to request
             * @returns {String} - Returns the URL
             */
            getTelemetryUrl : function getTelemetryUrl(uri, signal) {
                return helpers._url(signal, this.getServiceController(), this.getServiceExtension(), {
                    testDefinition : this.getTestDefinition(),
                    testCompilation : this.getTestCompilation(),
                    testServiceCallId : this.getServiceCallId(),
                    itemDefinition : uri
                });
            }
        };
    }

    return configFactory;
});

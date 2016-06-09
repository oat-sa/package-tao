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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Metadata to be sent to the server. Will be saved in result storage as a trace variable.
 * Usage example:
 * <pre>
 * var testMetaData = testMetaDataFactory({
 *   testServiceCallId : this.itemServiceApi.serviceCallId
 * });
 *
 * testMetaData.setData({
 *   'TEST' : {
 *      'TEST_EXIT_CODE' : 'T'
 *   },
 *   'SECTION' : {
 *      'SECTION_EXIT_CODE' : 704
 *   }
 * });
 *
 * testMetaData.addData({'ITEM' : {
 *      'ITEM_START_TIME_CLIENT' : 1443596730143,
 *      'ITEM_END_TIME_CLIENT' : 1443596731301
 *    }
 * });
 * </pre>
 *
 * Data for each service call id will be stored in local storage to be able get data
 * after reloading the page or resuming the test session.
 *
 * To clear all data related to current test_call_id used <i>clearData</i> method.
 */
define([
    'lodash'
], function (_) {
    'use strict';

    /**
     * @param {Object} options
     * @param {string} options.testServiceCallId - test call id.
     */
     var testMetaDataFactory = function testMetaDataFactory(options) {
        var _testServiceCallId,
            _storageKeyPrefix = 'testMetaData_',
            _data = {};

        if (!options || options.testServiceCallId === undefined) {
            throw new TypeError("testServiceCallId option is required");
        }

        var testMetaData = {
            SECTION_EXIT_CODE : {
                'COMPLETED_NORMALLY': 700,
                'QUIT': 701,
                'COMPLETE_TIMEOUT': 703,
                'TIMEOUT': 704,
                'FORCE_QUIT': 705,
                'IN_PROGRESS': 706,
                'ERROR': 300
            },
            TEST_EXIT_CODE : {
                'COMPLETE': 'C',
                'TERMINATED': 'T',
                'INCOMPLETE': 'IC',
                'INCOMPLETE_QUIT': 'IQ',
                'INACTIVE': 'IA',
                'CANDIDATE_DISAGREED_WITH_NDA': 'DA'
            },
            /**
             * Return test call id.
             * @returns {string}- Test call id
             */
            getTestServiceCallId : function getTestServiceCallId () {
                return _testServiceCallId;
            },

            /**
             * Set test call id.
             * @param {string} value
             */
            setTestServiceCallId : function setTestServiceCallId (value) {
                _testServiceCallId = value;
            },

            /**
             * Set meta data. Current data object will be overwritten.
             * @param {Object} data - metadata object
             */
            setData : function setData(data) {
                _data = data;
                setLocalStorageData(JSON.stringify(_data));
            },

            /**
             * Add data.
             * @param {Object} data - metadata object
             * @param {Boolean} overwrite - whether the same data should be overwritten. Default - <i>false</i>
             */
            addData : function addData(data, overwrite) {
                data = _.clone(data);
                if (overwrite === undefined) {
                    overwrite = false;
                }

                if (overwrite) {
                    _.merge(_data, data);
                } else {
                    _data = _.merge(data, _data);
                }
                setLocalStorageData(JSON.stringify(_data));
            },

            /**
             * Get the saved data.
             * The cloned object will be returned to avoid unwanted affecting of the original data.
             * @returns {Object} - metadata object.
             */
            getData : function getData() {
                return _.clone(_data);
            },

            /**
             * Clear all data saved in current object and in local storage related to current test call id.
             * @returns {Object} - metadata object.
             */
            clearData : function clearData() {
                _data = {};
                window.localStorage.removeItem(testMetaData.getLocalStorageKey());
            },

            getLocalStorageKey : function getLocalStorageKey () {
                return _storageKeyPrefix + _testServiceCallId;
            }
        };

        /**
         * Initialize test meta data manager
         */
        function init() {
            _testServiceCallId = options.testServiceCallId;
            testMetaData.setData(getLocalStorageData());
        }

        /**
         * Set data to local storage
         * @param {string} val - data to be stored.
         */
        function setLocalStorageData(val) {
            var currentKey = testMetaData.getLocalStorageKey();
            try {
                window.localStorage.setItem(currentKey, val);
            } catch(domException) {
                if (domException.name === 'QuotaExceededError' ||
                    domException.name === 'NS_ERROR_DOM_QUOTA_REACHED') {
                    var removed = 0,
                        i = window.localStorage.length,
                        key;
                    while (i--) {
                        key  = localStorage.key(i);
                        if (/^testMetaData_.*/.test(key) && key !== currentKey) {
                            window.localStorage.removeItem(key);
                            removed++;
                        }
                    }
                    if (removed) {
                        setLocalStorageData(val);
                    } else {
                        throw domException;
                    }
                } else {
                    throw domException;
                }
            }
        }

        /**
         * Get data from local storage stored for current call id
         * @returns {*} saved data or empty object
         */
        function getLocalStorageData() {
            var data = window.localStorage.getItem(testMetaData.getLocalStorageKey()),
                result = JSON.parse(data) || {};

            return result;
        }

        init();

        return testMetaData;
    };

    return testMetaDataFactory;
});
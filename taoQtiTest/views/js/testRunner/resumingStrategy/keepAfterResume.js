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
 * Keep duration of test-taker activity in localstorage
 */

define([], function () {
    'use strict';

    /**
     * @param {Object} options
     * @param {string} options.accuracy - period of user status checking
     */
    var sessionStateFactory = function sessionStateFactory(options) {
        var _storageKey = 'sessionState_active_for',
            _accuracy,
            _interval = null;

        var sessionState = {

            reset: function reset() {
                _reset();
            },

            restart: function restart() {
                _reset();
                _start();
            },

            getDuration: function getDuration() {
                return getLocalStorageData();
            }
        };

        function _start() {
            if (null !== _interval) {
                throw new TypeError('Tracking is already started');
            }
            _interval = setInterval(function () {
                setLocalStorageData(getLocalStorageData() + _accuracy);
            }, _accuracy);
        }

        function _stop() {
            clearInterval(_interval);
            _interval = null;
        }

        function _reset() {
            _stop();
            clearLocalStorage();
        }

        function _init() {
            _accuracy = options && options.accuracy || 1000;
        }

        _init();

        /**
         * Store duration in ms to local storage
         * @param {*} val - data to be stored.
         */
        function setLocalStorageData(val) {
            window.localStorage.setItem(_storageKey, val);
        }

        /**
         * Get duration from local storage
         * @returns {int} in ms
         */
        function getLocalStorageData() {
            var data = window.localStorage.getItem(_storageKey),
                result = JSON.parse(data) || 0;
            return result;
        }

        /**
         * Clear storage
         */
        function clearLocalStorage() {
            window.localStorage.removeItem(_storageKey);
        }

        return sessionState;
    };

    return sessionStateFactory;
});
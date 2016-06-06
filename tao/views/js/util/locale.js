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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * @author Ivan Klimchuk <klimchuk@1pt.com>
 */
define(['module'], function (module) {
    'use strict';

    var configuration = module.config();

    /**
     * Util object for manipulate locale dependent data
     * @exports util/locale
     */
    return {

        /**
         * Returns config of component
         * @returns {*}
         */
        getConfig: function getConfig() {
            return configuration;
        },

        /**
         * Sets config of component
         * @param config
         */
        setConfig: function setConfig(config) {
            configuration = config || {};
        },

        /**
         * Returns current system decimal separator
         * @returns {string}
         */
        getDecimalSeparator: function getDecimalSeparator() {
            return this.getConfig() && this.getConfig().decimalSeparator ? this.getConfig().decimalSeparator : '.';
        },

        /**
         * Returns current system thousands separator
         * @returns {string}
         */
        getThousandsSeparator: function getThousandsSeparator() {
            return this.getConfig() && this.getConfig().thousandsSeparator ? this.getConfig().thousandsSeparator : '';
        },

        /**
         * Parse float values with process locale features
         * @param number
         * @returns {Number}
         */
        parseFloat: function (number) {
            if (!number) {
                return parseFloat(number);
            }

            var parts = number.split(this.getDecimalSeparator(), 2);
            var ones = parts[0];

            if (this.getThousandsSeparator().length) {
                ones = parts[0].replace(new RegExp('\\' + this.getThousandsSeparator(), 'g'), '');
            }

            return parseFloat(ones) + parseFloat('0.' + parts[1]);
        },

        /**
         * Parse integer values with process locale features
         * @param number
         * @param numericBase
         * @returns {Number}
         */
        parseInt: function (number, numericBase) {

            if (this.getThousandsSeparator().length) {
                number = number.replace(new RegExp('\\' + this.getThousandsSeparator(), 'g'), '');
            }

            return parseInt(number, numericBase);
        }
    };

});

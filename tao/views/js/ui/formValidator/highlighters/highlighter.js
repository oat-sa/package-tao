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
 * @author Aleh Hutnikau
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/formValidator/highlighters/message',
    'ui/formValidator/highlighters/tooltip',
], function ($, _, __, messageHighlighter, tooltipHighlighter) {
    'use strict';

    var defaultOptions = {
        type : 'message'
    };

    /**
     * Error field highlighter
     * @param {Object} options
     * @param {string} [options.type] - highlighter provider name
     * @param {string} [options.errorClass] -
     * @param {string} [options.errorMessageClass] -
     */
    function highlighterFactory(options) {
        var highlighter,
            provider;

        highlighter = {
            /**
             * Destroy init
             * @param {object} options
             */
            init : function init() {
                options = $.extend(true, defaultOptions, options);
                provider = getProvider(options);

                return this;
            },
            /**
             * Highlight field
             * @param {jQuery} $field - field element to be highlighted
             * @param {string} message - message text.
             */
            highlight : function highlight($field, message) {
                provider.highlight($field, message);
            },
            /**
             * Unhighlight field
             * @param {jQuery} $field
             */
            unhighlight : function unhighlight($field) {
                provider.unhighlight($field);
            },
            /**
             * Destroy highlighter
             * @param {jQuery} $field
             */
            destroy : function destroy($field) {
                provider.destroy($field);
            }
        };

        /**
         * Get highlighter implementation
         * @private
         * @param {object} options - options
         * @returns {object} - highlighter implementation
         */
        function getProvider(options) {
            if (!highlighterFactory.providers[options.type]) {
                throw new TypeError('Provider ' + name + ' is not registered.');
            }

            return highlighterFactory.providers[options.type](options);
        }

        return highlighter.init();
    }

    highlighterFactory.providers = {};

    highlighterFactory.register = function (name, provider) {
        highlighterFactory.providers[name] = provider;
    };

    highlighterFactory.register('message', messageHighlighter);
    highlighterFactory.register('tooltip', tooltipHighlighter);

    return highlighterFactory;
});
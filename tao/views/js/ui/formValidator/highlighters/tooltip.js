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
    'tooltipster'
], function ($, _, __, tooltipster) {
    'use strict';

    var defaultOptions = {
        tooltip : {
            delay : 0,
            theme : 'tao-error-tooltip',
            trigger : 'custom'
        }
    };

    /**
     * Error field highlighter
     * @param {Object} options
     * @param {string} [options.errorClass] - field error class
     * @see {@link here: http://iamceege.github.io/tooltipster/} - more tooltipster plugin options
     */
    function highlighterFactory(options) {
        var highlighter;

        options = _.merge(defaultOptions, options);

        highlighter = {
            initTooltip : function ($field) {
                $field.tooltipster(options.tooltip);
            },

            /**
             * Highlight field by class defined in <i>self.options.errorClass</i> and add error message after it.
             * @param {jQuery} $field - field element to be highlighted
             * @param {string} message - message text.
             */
            highlight : function highlight($field, message) {
                this.initTooltip($field);
                $field.tooltipster('content', message);
                $field.tooltipster('show');

                $field.addClass(options.errorClass);
            },

            /**
             * Unhighlight field (remove error class and error message).
             * @param {jQuery} $field
             */
            unhighlight : function unhighlight($field) {
                this.destroy($field);
                $field.removeClass(options.errorClass);
            },

            destroy : function destroy($field) {
                if ($field.data('tooltipster') !== undefined) {
                    $field.tooltipster('destroy');
                }
            }
        };

        return highlighter;
    }

    return highlighterFactory;
});
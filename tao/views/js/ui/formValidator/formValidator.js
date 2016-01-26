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
 * Form validator plugin
 * @example
 * HTML markup:
 * </pre>
 *   <input
 *     name="field_name"
 *     type="text"
 *     data-validate="$length(min=4, max=10, message='Length should be from 4 to 10'); $numeric(message='Must be numeric')"/>
 * <pre>
 * To configure rules use <i>data-validate</i> attribute. Rules should be separated by semicolon.
 * Each rule consist of the name of the rule prefixed with <i>$</i> and optionally it's options in brackets.
 * example:
 * $notEmpty; $length(max=10, min=5, message='From 5 to 10');
 *          ^^              ^^
 * Note:
 * Rules should ends with <i>;</i>. Rules should be space separated.
 *
 *
 * A set of standard validation methods is provided:
 * <ul>
 *   <li>numeric</li>
 *   <li>notEmpty([message])</li>
 *   <li>pattern(pattern, modifier, [message])</li>
 *   <li>length(min, max, [message])</li>
 *   <li>fileExists(baseUrl, [message])</li>
 *   <li>validRegex([message])</li>
 * </ul>
 * For more information see 'core/validator/validators' module
 *
 * JavaScript:
 * <pre>
 * var validator = formValidatorFactory({
 *   highlighter : {
 *     type : 'tooltip',
 *     errorClass : 'error',
 *     errorMessageClass : 'validate-error'
 *   },
 *   container : $('#form_1'),
 *   event : 'change',
 *   selector : '[data-validate]:not([disabled])'
 * });
 * validator.validate();
 * </pre>
 * @author Aleh Hutnikau
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/validator',
    'ui/formValidator/highlighters/highlighter'
], function($, _, __, validator, highlighterFactory){
    'use strict';

    var defaultOptions = {
        highlighter : {
            type : 'message',
            errorClass : 'error'
        },
        container : $(document),
        selector : '[data-validate]',
        validateOnInit : false,
        events : ['change', 'blur']
    };

    /**
     * @param {Object} options
     * @param {jQuery} [options.container] - container which contains elements to validate
     * @param {Object} [options.selector = '[data-validate]'] - selector to find elements to validate
     * @param {string|Array} [options.events = ['change', 'blur']] - the default event that triggers the validation
     * @param {Object} [options.highlighter - error filed highlighter options {@see ui/formValidator/highlighters/highlighter}
     * @param {Object} [options.validateOnInit = false] - whether form should be validated after plugin initialization
     */
    var formValidatorFactory = function formValidatorFactory(options) {
        var state = {
                valid : true,
                errors : []
            },
            highlighter,
            $toValidate,
            validator;

        validator = {
            init : function init() {
                var self = this;

                self.options = $.extend(true, defaultOptions, options);

                $toValidate = getFieldsToValidate();

                $toValidate.validator({
                    event : self.options.events,
                    validated : function (valid, report) {
                        afterFieldValidate($(this), valid, report);
                    }
                });

                if (options.validateOnInit) {
                    self.validate();
                }

                return self;
            },
            /**
             * Validate form.
             * @returns {boolean} - whether form is valid
             */
            validate : function validate() {
                var self = this,
                    $toValidate = getFieldsToValidate();

                state = {
                    valid : true,
                    errors : []
                };
                $toValidate.validator('validate', function (valid, report) {
                    afterFieldValidate($(this), valid, report);
                });

                return state.valid;
            },
            /**
             * Get form state in following format:
             * <pre>
             *  {
             *    valid : false,
             *
             *  }
             * </pre>
             * @returns {object}
             */
            getState : function getState() {
                return state;
            },
            /**
             * Destroy validator.
             */
            destroy : function destroy() {
                var $fields = getFieldsToValidate();
                $fields.each(function () {
                    if (highlighter) {
                        highlighter.destroy($(this));
                    }
                });
            },
            /**
             * Get container which contains fields to validate
             * @returns {jQuery}
             */
            getContainer : function getContainer() {
                var self = this,
                    $container;

                if (self.options.container && self.options.container.length) {
                    $container = self.options.container;
                } else {
                    $container = $(document);
                }

                return $container;
            }
        };

        /**
         * Callback will be called after field validation.
         * @private
         * @param {jQuery} $field - validated field
         * @param {boolean} valid - whether field is valid
         * @param {array} report - list of reports {@see core/validator/Report}
         */
        function afterFieldValidate($field, valid, report) {
            var firstErrorData;
            state.valid = state.valid && valid;

            if (!valid) {
                firstErrorData = _.pluck(_.filter(report, {type : 'failure'}), 'data')[0];
                highlightField($field, false, firstErrorData.message);
                state.errors.push($.extend(
                    true,
                    {
                        field : $field
                    },
                    firstErrorData
                ));
            } else {
                highlightField($field, true);
            }
        }

        /**
         * Add or remove error class and error message
         * @private
         * @param {jQuery} $field - element to be highlighted
         * @param {boolean} success - whether input is valid or not.
         * @param {string} [message]
         */
        function highlightField($field, success, message) {
            var highlighter = getHighlighter();
            if (success) {
                highlighter.unhighlight($field);
            } else {
                highlighter.highlight($field, message);
            }
        }

        /**
         * Get highlighter helper
         * @private
         * @return {object} - highlighter {@see ui/formValidator/highlighters/highlighter}
         */
        function getHighlighter() {
            var self = validator;

            if (highlighter === undefined) {
                highlighter = highlighterFactory(self.options.highlighter);
            }
            return highlighter;
        }

        /**
         * Get fields to validate
         * @private
         * @returns {jQuery}
         */
        function getFieldsToValidate() {
            var self = validator,
                $container;

            if ($toValidate === undefined) {
                $container = self.getContainer();
                $toValidate = $container.find(self.options.selector);
            }
            return $toValidate;
        }

        return validator.init();
    };

    return formValidatorFactory;
});

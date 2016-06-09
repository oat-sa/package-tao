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
    'ui/component',
    'tpl!ui/actionbar/tpl/main'
], function ($, _, __, component, mainTpl) {
    'use strict';

    /**
     * Defines an action bar
     * @type {Object}
     */
    var actionbar = {
        /**
         * Gets the definition of a button
         * @param {String} id - The identifier of the button
         * @returns {Object|undefined}
         */
        getButton: function getButton(id) {
            if (this.is('rendered')) {
                return this.buttons[id];
            }
        },

        /**
         * Gets the DOM element of a button
         * @param {String} id - The identifier of the button
         * @returns {jQuery|undefined}
         */
        getButtonElement: function getButtonElement(id) {
            if (this.is('rendered')) {
                return this.controls.$buttons[id];
            }
        },

        /**
         * Shows a button
         * @param {String} id - The identifier of the button
         * @returns {actionbar}
         */
        showButton: function showButton(id) {
            var $btn = this.getButtonElement(id);
            if ($btn) {
                $btn.removeClass('hidden');
            }
            return this;
        },

        /**
         * Hides a button
         * @param {String} id - The identifier of the button
         * @returns {actionbar}
         */
        hideButton: function hideButton(id) {
            var $btn = this.getButtonElement(id);
            if ($btn) {
                $btn.addClass('hidden');
            }
            return this;
        },

        /**
         * Toggles a button according to a condition
         * @param {String} id - The identifier of the button
         * @param {Boolean} condition - If the condition is `true` the button will be displayed
         * @returns {actionbar}
         */
        toggleButton: function toggleButton(id, condition) {
            var $btn = this.getButtonElement(id);
            if ($btn) {
                if (undefined !== condition) {
                    condition = !condition;
                }
                $btn.toggleClass('hidden', condition);
            }
            return this;
        },

        /**
         * Shows the conditional buttons
         * @returns {actionbar}
         */
        showConditionals: function showConditionals() {
            if (this.is('rendered')) {
                this.controls.$conditional.removeClass('hidden');
            }
            return this;
        },

        /**
         * Hides the conditional buttons
         * @returns {actionbar}
         */
        hideConditionals: function hideConditionals() {
            if (this.is('rendered')) {
                this.controls.$conditional.addClass('hidden');
            }
            return this;
        },

        /**
         * Toggles the conditional buttons according to a condition
         * @param {Boolean} condition - If the condition is `true` the conditional buttons will be displayed
         * @returns {actionbar}
         */
        toggleConditionals: function toggleConditionals(condition) {
            if (this.is('rendered')) {
                if (undefined !== condition) {
                    condition = !condition;
                }
                this.controls.$conditional.toggleClass('hidden', condition);
            }
            return this;
        },

        /**
         * Shows all the buttons
         * @returns {actionbar}
         */
        showAll: function showAll() {
            if (this.is('rendered')) {
                this.controls.$all.removeClass('hidden');
            }
            return this;
        },

        /**
         * Hides all the buttons
         * @returns {actionbar}
         */
        hideAll: function hideAll() {
            if (this.is('rendered')) {
                this.controls.$all.addClass('hidden');
            }
            return this;
        },

        /**
         * Toggles all the buttons according to a condition
         * @param {Boolean} condition - If the condition is `true` the buttons will be displayed
         * @returns {actionbar}
         */
        toggleAll: function toggleAll(condition) {
            if (this.is('rendered')) {
                if (undefined !== condition) {
                    condition = !condition;
                }
                this.controls.$all.toggleClass('hidden', condition);
            }
            return this;
        }
    };

    /**
     * Builds an instance of the actionbar component
     * @param {Object} config
     * @param {Array} config.buttons - The list of buttons to display.
     * @param {String} config.buttons.id - The id of the button
     * @param {String} config.buttons.label - The text displayed in the button
     * @param {String} config.buttons.icon - An optional icon displayed in the button
     * @param {String} config.buttons.title - An optional tooltip displayed on the button
     * @param {Boolean} config.buttons.conditional - The button is hidden by default and must be displayed later
     * @param {Function} config.buttons.action - An action called when the button is clicked
     * @param {Boolean} [config.vertical] - Displays the action bar vertically
     * @returns {actionbar}
     *
     * @event init - Emitted when the component is initialized
     * @event destroy - Emitted when the component is destroying
     * @event render - Emitted when the component is rendered
     * @event button - Emitted when a button is clicked
     * @event show - Emitted when the component is shown
     * @event hide - Emitted when the component is hidden
     * @event enable - Emitted when the component is enabled
     * @event disable - Emitted when the component is disabled
     * @event template - Emitted when the template is changed
     */
    function actionbarFactory(config) {
        return component(actionbar)
            .setTemplate(mainTpl)

            // uninstalls the component
            .on('destroy', function () {
                this.buttons = null;
                this.controls = null;
            })

            // renders the component
            .on('render', function () {
                var self = this;
                var $component = this.getElement();

                // vertical or horizontal ?
                this.setState('horizontal', $component.hasClass('horizontal-action-bar'));
                this.setState('vertical', $component.hasClass('vertical-action-bar'));

                // get access to all needed placeholders
                this.buttons = {};
                this.controls = {
                    $buttons: {},
                    $conditional: $component.find('button.conditional'),
                    $all: $component.find('button')
                };
                _.forEach(this.config.buttons, function (button) {
                    self.buttons[button.id] = button;
                    self.controls.$buttons[button.id] = $component.find('[data-control="' + button.id + '"]');
                });

                // click on a button
                this.$component.on('click', 'button', function (e) {
                    var $this = $(this);
                    var buttonId = $this.closest('button').data('control');
                    var button = self.getButton(buttonId);

                    e.preventDefault();

                    if (button && button.action) {
                        button.action.call(self, buttonId, button);
                    }

                    /**
                     * @event actionbar#button
                     * @param {String} buttonId
                     * @param {Object} button
                     */
                    self.trigger('button', buttonId, button);
                });
            })
            .init(config);
    }

    return actionbarFactory;
});

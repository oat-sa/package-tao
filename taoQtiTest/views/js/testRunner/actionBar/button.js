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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'util/capitalize',
    'tpl!taoQtiTest/testRunner/tpl/button'
], function ($, _, __, capitalize, buttonTpl) {
    'use strict';

    /**
     * Events namespace
     * @type {String}
     * @private
     */
    var _ns = '.actionBarButton';

    /**
     * Default type of button
     * @type {String}
     * @private
     */
    var _defaultButtonType = 'button';

    /**
     * Default type of button when items are presents
     * @type {String}
     * @private
     */
    var _defaultItemsType = 'menu';

    /**
     * Defines an action bar button
     * @type {Object}
     */
    var button = {
        /**
         * Initializes the button
         * @param {String} id
         * @param {Object} config
         * @param {String} [config.label] - the label to be displayed in the button
         * @param {String} [config.icon] - the icon to be displayed in the button
         * @param {String} [config.title] - the title to be displayed in the button
         * @param {String} [config.type] - the type of button (button, menu, group)
         * @param {Array} [config.items] - an optional list of menu items
         * @param {String} [config.content] - an optional content to place just after the button
         * @param {String} [config.discard] - an optional CSS selector to discard from the click event
         * @param {Object} [testContext] - the complete state of the test
         * @param {Object} [testRunner] - the test runner instance
         * @returns {button}
         */
        init : function init(id, config, testContext, testRunner) {
            this.config = _.omit(config || {}, function(value) {
                return value === undefined || value === null;
            });
            this.config.id = id;
            this.config.is = {};

            this.testContext = testContext || {};
            this.testRunner = testRunner || {};

            this.setup();

            this.assumeType();

            if (!this.config.title && this.config.label && this.is('button')) {
                this.config.title = this.config.label;
            }

            return this;
        },

        /**
         *
         * @param {Document} [_document]
         * @returns {{itemContainerWindow: *, $: *, qtiRunner: (*|qtiRunner), $item: *, context: *}}
         */
        getItemContext: function(_document){
            var doc = _document || document;
            var itemFrame, itemWindow, itemContainerFrame, itemContainerWindow, item$, qtiRunner, $item, context;

            itemFrame = doc.getElementById('preview-container');

            // test
            if(!itemFrame) {
                itemFrame = doc.getElementById('qti-item');
                itemWindow = itemFrame && itemFrame.contentWindow;
                itemContainerFrame = itemWindow && itemWindow.document.getElementById('item-container');
                itemContainerWindow = itemContainerFrame && itemContainerFrame.contentWindow;
                context = 'test';
            }

            // preview
            else {
                itemContainerWindow = itemFrame && itemFrame.contentWindow;
                context = 'preview';
            }

            item$ = itemContainerWindow && itemContainerWindow.$;
            qtiRunner = itemContainerWindow && itemContainerWindow.qtiRunner;
            $item = item$ && item$('.qti-item');

            return {
                itemContainerWindow: itemContainerWindow,
                $: item$,
                qtiRunner: qtiRunner,
                $item: $item,
                context: context
            };
        },

        /**
         * Assumes the right button type is set
         * @private
         */
        assumeType : function assumeType() {
            var type = this.config.type;
            var setType;

            if (!type) {
                if (this.config.items) {
                    type = _defaultItemsType;
                } else {
                    type = _defaultButtonType;
                }
            }

            setType = this['setType' + capitalize(type)] || this.setTypeButton;

            setType.call(this);
        },

        /**
         * Sets the button type to standard button
         * @private
         */
        setTypeButton : function assumeTypeButton() {
            this.config.type = 'button';
            this.config.is.button = true;
        },

        /**
         * Sets the button type to button with menu
         * @private
         */
        setTypeMenu : function assumeTypeButton() {
            this.config.type = 'menu';
            this.config.is.menu = true;
            this.config.is.button = true;
        },

        /**
         * Sets the button type to group of buttons
         * @private
         */
        setTypeGroup : function assumeTypeButton() {
            this.config.type = 'group';
            this.config.is.group = true;
            this.config.is.button = false;
            this.config.title = '';
        },

        /**
         * Uninstalls the button
         * @param {jQuery|String|HTMLElement} [dom] - A DOM element to bind before clearing
         * @returns {button}
         */
        clear : function clear(dom) {
            if (dom) {
                this.bindTo(dom);
            }

            this.unbindEvents();

            this.tearDown();

            return this;
        },

        /**
         * Renders the button to a DOM element
         * @returns {jQuery}
         */
        render : function render() {
            var dom = this.renderTemplate();

            this.bindTo(dom);

            this.afterRender();

            this.bindEvents();

            return this.$button;
        },

        /**
         * Gets the template renderer
         * @returns {jQuery}
         */
        getTemplate : function getTemplate() {
            return buttonTpl;
        },

        /**
         * Renders the button template
         * @returns {jQuery}
         */
        renderTemplate : function renderTemplate() {
            var template = this.getTemplate();
            return template(this.config);
        },

        /**
         * Binds the button to an existing DOM
         * @param {jQuery|String|HTMLElement} dom - The DOM element to bind to
         * @returns {button}
         */
        bindTo : function bindTo(dom) {
            this.$button = $(dom);
            this.$menu = this.$button.find('.menu');

            return this;
        },

        /**
         * Binds the events onto the button DOM
         * @returns {button}
         * @private
         */
        bindEvents : function bindEvents() {
            var self = this;

            this.$button.on('click' + _ns, function(e) {
                var hasMenu = self.hasMenu();
                var $target = $(e.target);
                var $menuItem = hasMenu && $target.closest('.menu-item');
                var discard = self.config.discard && $target.closest(self.config.discard).length;
                var $action;
                var id;

                if (!self.$button.hasClass('disabled') && !discard) {
                    if ($menuItem && $menuItem.length) {
                        id = $menuItem.data('control');

                        self.setActiveMenu(id);
                        self.menuAction(id, $menuItem);
                        self.closeMenu();

                        /**
                         * Triggers a menuaction event
                         * @event button#menuaction
                         * @param {String} id - The menu item identifier
                         * @param {jQuery} $menuItem - The menu button
                         * @param {button} button - The button instance
                         */
                        self.$button.trigger('menuaction', [id, $menuItem, self]);
                    } else {
                        $action = $target.closest('.action-button');
                        id = $action.data('control');

                        self.action(id, $action);

                        if (hasMenu) {
                            self.toggleMenu();
                        }

                        /**
                         * Triggers a action event
                         * @event button#action
                         * @param {String} id - The button identifier
                         * @param {jQuery} $button - The button
                         * @param {button} button - The button instance
                         */
                        self.$button.trigger('action', [id, $action, self]);
                    }
                }

            });

            return this;
        },

        /**
         * Removes events listeners from the button DOM
         * @returns {button}
         * @private
         */
        unbindEvents : function unbindEvents() {
            if (this.$button) {
                this.$button.off(_ns);
            }

            return this;
        },

        /**
         * Gets the button identifier
         * @returns {String}
         */
        getId : function getId() {
            return this.config.id;
        },

        /**
         * Gets the button label
         * @returns {String}
         */
        getLabel : function getLabel() {
            return this.config.label;
        },

        /**
         * Tells if the button has the wanted type
         * @param {String} type
         * @returns {Boolean}
         */
        is : function is(type) {
            return !!this.config.is[type];
        },

        /**
         * Tells if the button is visible and can be rendered
         * @returns {Boolean}
         */
        isVisible : function isVisible() {
            return true;
        },

        /**
         * Tells if the button has a menu
         * @returns {Boolean}
         */
        hasMenu : function hasMenu() {
            return !!(this.$menu && this.$menu.length);
        },

        /**
         * Tells if the menu is open
         * @returns {Boolean}
         */
        isMenuOpen : function isMenuOpen() {
            var isOpen = false;

            if (this.hasMenu()) {
                isOpen = !this.$menu.hasClass('hidden');
            }

            return isOpen;
        },

        /**
         * Closes the menu if the button have one
         * @returns {button}
         */
        closeMenu : function closeMenu() {
            if (this.hasMenu()) {
                this.setActive(false);
                this.$menu.addClass('hidden');
            }

            return this;
        },

        /**
         * Opens the menu if the button have one
         * @returns {button}
         */
        openMenu : function openMenu() {
            if (this.hasMenu()) {
                this.setActive(true);
                this.$menu.removeClass('hidden');
            }

            return this;
        },

        /**
         * Opens or closes the menu if the button have one
         * @returns {button}
         */
        toggleMenu : function toggleMenu() {
            if (this.hasMenu()) {
                if (this.isMenuOpen()) {
                    this.closeMenu();
                } else {
                    this.openMenu();
                }
            }

            return this;
        },

        /**
         * Install an event handler on the underlying DOM element
         * @param {String} eventName
         * @returns {button}
         */
        on: function on(eventName) {
            var dom = this.$button;
            if (dom) {
                dom.on.apply(dom, arguments);
            }

            return this;
        },

        /**
         * Uninstall an event handler from the underlying DOM element
         * @param {String} eventName
         * @returns {button}
         */
        off: function off(eventName) {
            var dom = this.$button;
            if (dom) {
                dom.off.apply(dom, arguments);
            }

            return this;
        },

        /**
         * Triggers an event on the underlying DOM element
         * @param {String} eventName
         * @param {Array|Object} extraParameters
         * @returns {button}
         */
        trigger : function trigger(eventName, extraParameters) {
            var dom = this.$button;

            if (undefined === extraParameters) {
                extraParameters = [];
            }
            if (!_.isArray(extraParameters)) {
                extraParameters = [extraParameters];
            }

            extraParameters.push(this);

            if (dom) {
                dom.trigger(eventName, extraParameters);
            }

            return this;
        },


        /**
         * Target the element, either the button or a button in a group
         * @param {String} [id] - the button id in case of groups
         * @returns {jQueryElement} the target
         */
        _target : function _target(id){
            var $target = this.$button;

            if (id && id !== this.config.id) {
                $target = this.$button.find('[data-control="' + id + '"]');
            }
            return $target;
        },

        /**
         * Disables the button
         * @param {String} [id] - the button id in case of groups
         * @returns {button}
         */
        disable : function disable(id) {

            this._target(id).addClass('disabled');

            return this;
        },

        /**
         * Enables the button
         * @param {String} [id] - the button id in case of groups
         * @returns {button}
         */
        enable : function enable(id) {

            this._target(id).removeClass('disabled');

            return this;
        },

        /**
         * Hide a button
         * @param {String} [id] - the button id in case of groups
         * @returns {button} chains
         */
        hide : function hide(id) {

            this._target(id).hide();

            return this;
        },

        /**
         * Show a button
         * @param {String} [id] - the button id in case of groups
         * @returns {button} chains
         */
        show : function show(id) {

            this._target(id).show();

            return this;
        },

        /**
         * Tells if the button is active
         * @param {String} [id]
         * @returns {Boolean}
         */
        isActive : function isActive(id) {
            return this._target(id).hasClass('active');
        },

        /**
         * Sets the button active state
         * @param {Boolean} active
         * @param {String} [id]
         * @returns {button}
         */
        setActive : function setActive(active, id) {

            this._target(id).toggleClass('active', active);

            return this;
        },

        /**
         * Removes the button active state
         * @returns {button}
         */
        clearActive : function clearActive() {
            if (this.is('button')) {
                this.$button.removeClass('active');
            } else {
                this.$button.find('.active').removeClass('active');
            }

            return this;
        },

        /**
         * Gets the id of the selected menu entry
         * @returns {String|null}
         */
        getActiveMenu : function getActiveMenu() {
            var selected;
            if (this.hasMenu()) {
                selected = this.$menu.find('.selected').data('control');
            }
            return selected || null;
        },

        /**
         * Sets the selected menu entry
         * @param {String} id
         * @returns {button}
         */
        setActiveMenu : function setActiveMenu(id) {
            if (this.hasMenu()) {
                this.clearActiveMenu();
                this.$menu.find('[data-control="'+ id + '"]').addClass('selected');
            }
            return this;
        },

        /**
         * Clears the menu selection
         * @returns {button}
         */
        clearActiveMenu : function setActiveMenu() {
            if (this.hasMenu()) {
                this.$menu.find('.selected').removeClass('selected');
            }
            return this;
        },

        /**
         * Additional setup onto the button config set
         * @private
         */
        setup : function setup() {
            // just a template method to be overloaded
        },

        /**
         * Additional cleaning while uninstalling the button
         * @private
         */
        tearDown : function tearDown() {
            // just a template method to be overloaded
        },

        /**
         * Additional DOM rendering
         * @private
         */
        afterRender : function afterRender() {
            // just a template method to be overloaded
        },

        /**
         * Action called when the button is clicked
         * @param {String} id
         * @param {jQuery} $action
         * @private
         */
        action : function action(id, $action) {
            // just a template method to be overloaded
        },

        /**
         * Action called when a menu item is clicked
         * @param {String} id
         * @param {jQuery} $menuItem
         * @private
         */
        menuAction : function menuAction(id, $menuItem) {
            // just a template method to be overloaded
        },

        /**
         * Action called when the assessment item has been loaded
         */
        itemLoaded : function() {
            // just a template method to be overloaded
        }
    };

    /**
     * Builds a button instance
     * @param {Object} properties
     * @returns {button}
     */
    var buttonFactory = function buttonFactory(properties) {
        return _.defaults(properties || {}, button);
    };

    return buttonFactory;
});

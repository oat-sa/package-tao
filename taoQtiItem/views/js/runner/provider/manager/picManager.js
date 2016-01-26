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
    'taoQtiItem/qtiItem/core/Element'
], function ($, _, Element) {
    'use strict';

    /**
     * The template of a PicManager instance
     * @type {picManager}
     */
    var picManager = {
        /**
         * Creates a manager for a particular PIC
         *
         * @param {Object} pic
         * @param {QtiItem} item
         * @returns {picManager}
         */
        init : function init(pic, item) {
            if (Element.isA(pic, 'infoControl')) {
                this._pic = pic;
            }

            if (Element.isA(item, 'assessmentItem')) {
                this._item = item;
            }

            return this;
        },

        /**
         * Gets the managed PIC
         *
         * @returns {Object} the descriptor of the PIC
         */
        getPic : function getPic() {
            return this._pic;
        },

        /**
         * Gets the related Item
         *
         * @returns {QtiItem} the Item
         */
        getItem : function getItem() {
            return this._item;
        },

        /**
         * Gets the PIC serial
         * @returns {String}
         */
        getSerial : function getSerial() {
            return this._pic && this._pic.serial;
        },

        /**
         * Gets the PIC type identifier
         * @returns {String}
         */
        getTypeIdentifier : function getTypeIdentifier() {
            return this._pic && this._pic.typeIdentifier;
        },

        /**
         * Gets the underlying DOM element of the managed PIC
         * @returns {{pic: (jQuery), tool: (jQuery), button: (jQuery), broken: (Boolean))}|*} An object providing the underlying DOM elements of the PIC and its tool
         */
        getDom : function getDom() {
            if (!this._dom) {
                var serial = this.getSerial();
                var pic, tool;

                if (serial) {
                    pic = $('[data-serial="' + serial + '"]');
                    if (pic.length) {
                        tool = $('[data-pic-serial="' + serial + '"]');

                        if (!tool.length) {
                            tool = pic.children().first();
                        }

                        this._dom = {
                            pic : pic,
                            tool : tool,
                            button : tool.find('.sts-button'),
                            broken : pic.is(':empty') // tells if the tool has been moved outside of the PIC
                        };
                    }
                }
            }

            return this._dom;
        },

        /**
         * Enables the PIC.
         * @fires enable
         * @returns {picManager}
         */
        enable : function enable() {
            // @todo: find a better solution for disabling/enabling a PIC
            var dom = this.getDom();
            if (dom) {
                // just remove the disabled state and destroy the disable mask
                dom.button.removeClass('disabled');
                dom.tool.find('.sts-button-disable-mask').remove();

                this.disabled = false;
                this.trigger('enable');
            }

            return this;
        },

        /**
         * Disables the PIC
         * @fires disable
         * @returns {picManager}
         */
        disable : function disable() {
            // @todo: find a better solution for disabling/enabling a PIC
            var dom = this.getDom();
            var button;
            if (dom) {
                // set a disabled state by adding a CSS class, then mask the button with a top-level element
                button = dom.button.addClass('disabled');

                $('<div class="sts-button-disable-mask" style="position:absolute;z-index:10000000000000"></div>')
                    .appendTo(dom.tool)
                    .offset(button.offset())
                    .width(button.outerWidth())
                    .height(button.outerHeight());

                // also hide any sub component
                dom.tool.find('.sts-container, .sts-menu-container').addClass('sts-hidden-container');

                this.disabled = true;
                this.trigger('disable');
            }

            return this;
        },

        /**
         * Shows the PIC
         * @fires show
         * @returns {picManager}
         */
        show : function show() {
            var dom = this.getDom();
            if (dom) {
                dom.tool.show();

                this.trigger('show');
            }

            return this;
        },

        /**
         * Hides the PIC
         * @fires hide
         * @returns {picManager}
         */
        hide : function hide() {
            var dom = this.getDom();
            if (dom) {
                dom.tool.hide();

                this.trigger('hide');
            }

            return this;
        },

        /**
         * Triggers an event on the underlying DOM element
         * @param {String} eventName
         * @returns {picManager}
         */
        trigger : function trigger(eventName) {
            var dom = this.getDom();
            var args = _.rest(arguments, 1);

            args.unshift(this);

            if (dom) {
                // trigger the event, if the tool has been moved outside of the PIC, trigger also the event on the PIC
                dom.tool.trigger(eventName, args);
                if (dom.broken) {
                    dom.pic.trigger(eventName, args);
                }
            }

            return this;
        }
    };

    /**
     * The template of a PicManagerCollection instance
     * @type {picManagerCollection}
     */
    var picManagerCollection = {
        /**
         * Creates the collection of PIC from an Item
         *
         * @param {QtiItem} item
         * @returns {picManagerCollection}
         */
        init : function init(item) {
            if (Element.isA(item, 'assessmentItem')) {
                this._item = item;
            }

            return this;
        },

        /**
         * Gets the list of PIC managers for the PIC provided by the running item.
         *
         * @param {Boolean} [force] Force a list rebuild
         * @returns {Array} Returns the list of managers for the provided PIC
         */
        getList : function getList(force) {
            var self = this;

            // build the list if empty
            if (force || !self._list) {
                self._map = {};
                self._list = [];
                if (self._item) {
                    _.forEach(self._item.getElements(), function(element) {
                        var manager;

                        if (Element.isA(element, 'infoControl')) {
                            manager = managerFactory(element, self._item);
                            self._list.push(manager);
                            self._map[element.serial] = manager;
                            self._map[element.typeIdentifier] = manager;
                        }
                    });
                }
            }

            return this._list;
        },

        /**
         * Gets the manager of the first PIC matching the identifier from the list provided by the running item.
         *
         * @param {String} picId The PIC typeIdentifier or serial
         * @returns {Object} The manager of the PIC
         */
        getPic : function getPic(picId) {
            this.getList();
            return this._map[picId];
        },

        /**
         * Executes an action on a particular PIC from the running item.
         * @param {String} picId The PIC typeIdentifier or serial
         * @param {String} action The name of the action to call
         * @returns {*} Returns the action result
         */
        execute : function execute(picId, action) {
            var pic = this.getPic(picId);
            if (pic && pic[action]) {
                return pic[action].apply(pic, _.rest(arguments, 2));
            }
        },

        /**
         * Executes an action on each PIC provided by the running item.
         * @param {String} action The name of the action to call
         * @param {Function} [filter] An optional filter to reduce the list
         * @returns {picManagerCollection}
         */
        executeAll : function executeAll(action, filter) {
            var args = _.rest(arguments, 2);
            var cb;

            if (typeof filter === 'function') {
                cb = function(pic) {
                    if (filter.call(pic, pic) && pic[action]) {
                        pic[action].apply(pic, args);
                    }
                };
            } else {
                cb = function(pic) {
                    if (pic[action]) {
                        pic[action].apply(pic, args);
                    }
                };
            }

            return this.each(cb);
        },

        /**
         * Calls a callback function on each listed PIC from the running item.
         * @param {Function} cb The callback function to apply on each listed PIC
         * @returns {picManagerCollection}
         */
        each : function each(cb) {
            _.forEach(this.getList(), cb);
            return this;
        },

        /**
         * Enables a PIC provided by the running item.
         *
         * @param {String} picId The PIC typeIdentifier or serial
         * @returns {picManagerCollection}
         */
        enablePic : function enablePic(picId) {
            this.execute(picId, 'enable');
            return this;
        },

        /**
         * Disables a PIC provided by the running item.
         *
         * @param {String} picId The PIC typeIdentifier or serial
         * @returns {picManagerCollection}
         */
        disablePic : function disablePic(picId) {
            this.execute(picId, 'disable');
            return this;
        },

        /**
         * Shows a PIC provided by the running item.
         *
         * @param {String} picId The PIC typeIdentifier or serial
         * @returns {picManagerCollection}
         */
        showPic : function showPic(picId) {
            this.execute(picId, 'show');
            return this;
        },

        /**
         * Hides a PIC provided by the running item.
         *
         * @param {String} picId The PIC typeIdentifier or serial
         * @returns {picManagerCollection}
         */
        hidePic : function hidePic(picId) {
            this.execute(picId, 'hide');
            return this;
        },

        /**
         * Enables all PIC provided by the running item.
         *
         * @param {Function} [filter] An optional filter to reduce the list of PIC to enable
         * @returns {picManagerCollection}
         */
        enableAll : function enableAll(filter) {
            this.executeAll('enable', filter);
            return this;
        },

        /**
         * Disables all PIC provided by the running item.
         *
         * @param {Function} [filter] An optional filter to reduce the list of PIC to disable
         * @returns {picManagerCollection}
         */
        disableAll : function disableAll(filter) {
            this.executeAll('disable', filter);
            return this;
        },

        /**
         * Shows all PIC provided by the running item.
         *
         * @param {Function} [filter] An optional filter to reduce the list of PIC to show
         * @returns {picManagerCollection}
         */
        showAll : function showAll(filter) {
            this.executeAll('show', filter);
            return this;
        },

        /**
         * Hides all PIC provided by the running item.
         *
         * @param {Function} [filter] An optional filter to reduce the list of PIC to hide
         * @returns {picManagerCollection}
         */
        hideAll : function hideAll(filter) {
            this.executeAll('hide', filter);
            return this;
        }
    };

    /**
     * Creates a PIC manager for a particular Item.
     * @param {Object} pic
     * @param {QtiItem} item
     * @returns {picManager} Returns the instance of the PIC manager
     */
    var managerFactory = function managerFactory(pic, item) {
        var manager = _.clone(picManager, true);
        return manager.init(pic, item);
    };

    /**
     * Creates a PIC manager for a particular Item.
     * @param {QtiItem} item
     * @returns {picManager} Returns the instance of the PIC manager
     */
    var collectionFactory = function collectionFactory(item) {
        var collection = _.clone(picManagerCollection, true);
        return collection.init(item);
    };

    return {
        collection: collectionFactory,
        manager: managerFactory
    };
});

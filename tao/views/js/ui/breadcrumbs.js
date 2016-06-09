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
    'ui/component',
    'tpl!ui/breadcrumbs/tpl/breadcrumbs'
], function ($, _, __, component, breadcrumbsTpl) {
    'use strict';

    /**
     * Defines a breadcrumbs component
     * @type {Object}
     */
    var breadcrumbs = {
        /**
         * Updates the component with a new set of entries
         * @param {Array} breadcrumbs
         * @param {String} [breadcrumbs.id] - The identifier of the breadcrumb
         * @param {String} [breadcrumbs.url] - The URL targeted by the breadcrumb
         * @param {String} [breadcrumbs.label] - The displayed label
         * @param {String} [breadcrumbs.data] - An extra label to display, usually related to the current context
         * @param {Array} [breadcrumbs.entries] - A list of parallels links
         * @param {Array} [breadcrumbs.cls] - CSS class to add to the container
         * @returns {jQuery}
         */
        update : function update(breadcrumbs) {
            var $oldComponent = this.getContainer();
            var $component;

            this.config.breadcrumbs = breadcrumbs;

            /**
             * Notifies the update
             * @event breadcrumbs#update
             * @param {breadcrumbs} breadcrumbs
             */
            this.trigger('update', breadcrumbs, this);

            $component = this.render();

            if ($oldComponent) {
                if (!this.config.renderTo) {
                    $oldComponent.replaceWith($component);
                } else if (!this.config.replace) {
                    $oldComponent.remove();
                }
            }

            return $component;
        }
    };

    /**
     * Remove the link from the last crumb
     */
    var removeLastLink = function removeLastLink() {
        var breadcrumbs = this.config.breadcrumbs;

        if (breadcrumbs && breadcrumbs.length) {
            breadcrumbs = _.cloneDeep(this.config.breadcrumbs);
            breadcrumbs[breadcrumbs.length - 1].url = null;
            this.config.breadcrumbs = breadcrumbs;
        }
    };

    /**
     * Builds an instance of the breadcrumbs component
     * @param {Object} config
     * @param {Array} [config.breadcrumbs] - The list of entries to display
     * @param {jQuery|HTMLElement|String} [config.renderTo] - An optional container in which renders the component
     * @param {Boolean} [config.replace] - When the component is appended to its container, clears the place before
     * @returns {breadcrumbs}
     */
    var breadcrumbsFactory = function breadcrumbsFactory(config) {
        return component(breadcrumbs)
                .on('init', removeLastLink)
                .on('update', removeLastLink)
                .setTemplate(breadcrumbsTpl)
                .init(config);
    };

    return breadcrumbsFactory;
});

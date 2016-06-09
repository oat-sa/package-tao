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
 * Copyright (c) 2015 (original work) Open Assessment Technologies;
 *               
 */  
define(['IMSGlobal/jquery_2_1_1', 'OAT/lodash'], function ($, _) {
    'use strict';
    return function ($container, options) {
        var that = this,
            currentTabIndex,
            $tabs,
            $pages,
            defaultOptions = {
                buttonClass : 'tr-tab',
                activeButtonClass : 'tr-active-tab',
                tabsSelector : '.js-tab-buttons li',
                pagesSelector : '.js-tab-content',
                tabButtonSelectior : '.tr-tab-label',
                afterSelect : _.noop(),
                beforeSelect : _.noop(),
                afterCreate : _.noop(),
                beforeCreate : _.noop(),
                initialPageIndex : 0 //active tab index after initialize
            };

        /**
         * Function initializes tabs. 
         * <b>options.beforeCreate</b> and <b>options.afterCreate</b> callbacks will be invoked.
         * After creating will be selected the tab specified in <b>options.initialPageIndex</b> 
         * (<b>options.beforeCreate</b> and <b>options.afterCreate</b> callbacks also will be invoked)
         * @returns {undefined}
         */
        this.init = function () {
            if (_.isFunction(options.beforeCreate)) {
                options.beforeCreate.call(that);
            }

            options = _.extend(defaultOptions, _.clone(options));

            $tabs = $container.find(options.tabsSelector);
            $pages = $container.find(options.pagesSelector);
            currentTabIndex = options.initialPageIndex;

            this.index(currentTabIndex);

            $tabs.on('click', options.tabButtonSelectior, function () {
                var index = $tabs.index($(this).closest(options.tabsSelector));
                that.index(index);
            });

            if (_.isFunction(options.afterCreate)) {
                options.afterCreate.call(that);
            }
        };

        /**
         * Function returns current tab index if <b>index</b> parameter was not passed. 
         * Otherwise will be selected appropriate tab.
         * @param {integer} [index] - tab index to select.
         * @returns {integer} - Active tab index.
         */
        this.index = function (index) {
            if (index === undefined) {
                return currentTabIndex;
            }

            index = parseInt(index, 10);

            if (_.isFunction(options.beforeSelect)) {
                options.beforeSelect.call(that, index);
            }
            currentTabIndex = index;

            $tabs.removeClass(options.activeButtonClass).addClass(options.buttonClass);
            $tabs.eq(index).addClass(options.activeButtonClass);

            $pages.hide();
            $pages.eq(index).show();

            if (_.isFunction(options.afterSelect)) {
                options.afterSelect.call(that, index);
            }
            return currentTabIndex;
        };

        /**
         * Function returns number of tabs.
         * @returns {integer}
         */
        this.countTabs = function () {
            return $tabs.length;
        };

        this.init();
    };
});
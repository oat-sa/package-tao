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
    'lodash',
    'i18n',
    'taoQtiTest/testRunner/actionBar/button'
], function (_, __, button) {
    'use strict';

    /**
     * Defines an action bar button that mark for review the current assessment item
     * @type {Object}
     */
    var markForReview = {
        /**
         * Additional setup onto the button config set
         */
        setup : function setup() {
            _.defaults(this.config, {
                label : __('Mark for review'),
                icon : 'anchor'
            });
        },

        /**
         * Additional DOM rendering
         */
        afterRender : function afterRender() {
            var itemFlagged = this.testContext && this.testContext.itemFlagged;
            this.setActive(itemFlagged);
        },

        /**
         * Action called when the button is clicked
         */
        action : function action() {
            var testContext = this.testContext;
            var testRunner = this.testRunner;
            var itemFlagged;

            if (testContext) {
                itemFlagged = !testContext.itemFlagged;
                testContext.itemFlagged = itemFlagged;

                if (testRunner) {
                    testRunner.markForReview(itemFlagged, testContext.itemPosition);
                    this.setActive(itemFlagged);
                }
            }
        },

        /**
         * Tells if the button is visible and can be rendered
         * @returns {Boolean}
         */
        isVisible : function isVisible() {
            var testContext = this.testContext;
            
            return testContext &&
                !!testContext.reviewScreen &&
                !!testContext.navigatorMap &&
                !!testContext.considerProgress &&
                (_.indexOf(testContext.categories, 'x-tao-option-markReview') >= 0);
        }
    };

    return button(markForReview);
});

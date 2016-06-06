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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */
define([
    'jquery',
    'lodash',
    'util/adaptSize',
    'ui/waitForMedia'
], function ($, _, adaptSize, waitForMedia) {

    'use strict';

    return {

        /**
         * Resize jQueryElement that have changed their dimensions due to a change of the content
         *
         * @param {jQueryElement|widget} target
         */
        adaptSize: function (target) {

            var $elements;
            var $container;

            switch (true) {
                // widget
                case (typeof target.$container !== 'undefined'):
                    $elements = target.$container.find('.add-option, .result-area .target, .choice-area .qti-choice');
                    $container = target.$container;
                    break;

                // jquery elements
                default:
                    $elements = target;
                    $container = $($elements).first().parent();
            }

            $container.waitForMedia(function () {
                adaptSize.height($elements);
            });
        }
    };
});

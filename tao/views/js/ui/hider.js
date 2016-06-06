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
 * This helps you to show hide elements in a more reliable way than using jQuery.show/hide methods
 * by simply using a css class that has the display: none property in TAO.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'core/eventifier'
], function ($, _) {
    'use strict';

    var hiddenClass = 'hidden';

    /**
     * Wrap an element to ensure it's a jquery elt.
     * @private
     * @param {jQueryElement|HTMLElement|String} element - the element/node/selector
     * @returns {jQueryElement} the element
     */
    function jqWrap(element){
        return element instanceof $ ? element : $(element);
    }

    /**
     * The hider object
     */
    return {

        /**
         * Show the given element (let say unhide)
         * @param {jQueryElement|HTMLElement|String} element - the element/node/selector
         * @returns {jQueryElement} the element
         */
        show : function show(element){
            return jqWrap(element).removeClass(hiddenClass);
        },

        /**
         * Hide the given element
         * @param {jQueryElement|HTMLElement|String} element - the element/node/selector
         * @returns {jQueryElement} the element
         */
        hide : function hide(element){
            return jqWrap(element).addClass(hiddenClass);
        },

        /**
         * Toggle (show if hidden / hide if shown) the given element
         * @param {jQueryElement|HTMLElement|String} element - the element/node/selector
         * @returns {jQueryElement} the element
         */
        toggle : function toggle(element){
            return jqWrap(element).toggleClass(hiddenClass);
        },


        /**
         * Check whether the given element is hidden
         * @param {jQueryElement|HTMLElement|String} element - the element/node/selector
         * @param {Boolean} [real = false] - if the check takes in account the real display/visibility status.
         * @returns {jQueryElement} the element
         */
        isHidden : function isHidden(element, real){
            var $elt = jqWrap(element);
            if(!real){
                return $elt.hasClass(hiddenClass);
            }
            return $elt.hasClass(hiddenClass) || $elt.css('display') === 'none' || $elt.css('visibility') === 'hidden';
        }
    };
});

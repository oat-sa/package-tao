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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */

/**
 * IdentifiedElement model
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/helper/util'
], function(Element, util){
    'use strict';

    /**
     * IdentifiedElement model
     */
    var IdentifiedElement = Element.extend({

        /**
         * Generates and assign an identifier
         * @param {String} prefix - identifier prefix
         * @param {Boolean} [useSuffix = true] - add a "_ + index" to the identifier
         * @returns {Object} for chaining
         */
        buildIdentifier : function buildIdentifier(prefix, useSuffix){
            var item = this.getRelatedItem();
            var id = util.buildIdentifier(item, prefix, useSuffix);
            if(id){
                this.attr('identifier', id);
            }
            return this;
        },

        /**
         * Get/set and identifier. It will be generated if it doesn't exists.
         * @param {String} [value] - set the value or get it if not set.
         * @returns {String} the identifier
         */
        id : function id(value){
            if(!value && !this.attr('identifier')){
                this.buildIdentifier(this.qtiClass, true);
            }
            return this.attr('identifier', value);
        }
    });

    /**
     * @exports taoQtiItem/qtiItem/core/IdentifiableElement
     */
    return IdentifiedElement;
});


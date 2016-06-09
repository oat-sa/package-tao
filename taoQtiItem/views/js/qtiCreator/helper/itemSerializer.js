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
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['lodash'], function(_){
    'use strict';

    /**
     * Helps you to create a JSON representation of an item
     * @exports taoQtiItem/qtiCreator/helper/itemSerializer
     */
    var itemSerializer = {

        /**
         * Serialize an item object to JSON
         * @param {Object} item - the {@link taoQtiItem/qtiCreator/model/Item} to serialize
         * @returns {String} the JSON string
         */
        serialize : function(item){
           var serialized = '';
           if(item){
               try {
                    //clone and serialize the cleaned up value
                    serialized = JSON.stringify(item.toArray());
                } catch(e){
                    console.error(e);
                }
            }
            return serialized;
        }
    };

    return itemSerializer;
});

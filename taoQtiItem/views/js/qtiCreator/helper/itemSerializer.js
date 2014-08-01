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

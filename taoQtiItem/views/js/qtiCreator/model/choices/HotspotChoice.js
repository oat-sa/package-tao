/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash', 
    'taoQtiItem/qtiCreator/model/mixin/editable', 
    'taoQtiItem/qtiItem/core/choices/HotspotChoice'
], function(_, editable, Choice){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        
        /**
         * Set the default values for the model
         * @returns {Object} the default attributes 
         */ 
        getDefaultAttributes : function(){
            return {};
        }
    });
    return Choice.extend(methods);
});



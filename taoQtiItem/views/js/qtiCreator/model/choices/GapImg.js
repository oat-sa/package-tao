/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash', 
    'taoQtiItem/qtiCreator/model/mixin/editable', 
    'taoQtiItem/qtiItem/core/choices/GapImg'
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
            return {
                matchMin : 0,
                matchMax : 0  
            };
        }
    });
    return Choice.extend(methods);
});



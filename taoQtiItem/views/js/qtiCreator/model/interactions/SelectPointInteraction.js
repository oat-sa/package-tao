/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/SelectPointInteraction'
], function($, _, editable, editableInteraction, Interaction){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        
        /**
         * Set the default values for the model
         * @returns {Object} the default attributes 
         */ 
        getDefaultAttributes : function(){
            return {
                'maxChoices' : 0,
                'minChoices' : 0
            };
        },

        /**
         * Once the interaction model is created, 
         * we set the responsivness and create a default response 
         */ 
        afterCreate : function(){
            var relatedItem = this.getRelatedItem();
            var isResponsive = relatedItem.data('responsive');

            if(isResponsive === true){
                this.addClass('responsive');
            }
            this.createResponse({
                baseType:'point',
                cardinality:'multiple'
            }, 'MAP_RESPONSE_POINT');
        }
    });
    return Interaction.extend(methods);
});



/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/GraphicAssociateInteraction',
    'taoQtiItem/qtiCreator/model/choices/AssociableHotspot'
], function($, _, editable, editableInteraction, Interaction, AssociableHotspot){
    'use strict';

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
                'maxAssociations' : 0,
                'minAssociations' : 0
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
                baseType:'pair',
                cardinality:'multiple'
            });
        },


        /**
         * Create a choice for the interaction
         * @param {Object} attr - the choice attributes
         * @returns {Object} the created choice
         */
        createChoice : function(attr){
            var choice = new AssociableHotspot('', attr);

            this.addChoice(choice);
            choice.buildIdentifier('associablehotspot');
            
            if(this.getRenderer()){
                choice.setRenderer(this.getRenderer());
            }
            
            $(document).trigger('choiceCreated.qti-widget', {'choice' : choice, 'interaction' : this});
           
            return choice;
        }
    });

    return Interaction.extend(methods);
});



define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/TextEntryInteraction'
], function(_, editable, editableInteraction, Interaction){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                base : 10,
                placeholderText : ''
            };
        },
        afterCreate : function(){
            this.createResponse({
                baseType:'string',
                cardinality:'single'
            });
        },
        createChoice : function(){
            throw new Error('text entry interaction has no choice');
        }
    });
    return Interaction.extend(methods);
});
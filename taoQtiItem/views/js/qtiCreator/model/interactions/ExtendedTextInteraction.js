define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/ExtendedTextInteraction'
], function(_, editable, editableInteraction, Interaction){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                format: 'plain'
            };
        },
        afterCreate : function(){
            this.createResponse({
                baseType: 'string',
                cardinality: 'single'
            });
        },
        createChoice : function(){
            throw "extendedTextInteraction does not have any choices";
        }
    });
    return Interaction.extend(methods);
});



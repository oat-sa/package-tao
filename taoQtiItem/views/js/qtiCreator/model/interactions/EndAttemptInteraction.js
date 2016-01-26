define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/EndAttemptInteraction',
    'i18n'
], function(_, editable, editableInteraction, Interaction, __){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                title : __('End Attempt')
            };
        },
        afterCreate : function(){
            this.createResponse({
                baseType:'boolean',
                cardinality:'single'
            });
        },
        createChoice : function(){
            throw new Error('end attempt interaction has no choice');
        }
    });
    return Interaction.extend(methods);
});
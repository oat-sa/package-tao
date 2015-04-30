define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/InlineChoiceInteraction',
    'taoQtiItem/qtiCreator/model/choices/InlineChoice'
], function($, _, editable, editableInteraction, Interaction, Choice){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                'shuffle' : false,
                'required' : false
            };
        },
        afterCreate : function(){
            this.createChoice();
            this.createChoice();
            this.createChoice();
            this.createResponse({
                baseType:'identifier',
                cardinality:'single'
            });
        },
        createChoice : function(){
            var choice = new Choice();

            this.addChoice(choice);

            var rank = _.size(this.getChoices());

            choice
                .val('choice' + ' #' + rank)
                .buildIdentifier('choice');

            if(this.getRenderer()){
                choice.setRenderer(this.getRenderer());
            }

            $(document).trigger('choiceCreated.qti-widget', {'choice' : choice, 'interaction' : this});

            return choice;
        }
    });
    return Interaction.extend(methods);
});



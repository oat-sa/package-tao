define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/OrderInteraction',
    'taoQtiItem/qtiCreator/model/choices/SimpleChoice'
], function($, _, editable, editableInteraction, Interaction, Choice){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                shuffle : false,
                orientation : 'vertical'
            };
        },
        afterCreate : function(){
            this.createChoice();
            this.createChoice();
            this.createChoice();
            this.createResponse({
                baseType:'identifier',
                cardinality:'ordered'
            });
        },
        createChoice : function(){
            var choice = new Choice();

            this.addChoice(choice);

            var rank = _.size(this.getChoices());

            choice
                .body('choice' + ' #' + rank)
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



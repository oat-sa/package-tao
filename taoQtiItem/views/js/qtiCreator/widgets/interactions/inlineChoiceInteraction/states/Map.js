define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiItem/helper/interactionHelper',
    'lodash',
], function($, stateFactory, Map, formElement, interactionHelper, _){

    var AssociateInteractionStateCorrect = stateFactory.create(Map, function(){

        var _widget = this.widget,
            $container = _widget.$container,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        $container.find('[data-edit=map]').show();

        //init correct response radio group:
        var correct = _.values(response.getCorrect());
        if(correct.length){
            var selectedChoice = interaction.getChoiceByIdentifier(correct.pop());
            if(selectedChoice){
                $container.find('input[name=correct][value="' + selectedChoice.serial + '"]').prop('checked', true);
            }
        }

        formElement.setChangeCallbacks($container, response, {
            correct : function(response, value){
                response.setCorrect(interactionHelper.serialToIdentifier(interaction, value));
            },
            score : function(response, value){
                var score = parseFloat(value);
                if(!isNaN(score)){
                    response.setMapEntry(interactionHelper.serialToIdentifier(interaction, $(this).data('for')), score);
                }
            }
        });

    }, function(){

    });


    return AssociateInteractionStateCorrect;
});

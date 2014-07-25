define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/ResponseWidget',
    'lodash'
], function(stateFactory, Map, responseWidget, _){

    var ChoiceInteractionStateMap = stateFactory.create(Map, function(){

        var _widget = this.widget,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        //init response widget in responseMapping mode:
        responseWidget.create(_widget, true);

        //finally, apply defined correct response and response mapping:
        responseWidget.setResponse(interaction, _.values(response.getCorrect()));

    }, function(){

        responseWidget.destroy(this.widget);

    });

    return ChoiceInteractionStateMap;
});
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/ResponseWidget',
    'lodash'
], function(stateFactory, Correct, responseWidget, _){

    var MatchInteractionStateCorrect = stateFactory.create(Correct, function(){

        var _widget = this.widget,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        //init response widget in responseMapping mode:
        responseWidget.create(_widget);

        //finally, apply defined correct response and response mapping:
        responseWidget.setResponse(interaction, _.values(response.getCorrect()));

    }, function(){

        responseWidget.destroy(this.widget);

    });

    return MatchInteractionStateCorrect;
});

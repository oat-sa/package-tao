define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/ResponseWidget',
    'lodash'
], function($, stateFactory, Map, responseWidget, _){

    var MatchInteractionStateMap = stateFactory.create(Map, function(){

        var _widget = this.widget,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        //init response widget in responseMapping mode:
        responseWidget.create(_widget, true);

        //finally, apply defined correct response and response mapping:
        responseWidget.setResponse(interaction, _.values(response.getCorrect()));
        
       //change the correct state 
       _widget.on('metaChange', function(meta){
            if(meta.key === 'defineCorrect'){
                if(meta.value){
                    $('.match-interaction-area input[type="checkbox"]', _widget.$container)
                        .removeProp('disabled') 
                        .removeClass('disabled');
                } else {
                    $('.match-interaction-area input[type="checkbox"]', _widget.$container)
                        .prop('disabled', true)
                        .prop('checked', false)
                        .addClass('disabled');
                }
                if(meta.value === false){
                    response.setCorrect([]);
                }
            }
        });

    }, function(){

        responseWidget.destroy(this.widget);

    });

    return MatchInteractionStateMap;
});
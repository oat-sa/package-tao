define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/states/states'
], function(Widget, states){

    var MatchInteractionWidget = Widget.clone();

    MatchInteractionWidget.initCreator = function(){
        this.registerStates(states);
        Widget.initCreator.call(this);
    };
    
    return MatchInteractionWidget;
});
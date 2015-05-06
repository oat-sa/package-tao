define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/gapMatchInteraction/states/states'
], function(Widget, states){

    var GapMatchInteractionWidget = Widget.clone();

    GapMatchInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    return GapMatchInteractionWidget;
});

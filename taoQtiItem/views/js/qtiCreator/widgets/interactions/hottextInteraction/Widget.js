define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/hottextInteraction/states/states'
], function(Widget, states){

    var HottextInteractionWidget = Widget.clone();

    HottextInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    return HottextInteractionWidget;
});
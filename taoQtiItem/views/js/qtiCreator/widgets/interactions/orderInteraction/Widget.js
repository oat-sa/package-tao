define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/orderInteraction/states/states'
], function(Widget, states){

    var OrderInteractionWidget = Widget.clone();

    OrderInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    return OrderInteractionWidget;
});
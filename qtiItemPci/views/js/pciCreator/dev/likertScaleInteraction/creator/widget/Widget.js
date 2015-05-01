define([
    'taoQtiItem/qtiCreator/widgets/interactions/customInteraction/Widget',
    'likertScaleInteraction/creator/widget/states/states'
], function(Widget, states){

    var LikertScaleInteractionWidget = Widget.clone();

    LikertScaleInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    return LikertScaleInteractionWidget;
});
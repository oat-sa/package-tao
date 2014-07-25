define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/mediaInteraction/states/states'
], function(Widget, states){
    
    var MediaInteractionWidget = Widget.clone();

    MediaInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
        
    };
    
    return MediaInteractionWidget;
});

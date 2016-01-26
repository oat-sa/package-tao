define([
    'taoQtiItem/qtiCreator/widgets/interactions/customInteraction/Widget',
    'liquidsInteraction/creator/widget/states/states'
], function(Widget, states){

    var LiquidsInteractionWidget = Widget.clone();

    LiquidsInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    return LiquidsInteractionWidget;
});
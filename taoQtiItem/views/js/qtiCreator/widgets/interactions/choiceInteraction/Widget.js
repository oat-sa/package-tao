define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/states/states'
], function(Widget, states){

    var ChoiceInteractionWidget = Widget.clone();

    ChoiceInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    return ChoiceInteractionWidget;
});
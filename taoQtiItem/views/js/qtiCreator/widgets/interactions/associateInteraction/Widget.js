define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/helper'
], function(Widget, states, helper){

    var AssociateInteractionWidget = Widget.clone();

    AssociateInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
        
        helper.adaptSize(this);
    };
    
    return AssociateInteractionWidget;
});
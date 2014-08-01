define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Choice',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/helper'
], function(stateFactory, Choice, helper){

    var AssociateInteractionStateChoice = stateFactory.extend(Choice, function(){
        
        var widget = this.widget;
        
        widget.on('containerBodyChange contentChange choiceCreated', function(){
            helper.adaptSize(widget);
        });
        
    }, function(){
        
    });

    return AssociateInteractionStateChoice;
});

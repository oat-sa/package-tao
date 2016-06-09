define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Answer'
], function(stateFactory, Answer){

    var OrderInteractionStateAnswer = stateFactory.extend(Answer, function(){
        
        //currently only allow "correct" state
        this.widget.changeState('correct');
        
    }, function(){
        
    });

    return OrderInteractionStateAnswer;
});
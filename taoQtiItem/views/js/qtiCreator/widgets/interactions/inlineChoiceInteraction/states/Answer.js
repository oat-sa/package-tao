define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState'
], function(stateFactory, Answer, answerStateHelper){

    var InlineChoiceInteractionStateAnswer = stateFactory.extend(Answer, function(){
        answerStateHelper.forward(this.widget);
    }, function(){
        
    });
    
    return InlineChoiceInteractionStateAnswer;
});
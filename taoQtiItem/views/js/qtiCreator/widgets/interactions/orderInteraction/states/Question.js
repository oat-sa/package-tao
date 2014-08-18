define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/states/Question'
], function(stateFactory, Question, ChoiceInteractionQuestionState){

    var OrderInteractionStateQuestion = stateFactory.extend(Question);
    
    //reuse the same exact same form as choice interaction
    OrderInteractionStateQuestion.prototype.initForm = function(){
         ChoiceInteractionQuestionState.prototype.initForm.call(this, false);
    };

    return OrderInteractionStateQuestion;
});
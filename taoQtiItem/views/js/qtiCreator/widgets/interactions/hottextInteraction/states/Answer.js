define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState'
], function(stateFactory, Answer, answerStateHelper){

    var HottextInteractionStateAnswer = stateFactory.extend(Answer, function(){
        
        this.widget.$original.find('.hottext-checkmark > input').removeProp('disabled');
        
        //forward to one of the available sub state, according to the response processing template
        answerStateHelper.forward(this.widget);
        
    }, function(){
        
        this.widget.$original.find('.hottext-checkmark > input').prop('disabled', 'disabled');
        
    });
    
    return HottextInteractionStateAnswer;
});
/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState'
], function(stateFactory, Answer, answerStateHelper){

    /**
     * Just forward to the correct/map states
     */
    function initAnswerState(){
        answerStateHelper.forward(this.widget);
    }
    
    
    function exitAnswerState(){
        //needed an exit callback even empty
    }
    
    
    /**
     * The answer state for the hotspot interaction
     * @extends taoQtiItem/qtiCreator/widgets/interactions/states/Answer
     * @exports taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Answer
     */
    return stateFactory.extend(Answer, initAnswerState, exitAnswerState);
});

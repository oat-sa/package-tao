define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/media',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
], function(stateFactory, Answer, answerStateHelper, formTpl, formElement){

    //does not much sense for mediaInteraction (same remark for uploadInteraction)
    //I would recommend disabling the answer button completely and remove this file if no longer required
    
    var MediaInteractionStateAnswer = stateFactory.extend(Answer, function(){
        var _widget = this.widget,
            interaction = _widget.element;
        
        //forward to one of the available sub state, according to the response processing template
        //answerStateHelper.forward(_widget);
        
    }, function(){
        var _widget = this.widget;
        
    });
    
    
    
    MediaInteractionStateAnswer.prototype.initResponseForm = function(){
        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

    };
    
    
    return MediaInteractionStateAnswer;
});
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/SliderInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'lodash',
    'i18n'
], function(stateFactory, Correct, commonRenderer, instructionMgr, _, __){

    var SliderInteractionStateCorrect = stateFactory.create(Correct, function(){
        
        _createResponseWidget(this.widget);
        
    }, function(){
        
        _destroyResponseWidget(this.widget);
        
    });
    
    var _createResponseWidget = function(widget){

        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var correctResponse = _.values(response.getCorrect());

        commonRenderer.setResponse(interaction, _formatResponse(correctResponse));
        
        var $sliderElt = widget.$container.find('.qti-slider');
        $sliderElt.removeAttr('disabled');

        instructionMgr.appendInstruction(interaction, __('Please define the correct response using the slider.'));

        widget.$container.on('responseChange.qti-widget', function(e, data){
            response.setCorrect(_unformatResponse(data.response));
        });
    };
    
    var _destroyResponseWidget = function(widget){
        
        var $sliderElt = widget.$container.find('.qti-slider');
        var lowerBound = widget.element.attributes.lowerBound;
        
        $sliderElt.attr('disabled', 'disabled');
        $sliderElt.val(lowerBound);
        widget.$container.find('span.qti-slider-cur-value').text('' + lowerBound);
        
        instructionMgr.removeInstructions(widget.element);
        widget.$container.off('responseChange.qti-widget');
    };
    
    var _formatResponse = function(response){
        return {"base" : {"integer" : response}};
    };

    var _unformatResponse = function(formatedResponse){
        return [formatedResponse.base.integer];
    };
    
    return SliderInteractionStateCorrect;
});

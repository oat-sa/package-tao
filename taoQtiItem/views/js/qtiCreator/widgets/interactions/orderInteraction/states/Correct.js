define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/OrderInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'lodash',
    'i18n'
], function(stateFactory, Correct, commonRenderer, instructionMgr, _, __){

    var InlineChoiceInteractionStateCorrect = stateFactory.create(Correct, function(){
        
        _createResponseWidget(this.widget);
        
    }, function(){
        
        _destroyResponseWidget(this.widget);
        
    });
    
    var _createResponseWidget = function(widget){

        var interaction = widget.element,
            response = interaction.getResponseDeclaration(),
            correctResponse = _.values(response.getCorrect());

        instructionMgr.appendInstruction(widget.element, __('Please define the correct order in the box to the right.'));

        commonRenderer.render(widget.element);
        commonRenderer.setResponse(interaction, _formatResponse(correctResponse));

        widget.$container.on('responseChange.qti-widget', function(e, data){
            response.setCorrect(_unformatResponse(data.response));
        });
    };
    
    var _destroyResponseWidget = function(widget){
        
        widget.$container.off('responseChange.qti-widget');

        commonRenderer.resetResponse(widget.element);

        commonRenderer.destroy(widget.element);
    };
    
    var _formatResponse = function(response){
        return {list : {identifier : response}};
    };

    var _unformatResponse = function(formatedResponse){
        var res = [];
        if(formatedResponse.list && formatedResponse.list.identifier){
            res = formatedResponse.list.identifier;
        }
        return res;
    };
    
    return InlineChoiceInteractionStateCorrect;
});

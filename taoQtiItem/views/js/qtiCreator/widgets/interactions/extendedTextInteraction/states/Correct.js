define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/ExtendedTextInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager'
], function(_, __,stateFactory, Correct, renderer, instructionMgr){

    var ExtendedTextInteractionStateCorrect = stateFactory.create(Correct, function(){

        _createResponseWidget(this.widget);

    }, function(){

        _destroyResponseWidget(this.widget);

    });

    var _createResponseWidget = function(widget){

        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var correctResponse = _.values(response.getCorrect());

        renderer.enable(interaction);
        renderer.setText(interaction, correctResponse[0]);
       
        instructionMgr.appendInstruction(interaction, __('Please type the correct response below.'));
        
        widget.$container.on('responseChange.qti-widget', function(e, data){
            response.setCorrect([renderer.getResponse(interaction).base.string]);
        });
    };

    var _destroyResponseWidget = function(widget){

        var interaction = widget.element;
        renderer.clearText(interaction);
        
        instructionMgr.removeInstructions(widget.element);
        widget.$container.off('responseChange.qti-widget');
    };

    return ExtendedTextInteractionStateCorrect;
});

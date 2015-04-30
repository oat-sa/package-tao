define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'lodash',
    'i18n'
], function($, stateFactory, Correct, instructionMgr, _, __){

    var TextEntryInteractionStateCorrect = stateFactory.create(Correct, function(){

        var $container = this.widget.$container,
            response = this.widget.element.getResponseDeclaration(),
            correct = _.values(response.getCorrect()).pop() || '';

        $container.find('tr[data-edit=correct] input[name=correct]').focus().val(correct);
        $container.on('keyup.correct', 'tr[data-edit=correct] input[name=correct]', function(){
            var value = $(this).val();
            response.setCorrect(value);
        });
        instructionMgr.appendInstruction(this.widget.element, __('Please type the correct response in the box below.'));

    }, function(){

        this.widget.$container.off('.correct');
        instructionMgr.removeInstructions(this.widget.element);

    });

    return TextEntryInteractionStateCorrect;
});

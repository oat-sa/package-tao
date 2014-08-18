define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'lodash'
], function($, stateFactory, Correct, _){

    var TextEntryInteractionStateCorrect = stateFactory.create(Correct, function(){

        var $container = this.widget.$container,
            response = this.widget.element.getResponseDeclaration(),
            correct = _.values(response.getCorrect()).pop() || '';
            
        $container.find('tr[data-edit=correct] input[name=correct]').focus().val(correct);
        $container.on('keyup.correct', 'tr[data-edit=correct] input[name=correct]', function(){
            var value = $(this).val();
            response.setCorrect(value);
        });

    }, function(){

        this.widget.$container.off('.correct');
    });

    return TextEntryInteractionStateCorrect;
});

define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/textEntry'
], function(stateFactory, Question, formElement, formTpl){

    var TextEntryInteractionStateQuestion = stateFactory.extend(Question);

    TextEntryInteractionStateQuestion.prototype.initForm = function(){
        
        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

        $form.html(formTpl({
            base : parseInt(interaction.attr('base')),
            patternMask : interaction.attr('patternMask'),
            placeholderText : interaction.attr('placeholderText'),
            expectedLength : parseInt(interaction.attr('expectedLength'))
        }));

        formElement.initWidget($form);

        formElement.setChangeCallbacks($form, interaction, {
            base : formElement.getAttributeChangeCallback(),
            patternMask : formElement.getAttributeChangeCallback(),
            placeholderText : formElement.getAttributeChangeCallback(),
            expectedLength : formElement.getAttributeChangeCallback()
        });
    };

    return TextEntryInteractionStateQuestion;
});
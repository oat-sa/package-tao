define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/choice',
    'lodash'
], function(stateFactory, Question, formElement, interactionFormElement, formTpl, _){

    var ChoiceInteractionStateQuestion = stateFactory.extend(Question);

    ChoiceInteractionStateQuestion.prototype.initForm = function(updateCardinality){

        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

        $form.html(formTpl({
            shuffle : !!interaction.attr('shuffle'),
            maxChoices : parseInt(interaction.attr('maxChoices')),
            minChoices : parseInt(interaction.attr('minChoices')),
            choicesCount : _.size(_widget.element.getChoices())
        }));

        formElement.initWidget($form);
        
        //init data change callbacks
        var callbacks = formElement.getMinMaxAttributeCallbacks(this.widget.$form, 'minChoices', 'maxChoices', updateCardinality);
        callbacks['shuffle'] = formElement.getAttributeChangeCallback();
        formElement.setChangeCallbacks($form, interaction, callbacks);
        
        interactionFormElement.syncMaxChoices(_widget);
        
        //modify the checkbox/radio input appearances
        _widget.on('attributeChange', function(data){
            
            var $checkboxIcons = _widget.$container.find('.real-label > span');
            
            if(data.element.serial === interaction.serial && data.key === 'maxChoices'){
                if(parseInt(data.value) === 1){
                    //radio
                    $checkboxIcons.removeClass('icon-checkbox').addClass('icon-radio');
                }else{
                    //checkbox
                    $checkboxIcons.removeClass('icon-radio').addClass('icon-checkbox');
                }
            }
        });
    };

    return ChoiceInteractionStateQuestion;
});

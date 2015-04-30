define([
    'lodash',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/containerInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/hottext',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/hottext-create'
], function(_, stateFactory, Question, formElement, interactionFormElement, formTpl, hottextTpl){

    var HottextInteractionStateQuestion = stateFactory.extend(Question);

    HottextInteractionStateQuestion.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

        $form.html(formTpl({
            maxChoices : interaction.attr('maxChoices'),
            minChoices : interaction.attr('minChoices'),
            choicesCount : _.size(interaction.getChoices())
        }));

        formElement.initWidget($form);

        var callbacks = formElement.getMinMaxAttributeCallbacks($form, 'minChoices', 'maxChoices');
        formElement.setChangeCallbacks($form, interaction, callbacks);
        interactionFormElement.syncMaxChoices(_widget);
    };

    HottextInteractionStateQuestion.prototype.getGapModel = function(){

        return {
            toolbarTpl : hottextTpl,
            qtiClass : 'hottext',
            afterCreate : function(interactionWidget, newHottextWidget, text){
            
                newHottextWidget.element.body(text);
                newHottextWidget.$container.find('.hottext-content').html(text);//add this manually the first time
                newHottextWidget.changeState('choice');
            }
        };
    };

    return HottextInteractionStateQuestion;
});

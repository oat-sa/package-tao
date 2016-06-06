define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/inlineChoice'
], function($, stateFactory, Question, formElement, formTpl){

    var InlineChoiceInteractionStateQuestion = stateFactory.extend(Question, function(){

        var $mainOption = this.widget.$container.find('.main-option'),
            $original = this.widget.$original;
        
        //listener to children choice widget change and update the original interaction placeholder
        $(document).on('choiceTextChange.qti-widget.question', function(){
            $original.width($mainOption.width());
        });

    }, function(){
        
        $(document).off('.qti-widget.question');
    });

    InlineChoiceInteractionStateQuestion.prototype.addNewChoiceButton = function(){

        var _widget = this.widget,
            $addChoice = _widget.$container.find('.add-option'),
            interaction = _widget.element;

        //init add choice button once only
        if(!$addChoice.data('initialized')){

            $addChoice.on('click.qti-widget', function(e){

                e.stopPropagation();

                //add a new choice
                var choice = interaction.createChoice();

                //append render choice:
                $(this).closest('tr').before(_widget.renderChoice(choice));
                _widget.buildChoice(choice, {
                    ready : function(widget){
                        //transition state directly back to "question"
                        widget.changeState('question');
                    }
                });
            });

            //set button as initialized
            $addChoice.data('initialized', true);
        }
    };

    InlineChoiceInteractionStateQuestion.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

        $form.html(formTpl({
            shuffle : !!interaction.attr('shuffle'),
            required : !!interaction.attr('required')
        }));

        formElement.initWidget($form);

        formElement.setChangeCallbacks($form, interaction, {
            shuffle : formElement.getAttributeChangeCallback(),
            required : formElement.getAttributeChangeCallback()
        });
    };

    return InlineChoiceInteractionStateQuestion;
});

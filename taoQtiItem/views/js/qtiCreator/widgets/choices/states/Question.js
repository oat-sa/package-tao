define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Question',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/simpleChoice.content',
    'taoQtiItem/qtiCreator/widgets/choices/helpers/formElement'
], function($, stateFactory, QuestionState, contentToolbarTpl, formElement){

    var ChoiceStateQuestion = stateFactory.create(QuestionState, function(){

        var _widget = this.widget;

        //add some event listeners
        _widget.$container.on('click.question', function(){
            //show option form
            _widget.changeState('choice');
        }).on('mouseenter.question', function(){
            //add listener to display proper hover style
            $(this).addClass('hover');
        }).on('mouseleave.question', function(){
            $(this).removeClass('hover');
        });

        this.createToolbar().show();

    }, function(){

        //remove the question state toolbar properly
        this.removeToolbar();

        //!! very important, always unbind the event on exit!
        this.widget.$container.off('.question');
    });

    ChoiceStateQuestion.prototype.createToolbar = function(){

        var _widget = this.widget,
            $container = _widget.$container,
            choice = _widget.element,
            interaction,
            $toolbar = $container.find('.mini-tlb').not('[data-html-editable] *');

        if(!$toolbar.length){

            interaction = choice.getInteraction();

            //add mini toolbars
            $toolbar = $(contentToolbarTpl({
                choiceSerial : choice.getSerial(),
                interactionSerial : interaction.getSerial(),
                fixed : choice.attr('fixed'),
                interactionShuffle : interaction.attr('shuffle')
            }));

            $container.append($toolbar);

            //set toolbar button behaviour:
            formElement.initShufflePinToggle(_widget);
            formElement.initDelete(_widget);
        }

        return $toolbar;
    };

    ChoiceStateQuestion.prototype.removeToolbar = function(){

        this.widget.$container.find('.mini-tlb[data-edit=question]').remove()
    };

    return ChoiceStateQuestion;
});

define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Question',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/gap',
    'taoQtiItem/qtiCreator/widgets/choices/helpers/formElement'
], function($, stateFactory, QuestionState, gapTpl, formElement){

    var GapStateQuestion = stateFactory.extend(QuestionState);

    GapStateQuestion.prototype.createToolbar = function(){

        var _widget = this.widget,
            $container = _widget.$container,
            gap = _widget.element,
            $toolbar = $container.find('.mini-tlb').not('[data-html-editable] *');

        if(!$toolbar.length){

            //add mini toolbars
            $toolbar = $(gapTpl({
                serial : gap.getSerial(),
                state : 'question'
            }));

            $container.append($toolbar);

            formElement.initDelete(_widget);
        }

        return $toolbar;
    };

    return GapStateQuestion;
});

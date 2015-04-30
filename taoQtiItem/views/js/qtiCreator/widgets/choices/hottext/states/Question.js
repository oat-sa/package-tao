define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Question',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/hottext',
    'taoQtiItem/qtiCreator/widgets/choices/helpers/formElement'
], function($, stateFactory, QuestionState, gapTpl, formElement){

    var HottextStateQuestion = stateFactory.extend(QuestionState);

    HottextStateQuestion.prototype.createToolbar = function(){

        var _widget = this.widget,
            $container = _widget.$container,
            hottext = _widget.element,
            $toolbar = $container.find('.mini-tlb').not('[data-html-editable] *');

        if(!$toolbar.length){

            //add mini toolbars
            $toolbar = $(gapTpl({
                serial : hottext.getSerial(),
                state : 'question'
            }));

            $container.append($toolbar);

            //init delete:
            $toolbar.find('[data-role=restore]').on('mousedown.question', function(){
                $container.replaceWith(hottext.body());
                _widget.destroy();
                hottext.remove();
            });
        }

        return $toolbar;
    };

    return HottextStateQuestion;
});

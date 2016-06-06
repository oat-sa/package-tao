define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Question',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/htmlEditorTrigger',
    'i18n'
], function($, stateFactory, Question, htmlEditor, contentHelper, promptToolbarTpl, __){

    var BlockInteractionStateQuestion = stateFactory.extend(Question, function(){

        this.buildPromptEditor();

    }, function(){

        this.destroyPromptEditor();
    });

    BlockInteractionStateQuestion.prototype.buildPromptEditor = function(){

        var _widget = this.widget,
            $editableContainer = _widget.$container.find('.qti-prompt-container'),
            $editable = $editableContainer.find('.qti-prompt'),
            container = _widget.element.prompt.getBody();

        //@todo set them in the tpl
        $editableContainer.attr('data-html-editable-container', true);
        $editable.attr('data-html-editable', true);

        if(!htmlEditor.hasEditor($editableContainer)){

            var $promptTlb = $(promptToolbarTpl({
                serial : _widget.serial,
                state : 'question'
            }));

            //add toolbar once only:
            $editableContainer.append($promptTlb);
            $promptTlb.show();

            htmlEditor.buildEditor($editableContainer, {
                placeholder : __('define prompt'),
                change : contentHelper.getChangeCallback(container),
                data : {
                    container : container,
                    widget : _widget
                }
            });
        }
    };

    BlockInteractionStateQuestion.prototype.destroyPromptEditor = function(){

        var $editableContainer = this.widget.$container.find('.qti-prompt-container');
        $editableContainer.find('.mini-tlb').remove();
        htmlEditor.destroyEditor($editableContainer);
    };

    return BlockInteractionStateQuestion;
});

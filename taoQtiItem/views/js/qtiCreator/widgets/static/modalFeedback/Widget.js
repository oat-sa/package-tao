define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    'taoQtiItem/qtiCreator/widgets/static/modalFeedback/states/states',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/htmlEditorTrigger',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/okButton',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content'
], function($, Widget, states, toolbarTpl, okButtonTpl, formElement, htmlEditor, contentHelper){

    var ModalFeedbackWidget = Widget.clone();

    ModalFeedbackWidget.initCreator = function(){

        Widget.initCreator.call(this);

        this.registerStates(states);

        this.buildEditor();

        this.createOkButton();
    };

    ModalFeedbackWidget.buildContainer = function(){

        this.$container = this.$original.addClass('widget-box');
    };

    ModalFeedbackWidget.createToolbar = function(){

        this.$container.find('.modal-body').append(toolbarTpl({
            serial : this.serial,
            state : 'active'
        }));

        return this;
    };

    ModalFeedbackWidget.buildEditor = function(){

        var _this = this,
            $editableContainer = _this.$container.find('.modal-body'),
            element = _this.element,
            container = element.getBody();

        $editableContainer.attr('data-html-editable-container', true);

        if(!htmlEditor.hasEditor($editableContainer)){
            htmlEditor.buildEditor($editableContainer, {
                change : contentHelper.getChangeCallback(container),
                data : {
                    container : container,
                    widget : _this
                }
            });
        }

        formElement.initTitle(this.$original.find('.qti-title'), element);
    };

    ModalFeedbackWidget.createOkButton = function(){

        var _this = this;

        this.$container
            .append($(okButtonTpl())
            .on('click.qti-widget', function(e){
                e.stopPropagation();
                _this.changeState('sleep');
            }));
    };

    return ModalFeedbackWidget;
});

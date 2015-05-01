define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/text'
], function(stateFactory, Active, htmlEditor, content, formTpl){

    var TextActive = stateFactory.extend(Active, function(){

        this.buildEditor();
        this.initForm();

    }, function(){

        this.destroyEditor();
        this.widget.$form.empty();
    });

    TextActive.prototype.buildEditor = function(){

        var widget = this.widget,
            $editableContainer = widget.$container,
            container = widget.element;

        $editableContainer.attr('data-html-editable-container', true);

        if(!htmlEditor.hasEditor($editableContainer)){

            htmlEditor.buildEditor($editableContainer, {
                change : content.getChangeCallback(container),
                blur : function(){
                    widget.changeState('sleep');
                },
                data : {
                    widget : widget,
                    container : container
                }
            });
        }
    };

    TextActive.prototype.destroyEditor = function(){
        //search and destroy the editor
        htmlEditor.destroyEditor(this.widget.$container);
    };

    TextActive.prototype.initForm = function(){
        this.widget.$form.html(formTpl());
    };

    return TextActive;
});

define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/rubricBlock',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement'
], function(stateFactory, Active, htmlEditor, content, formTpl, formElement){

    var RubricBlockStateActive = stateFactory.extend(Active, function(){

        this.buildEditor();
        this.initForm();

    }, function(){

        this.destroyEditor();
        this.widget.$form.empty();
    });

    RubricBlockStateActive.prototype.buildEditor = function(){

        var widget = this.widget,
            $editableContainer = widget.$container,
            container = widget.element.getBody();

        $editableContainer.attr('data-html-editable-container', true);

        if(!htmlEditor.hasEditor($editableContainer)){

            htmlEditor.buildEditor($editableContainer, {
                change : content.getChangeCallback(container),
                data : {
                    widget : widget,
                    container : container
                }
            });
        }
    };

    RubricBlockStateActive.prototype.destroyEditor = function(){
        //search and destroy the editor
        htmlEditor.destroyEditor(this.widget.$container);
    };

    RubricBlockStateActive.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

        $form.html(formTpl({
            view : interaction.attr('view'),
            use : interaction.attr('use')
        }));

        formElement.initWidget($form);

        //init data change callbacks
        formElement.setChangeCallbacks($form, interaction, {
            view : formElement.getAttributeChangeCallback(),
            use : formElement.getAttributeChangeCallback()
        });

    };

    return RubricBlockStateActive;
});
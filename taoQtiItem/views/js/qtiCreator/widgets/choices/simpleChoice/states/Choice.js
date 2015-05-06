define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Choice',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/choices/choice',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/helpers/identifier',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content'
], function(stateFactory, Choice, formTpl, formElement, identifierHelper, htmlEditor, contentHelper){

    var SimpleChoiceStateChoice = stateFactory.extend(Choice, function(){
        
        this.buildEditor();
        
    }, function(){
        
        this.destroyEditor();
    });

    SimpleChoiceStateChoice.prototype.initForm = function(){

        var _widget = this.widget;

        //build form:
        _widget.$form.html(formTpl({
            serial : _widget.element.getSerial(),
            identifier : _widget.element.id()
        }));

        formElement.initWidget(_widget.$form);

        //init data validation and binding
        formElement.setChangeCallbacks(_widget.$form, _widget.element, {
            identifier : identifierHelper.updateChoiceIdentifier
        });
    };
    
    SimpleChoiceStateChoice.prototype.buildEditor = function(){

        var _widget = this.widget,
            container = _widget.element.getBody(),
            $editableContainer = _widget.$container;

        //@todo set them in the tpl
        $editableContainer.attr('data-html-editable-container', true);

        if(!htmlEditor.hasEditor($editableContainer)){
            
            htmlEditor.buildEditor($editableContainer, {
                change : contentHelper.getChangeCallback(container),
                data : {
                    container : container,
                    widget : _widget
                }
            });
        }
    };

    SimpleChoiceStateChoice.prototype.destroyEditor = function(){
        
        //search and destroy the editor
        htmlEditor.destroyEditor(this.widget.$container);
    };

    return SimpleChoiceStateChoice;
});
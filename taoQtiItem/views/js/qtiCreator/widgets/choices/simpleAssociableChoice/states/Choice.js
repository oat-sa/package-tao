define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Choice',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/choices/simpleAssociableChoice',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/helpers/identifier',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content'
], function(stateFactory, Choice, formTpl, formElement, identifierHelper, htmlEditor, contentHelper){

    var SimpleAssociableChoiceStateChoice = stateFactory.extend(Choice, function(){
        
        this.buildEditor();
        
    }, function(){
        
        this.destroyEditor();
    });
    
    SimpleAssociableChoiceStateChoice.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            choice = _widget.element;

        //build form:
        $form.html(formTpl({
            serial : choice.getSerial(),
            identifier : choice.id(),
            matchMin : choice.attr('matchMin'),
            matchMax : choice.attr('matchMax')
        }));

        formElement.initWidget($form);

        //init data validation and binding
        var callbacks = formElement.getMinMaxAttributeCallbacks(this.widget.$form, 'matchMin', 'matchMax');
        callbacks.identifier = identifierHelper.updateChoiceIdentifier;
        formElement.setChangeCallbacks($form, choice, callbacks);
    };
    
    SimpleAssociableChoiceStateChoice.prototype.buildEditor = function(){
        
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

    SimpleAssociableChoiceStateChoice.prototype.destroyEditor = function(){
        
        //search and destroy the editor
        htmlEditor.destroyEditor(this.widget.$container);
    };
    
    return SimpleAssociableChoiceStateChoice;
});

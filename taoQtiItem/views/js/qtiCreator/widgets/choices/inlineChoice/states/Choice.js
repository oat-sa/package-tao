define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Choice',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/choices/choice',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/helpers/identifier'
], function(stateFactory, Choice, formTpl, formElement, identifierHelper){
    
    var InlineChoiceStateChoice = stateFactory.extend(Choice);

    InlineChoiceStateChoice.prototype.initForm = function(){
        
        var _widget = this.widget;
        
        //build form:
        _widget.$form.html(formTpl({
            serial:_widget.element.getSerial(),
            identifier:_widget.element.id()
        }));
        
        //init data validation and binding
        formElement.setChangeCallbacks(_widget.$form, _widget.element, {
            identifier : identifierHelper.updateChoiceIdentifier
        });
    };
    
    return InlineChoiceStateChoice;
});
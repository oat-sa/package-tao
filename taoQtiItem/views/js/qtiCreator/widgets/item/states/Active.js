define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Active',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/item',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement'
], function(stateFactory, Active, formTpl, formElement){
    
    'use strict';
    
    var ItemStateActive = stateFactory.create(Active, function(){

        this.initForm();

    }, function(){

    });

    ItemStateActive.prototype.initForm = function(){

        var _widget = this.widget,
            item = _widget.element,
            $form = _widget.$form;

        //build form:
        $form.html(formTpl({
            serial : item.getSerial(),
            identifier : item.id(),
            title : item.attr('title'),
            timeDependent : !!item.attr('timeDependent')
        }));
        
        //init widget
        formElement.initWidget($form);

        //init data validation and binding
        formElement.setChangeCallbacks($form, item, {
            identifier : formElement.getAttributeChangeCallback(),
            title : formElement.getAttributeChangeCallback(),
            timeDependent : formElement.getAttributeChangeCallback()
        });
    };

    return ItemStateActive;
});
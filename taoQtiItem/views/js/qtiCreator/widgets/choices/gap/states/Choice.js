define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Choice',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/choices/gap',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/helpers/identifier',
    'taoQtiItem/qtiItem/core/Element'
], function(stateFactory, Choice, formTpl, formElement, identifierHelper, Element){

    var GapStateChoice = stateFactory.extend(Choice, function(){
        
        var _widget = this.widget;
        
        //listener to changes of sibling choices' mode 
        _widget.beforeStateInit(function(e, element, state){
            
            if(Element.isA(element, 'gap') && _widget.interaction.getBody().getElement(element.serial)){
                
                if(state.name === 'choice' && element.serial !== _widget.serial){
                    _widget.changeState('question');
                }else if(state.name === 'active'){
                    _widget.changeState('question');
                }
                
            }
        }, 'otherActive');
        
    }, function(){

        //add remove the toolbar
        this.widget.offEvents('otherActive');
    });

    GapStateChoice.prototype.initForm = function(){

        var $form = this.widget.$form,
            interaction = this.widget.element;

        //build form:
        $form.html(formTpl({
            serial : interaction.getSerial(),
            identifier : interaction.id(),
            required : !!interaction.attr('required')
        }));

        formElement.initWidget($form);

        //init data validation and binding
        formElement.setChangeCallbacks($form, interaction, {
            identifier : identifierHelper.updateChoiceIdentifier,
            required : formElement.getAttributeChangeCallback()
        });
    };

    return GapStateChoice;
});
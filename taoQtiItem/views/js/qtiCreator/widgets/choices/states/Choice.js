define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Choice',
    'taoQtiItem/qtiItem/core/Element'
], function(stateFactory, Choice, Element){

    var ChoiceStateChoice = stateFactory.create(Choice, function(){

        var _widget = this.widget;

        //focus on the selected choice

        //listener to other siblings choice mode
        _widget.beforeStateInit(function(e, element, state){
            
            if(Element.isA(element, 'choice') && _widget.interaction.getChoice(element.serial)){//@todo hottext an
                
                if(state.name === 'choice' && element.serial !== _widget.serial){
                    _widget.changeState('question');
                }else if(state.name === 'active'){
                    _widget.changeState('question');
                }
                
            }else if(element.is('img') || element.is('math') || element.is('object')){
                
                if(state.name === 'active'){
                    _widget.changeState('question');
                }
                
            }
        }, 'otherActive');

        //add options form
        this.initForm();
        this.widget.$form.show();

    }, function(){

        //destroy and hide the form
        this.widget.$form.empty().hide();
        
        this.widget.offEvents('otherActive');
    });

    ChoiceStateChoice.prototype.initForm = function(){
        stateFactory.throwMissingRequiredImplementationError('initForm');
    };

    return ChoiceStateChoice;
});
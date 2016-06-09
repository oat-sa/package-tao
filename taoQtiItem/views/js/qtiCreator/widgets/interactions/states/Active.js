define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Active',
    'lodash'
], function(stateFactory, Active, _){

    var _containsInModalFeedbackElts = function(response, searchSerial){

        var found = false,
            modalFeedbacks = [];

        _.each(response.feedbackRules, function(rule){
            if(rule.feedbackThen && rule.feedbackThen.qtiClass === 'modalFeedback'){
                modalFeedbacks.push(rule.feedbackThen);
            }
            if(rule.feedbackElse && rule.feedbackElse.qtiClass === 'modalFeedback'){
                modalFeedbacks.push(rule.feedbackElse);
            }
        });
        _.each(modalFeedbacks, function(fb){
            var elt = fb.getBody().getElement(searchSerial);
            if(elt){
                found = true;
                return false;//break
            }
        });

        return found;
    };

    var InteractionStateActive = stateFactory.extend(Active, function(){
        
        var _widget = this.widget,
            $container = _widget.$container,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        $container.attr('contenteditable', false);

        _widget.beforeStateInit(function(e, element, state){
            
            var serial = element.getSerial();
            
            if(element.qtiClass === 'modalFeedback'){
                return false;
            }
            
            if(state.name === 'active' && serial !== _widget.serial){
                
                //when it does not click on itself, check if the newly activated element is its own composing element:
                var composingElts = interaction.getComposingElements();
                var inModalFeedback = _containsInModalFeedbackElts(response, serial);
                if(!composingElts[serial] && !inModalFeedback){
                    _widget.changeState('sleep');
                }
            }
        }, 'otherActive');

        $container.on('mouseenter.active', function(e){
            e.stopPropagation();
            $container.parent().trigger('mouseleave.sleep');
        }).on('mouseleave.active', function(e){
            e.stopPropagation();
            $container.parent().trigger('mouseenter.sleep');
        }).on('click.active', function(e){
            e.stopPropagation();
        });

    }, function(){

        this.widget.offEvents('otherActive');
    });

    return InteractionStateActive;
});
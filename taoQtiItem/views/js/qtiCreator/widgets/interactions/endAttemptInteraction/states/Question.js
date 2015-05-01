define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/endAttempt'
], function(_, $, stateFactory, Question, formElement, formTpl){
    'use strict';
    var EndAttemptInteractionStateQuestion = stateFactory.extend(Question, function(){
        
        var $mainOption = this.widget.$container.find('.main-option'),
            $original = this.widget.$original;
        
        //listener to children choice widget change and update the original interaction placehoder
        $(document).on('choiceTextChange.qti-widget.question', function(){
            $original.width($mainOption.width());
        });

    }, function(){
        
        $(document).off('.qti-widget.question');
    });

    EndAttemptInteractionStateQuestion.prototype.addNewChoiceButton = function(){};

    EndAttemptInteractionStateQuestion.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            $title = _widget.$container.find('.endAttemptInteraction-placeholder'),
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();
        
        var restrictedIdentifiers = _prepareRestrictedIdentifier(_widget);
        var hasRestrictedIdentifier = !!_.size(restrictedIdentifiers);
        
        $form.html(formTpl({
            hasRestrictedIdentifier : hasRestrictedIdentifier,
            restrictedIdentifiers : restrictedIdentifiers,
            responseSerial : response.serial,
            responseIdentifier : interaction.attr('responseIdentifier'),
            title : interaction.attr('title')
        }));

        formElement.initWidget($form);

        formElement.setChangeCallbacks($form, interaction, {
            title : function(interaction, title){
                interaction.attr('title', title);
                $title.html(title);
                _widget.$original.html(title);
            },
            responseIdentifier : function(interaction, identifier){
                //directly save the validate response identifier (it went throught the validator so we know it is unique)
                response.id(identifier);
                //sync the response identifier in the interaction
                interaction.attr('responseIdentifier', identifier);
            },
            restrictedIdentifier : function(interaction, identifier){
                //generate a response from that identifier (because might not be unique
                response.buildIdentifier(identifier, false);
                //sync the response identifier in the interaction
                interaction.attr('responseIdentifier', response.id());
            }
        });
    };
    
    /**
     * Prepare the restricted identifiers to be used 
     * (when the option in configuration "responseIdentifiers" is set)
     * 
     * @param {Object} widget
     * @returns {Object}
     */
    function _prepareRestrictedIdentifier(widget){
        
        var ret = {},
            interaction = widget.element,
            response = interaction.getResponseDeclaration(),
            responseIdentifier = interaction.attr('responseIdentifier'),
            config = widget.options.config,
            defaultIdentifier = '',
            isset = '',
            i = 0;
        
        //only execute the code when the config option is set "responseIdentifiers"
        if(config && config.responseIdentifiers){
            
            _.forIn(config.responseIdentifiers, function(title, identifier){
                
                //store the first identifier as the default one
                if(!i){
                    defaultIdentifier = identifier;
                }
                
                //considered selected when match the pattern RESPONSE_STUFF === RESPONSE_STUFF_2 === RESPONSE_STUFF_3
                var selected = responseIdentifier.match(new RegExp('^('+identifier+')(_[0-9]*)?$'));
                
                //prepare the template data
                ret[identifier] = {
                    identifier : identifier,
                    title : title,
                    selected : selected
                };
                
                if(selected){
                    isset = true;
                }
                
                i++;
            });
            
            //if none of the authorized identifier is set, set the first one
            if(!isset){
                //directly save the validate response identifier (it went throught the validator so we know it is unique)
                response.buildIdentifier(defaultIdentifier, false);
                //sync the response identifier in the interaction
                interaction.attr('responseIdentifier', response.id());
            }
        }
        return ret;
    }
    
    return EndAttemptInteractionStateQuestion;
});

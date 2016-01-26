define([
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/HottextInteraction',
    'taoQtiItem/qtiCreator/model/choices/Hottext',
    'taoQtiItem/qtiCreator/model/helper/event',
    'taoQtiItem/qtiCreator/model/helper/response'
], function(_, Element, editable, editableInteraction, Interaction, Choice,event, responseHelper){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                maxChoices : 0,
                minChoices : 0
            };
        },
        afterCreate : function(){
            this.body('<p>Lorem ipsum dolor sit amet, consectetur adipisicing ...</p>');
            this.createResponse({
                baseType : 'identifier',
                cardinality : 'multiple'
            });
        },
        createChoice : function(attr, body){

            var choice = new Choice('', attr);

            this.addChoice(choice);
            choice.buildIdentifier('hottext');
            choice.body(body);
                
            if(this.getRenderer()){
                choice.setRenderer(this.getRenderer());
            }
            
            event.choiceCreated(choice, this);

            return choice;
        },
        removeChoice : function(hottext){
        
            var serial = '', c;
            
            if(typeof(hottext) === 'string'){
                serial = hottext;
            }else if(Element.isA(hottext, 'hottext')){
                serial = hottext.getSerial();
            }
            
            c = this.getBody().getElement(serial);
            if(c){
                //remove choice
                this.getBody().removeElement(c);
                
                //update the response
                responseHelper.removeChoice(this.getResponseDeclaration(), c);
                
                //trigger event
                event.deleted(c, this);
            }
            
            return this;
        }
    });
    
    return Interaction.extend(methods);
});

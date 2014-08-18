define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableContainer',
    'taoQtiItem/qtiItem/core/Item',
    'taoQtiItem/qtiCreator/model/Stylesheet',
    'taoQtiItem/qtiCreator/model/ResponseProcessing',
    'taoQtiItem/qtiCreator/model/variables/OutcomeDeclaration',
    'taoQtiItem/qtiCreator/model/feedbacks/ModalFeedback'
], function(_, editable, editableContainer, Item, Stylesheet, ResponseProcessing, OutcomeDeclaration, ModalFeedback){
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableContainer);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                identifier : 'myItem_1',
                title : 'Item title',
                adaptive : false,
                timeDependent : false
            };
        },
        createResponseProcessing : function(){
            var rp = new ResponseProcessing();
            rp.processingType = 'templateDriven';
            this.setResponseProcessing(rp);
            return rp;
        },
        createStyleSheet : function(href){
            if(href && _.isString(href)){
                var stylesheet = new Stylesheet({href : href});
                stylesheet.setRenderer(this.getRenderer());
                this.addStylesheet(stylesheet);
                return stylesheet;
            }else{
                throw 'missing or invalid type for the required arg "href"';
                return null;
            }
        },
        createOutcomeDeclaration : function(attributes){

            var identifier = attributes.identifier || '';
            delete attributes.identifier;
            var outcome = new OutcomeDeclaration(attributes);

            this.addOutcomeDeclaration(outcome);
            outcome.buildIdentifier(identifier);

            return outcome;
        },
        createModalFeedback : function(attributes){

            var identifier = attributes.identifier || '';
            delete attributes.identifier;
            var modalFeedback = new ModalFeedback(attributes);

            this.addModalFeedback(modalFeedback);
            modalFeedback.buildIdentifier(identifier);
            modalFeedback.body('Some feedback text.');

            return modalFeedback;
        },
        deleteResponseDeclaration : function(response){
            var serial;
            if(_.isString(response)){
                serial = response;
            }else if(response && response.qtiClass === 'responseDeclaration'){
                serial = response.getSerial();
            }
            delete this.responses[serial];
            return this;
        }
    });
    return Item.extend(methods);
});
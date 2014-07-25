Qti.Item = Qti.Element.extend({
    qtiTag : 'assessmentItem',
    responses : {},
    outcomes : {},
    modalFeedbacks : {},
    init : function(serial, attributes){
        this._super(serial, attributes);
        this.relatedItem = this;
    },
    postRender : function(){
        this.getBody().postRender(this.getRenderer());
    },
    getInteractions : function(){
        var interactions = [];
        var elts = this.getComposingElements();
        for(var serial in elts){
            if(elts[serial] instanceof Qti.Interaction){
                interactions.push(elts[serial]);
            }
        }
        return interactions;
    },
    addResponseDeclaration : function(response){
        if(response instanceof Qti.ResponseDeclaration){
            response.setRelatedItem(this);
            this.responses[response.getSerial()] = response;
        }else{
            throw 'is not a qti response declaration';
        }
    },
    getResponseDeclaration : function(identifier){
        for(var i in this.responses){
            if(this.responses[i].attr('identifier') === identifier){
                return this.responses[i];
            }
        }
        return null;
    },
    addOutcomeDeclaration : function(outcome){
        if(outcome instanceof Qti.OutcomeDeclaration){
            outcome.setRelatedItem(this);
            this.outcomes[outcome.getSerial()] = outcome;
        }else{
            throw 'is not a qti outcome declaration';
        }
    },
    addModalFeedbacks : function(feedback){
        if(feedback instanceof Qti.ModalFeedback){
            feedback.setRelatedItem(this);
            this.modalFeedbacks[feedback.getSerial()] = feedback;
        }else{
            throw 'is not a qti modal feedback';
        }
    },
    getComposingElements : function(){
        var elts = this._super();
        for(var i in this.modalFeedbacks){
            var feedback = this.modalFeedbacks[i];
            elts[i] = feedback;
            elts = $.extend(elts, feedback.getComposingElements());
        }
        return elts;
    },
    getResponses : function(){
        return this.responses;
    },
    getRelatedItem : function(){
        return this;
    },
    setNamespaces : function(namespaces){
        this.namespaces = namespaces;
    },
    setStylesheets : function(stylesheets){
        this.stylesheets = stylesheets;
    },
    toArray : function(){
        var arr = this._super();
        arr.outcomes = {};
        for(var i in this.outcomes){
            arr.outcomes[i] = this.outcomes[i].toArray();
        }
        arr.responses = {};
        for(var i in this.responses){
            arr.responses[i] = this.responses[i].toArray();
        }
        arr.namespaces = this.namespaces;
        arr.stylesheets = this.stylesheets;
        return arr;
    }
}, {
    'container' : Qti.traits.container
});
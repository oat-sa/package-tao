define(['taoQtiItem/qtiItem/core/Element'], function(Element){

    var SimpleFeedbackRule = Element.extend({
        qtiClass : '_simpleFeedbackRule',
        serial : '',
        relatedItem : null,
        init : function(serial, feedbackOutcome, feedbackThen, feedbackElse){

            this._super(serial, {});

            this.condition = 'correct';
            this.comparedOutcome = null;
            this.comparedValue = 0.0;

            this.feedbackOutcome = feedbackOutcome;
            if(Element.isA(feedbackThen, 'feedback')){
                this.feedbackThen = feedbackThen;
            }else{
                this.feedbackThen = null;
            }
            if(Element.isA(feedbackElse, 'feedback')){
                this.feedbackElse = feedbackThen;
            }else{
                this.feedbackElse = null;
            }

        },
        setCondition : function(comparedOutcome, condition, comparedValue){

            if(Element.isA(comparedOutcome, 'variableDeclaration')){
                switch(condition){
                    case 'correct':
                    case 'incorrect':
                        if(Element.isA(comparedOutcome, 'responseDeclaration')){
                            this.comparedOutcome = comparedOutcome;
                            this.condition = condition;
                        }else{
                            throw 'invalid outcome type: must be a responseDeclaration';
                        }
                        break;
                    case 'lt':
                    case 'lte':
                    case 'equal':
                    case 'gte':
                    case 'gt':
                        if(comparedValue !== null && comparedValue !== undefined){
                            this.comparedOutcome = comparedOutcome;
                            this.condition = condition;
                            this.comparedValue = comparedValue;
                        }else{
                            throw 'compared value must not be null';
                        }
                        break;
                    default:
                        throw 'unknown condition type : '.condition;
                }
            }else{
                throw 'invalid outcome type: must be a variableDeclaration';
            }

            return this;
        },
        setFeedbackElse : function(feedback){
            if(Element.isA(feedback, 'feedback')){
                this.feedbackElse = feedback;
            }
        }
    });

    return SimpleFeedbackRule;
});
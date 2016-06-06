define([
    'lodash',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule_condition',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule_correct',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule_incorrect',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule_choices'
], function(_, tpl, tplCondition, tplCorrect, tplIncorrect, tplChoices){
    return {
        qtiClass : '_simpleFeedbackRule',
        template : tpl,
        getData : function(rule, data){
            
            var template = null, ruleXml = '';
            var _values;
            var tplData = {
                response : rule.comparedOutcome.id(),
                feedback : {
                    'outcome' : rule.feedbackOutcome.id(),
                    'then' : rule.feedbackThen.id(),
                    'else' : rule.feedbackElse ? rule.feedbackElse.id() : ''
                }
            };

            switch(rule.condition){
                case 'correct':
                    template = tplCorrect;
                    break;
                case 'incorrect':
                    template = tplIncorrect;
                    break;
                case 'lt':
                case 'lte':
                case 'equal':
                case 'gte':
                case 'gt':
                    template = tplCondition;
                    tplData.condition = rule.condition;
                    tplData.comparedValue = rule.comparedValue;
                    break;
                case 'choices':
                    template = tplChoices;
                    tplData.condition = rule.condition;
                    tplData.multiple = rule.comparedOutcome.isCardinality(['multiple', 'ordered']);
                    _values = [];
                    _.each(rule.comparedValue, function(choice){
                        //check if all the selected choices still exist
                        if(choice.parent()){
                            _values.push(choice.id());
                        }
                    });
                    
                    if(tplData.multiple){
                        tplData.choices = _values;
                    }else{
                        if(_values.length){
                            tplData.choice = _.head(_values);
                        }else{
                            tplData.noData = true;
                        }
                    }
                    break;
                default:
                    throw new Error('unknown condition in simple feedback rule rendering : '+rule.condition);
            }

            if(template){
                ruleXml = template(tplData);
            }

            return _.merge(data || {}, {rule : ruleXml});
        }
    };
});

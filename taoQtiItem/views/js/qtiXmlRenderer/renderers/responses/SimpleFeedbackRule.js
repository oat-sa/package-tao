define([
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule_condition',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule_correct',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/rule_incorrect'
], function(tpl, tplCondition, tplCorrect, tplIncorrect){
    return {
        qtiClass : '_simpleFeedbackRule',
        template : tpl,
        getData : function(rule, data){
            
            var template = null, ruleXml = '';
            
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
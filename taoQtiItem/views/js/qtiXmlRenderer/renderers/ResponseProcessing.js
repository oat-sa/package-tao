define([
    'lodash',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responseProcessing',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/match_correct',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/map_response',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/responses/map_response_point'
], function(_, tpl, correctTpl, mapTpl, mapPointTpl){
    
    var _renderInteractionRp = function(interaction){
        var ret = '', response = interaction.getResponseDeclaration();
        if(response.template){
            ret = _renderRpTpl(response.template, {
                responseIdentifier : response.id(),
                outcomeIdentifier : 'SCORE'
            });
        }
        return ret;
    };

    var _renderRpTpl = function(rpTpl, data){

        var ret = '';

        switch(rpTpl){
            case 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct':
            case 'MATCH_CORRECT':
                ret = correctTpl(data);
                break;
            case 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response':
            case 'MAP_RESPONSE':
                ret = mapTpl(data);
                break;
            case 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response_point':
            case 'MAP_RESPONSE_POINT':
                ret = mapPointTpl(data);
                break;
            default:
                throw new Error('unknown rp template : ' + rpTpl);
        }

        return ret;
    };
    
    var _renderFeedbackRules = function(renderer, response){
        var ret = [];
        _.each(response.getFeedbackRules(), function(rule){
            ret.push(rule.render(renderer));
        });
        return ret;
    };
    
    return {
        qtiClass : 'responseProcessing',
        template : tpl,
        getData : function(responseProcessing, data){
            
            var defaultData = {},
                _renderer = this;
            
            switch(responseProcessing.processingType){
                case 'custom':
                    defaultData.custom = true;
                    defaultData.xml = responseProcessing.xml;
                    break;
                case 'templateDriven':
                    var interactions = responseProcessing.getRelatedItem().getInteractions();
                    if(interactions.length === 1){
                        var response = interactions[0].getResponseDeclaration();
                        if(_.size(response.getFeedbackRules()) === 0 && response.id() === 'RESPONSE'){
                            //the exact condition to serialize the rp as a standard template is met
                            defaultData.template = response.template;
                            break;
                        }
                    }
                    defaultData.templateDriven = true;
                    defaultData.responseRules = [];
                    _.each(interactions, function(interaction){
                        defaultData.responseRules.push(_renderInteractionRp(interaction));
                    });
                    
                    defaultData.feedbackRules = [];
                    _.each(interactions, function(interaction){
                        defaultData.feedbackRules = _.union(defaultData.feedbackRules, _renderFeedbackRules(_renderer, interaction.getResponseDeclaration()));
                    });
                    
                    break;
            }
            
            return _.merge(data || {}, defaultData);
        }
    };
});
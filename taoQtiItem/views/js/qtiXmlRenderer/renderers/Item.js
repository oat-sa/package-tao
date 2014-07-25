define(['lodash', 'tpl!taoQtiItem/qtiXmlRenderer/tpl/item'], function(_, tpl, rendererConfig){
    return {
        qtiClass : 'assessmentItem',
        template : tpl,
        getData : function(item, data){
            
            var ns = _.clone(item.namespaces) || [],
                renderer = this;
            
            delete ns[''];
            delete ns['xsi'];
            delete ns['xml'];
            
            var defaultData = {
                responses : [],
                outcomes : [],
                stylesheets : [],
                feedbacks : [],
                namespaces : ns,
                empty : item.isEmpty(),
                responseProcessing : item.responseProcessing ? item.responseProcessing.render(renderer) : ''
            };
            
            _.each(item.responses, function(response){
                defaultData.responses.push(response.render(renderer));
            });
            _.each(item.outcomes, function(outcome){
                defaultData.outcomes.push(outcome.render(renderer));
            });
            _.each(item.stylesheets, function(stylesheet){
                defaultData.stylesheets.push(stylesheet.render(renderer));
            });
            _.each(item.modalFeedbacks, function(feedback){
                defaultData.feedbacks.push(feedback.render(renderer));
            });
            
            return _.merge(data || {}, defaultData);
        }
    };
});
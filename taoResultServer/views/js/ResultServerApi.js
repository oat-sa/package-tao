define(['jquery'], function($){
    
    function ResultServerApi(endpoint, parameters){
            this.endpoint = endpoint;
            this.parameters = parameters || {};
    }

    ResultServerApi.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, params, callback){
            $.ajax({
                    url : this.endpoint + 'storeItemVariableSet',
                    data : {
                        itemId: itemId,
                        serviceCallId: serviceCallId,
                        responseVariables: responses,
                        outcomeVariables: scores,
                        traceVariables: events
                    },
                    type : 'post',
                    dataType : 'json',
                    success : function() {
                        if(typeof callback === 'function'){
                            callback();
                        }
                    }
            });
    };
    
    return ResultServerApi;
});
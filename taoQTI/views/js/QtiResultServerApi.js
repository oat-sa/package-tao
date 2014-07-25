function QtiResultServerApi(endpoint, itemUri){
    this.endpoint = endpoint;
    this.itemUri = itemUri;
    
    //private variable
    var qtiRunner = null;
    this.setQtiRunner = function(runner){
        qtiRunner = runner;
    };
    this.getQtiRunner = function(){
        return qtiRunner;
    };
}

QtiResultServerApi.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, callback){
    var _this = this;
    $.ajax({
        url : this.endpoint + 'submitResponses',
        data : {
            itemId : itemId,
            itemUri: this.itemUri,
            serviceCallId : serviceCallId,
            responseVariables : responses,
            traceVariables : events
        },
        type : 'post',
        dataType : 'json',
        success : function(r){
            if(r.success){
                var fbCount = 0;
                if(r.itemSession){
                    var runner = _this.getQtiRunner();
                    if(runner){
                        fbCount = runner.showFeedbacks(r.itemSession, callback);
                    }
                }
                if(!fbCount){
                    callback();
                }
            }
        }
    });
};
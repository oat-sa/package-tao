function QtiPreviewResultServerApi(endpoint, itemUri){
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

QtiPreviewResultServerApi.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, callback){
    var _this = this;
    $.ajax({
        url : this.endpoint + 'submitResponses',
        data : {
            itemId : itemId,
            itemUri : this.itemUri,
            serviceCallId : serviceCallId,
            responseVariables : responses,
            traceVariables : events,
        },
        type : 'post',
        dataType : 'json',
        success : function(r){
            if(r.success){
                var fbCount = 0;
                var runner = _this.getQtiRunner();
                if(r.itemSession){

                    fbCount = runner.showFeedbacks(r.itemSession, callback);

                    // Log in preview console.
                    var previewConsole = $('#preview-console');
                    for(var variableIdentifier in r.itemSession){
                        previewConsole.trigger('updateConsole', ['QTI Outcome Variable', variableIdentifier + ': ' + r.itemSession[variableIdentifier]]);
                    }
                }
                if(!fbCount){
                    callback();
                }
                
                //reset submit listener, in the preview iframe:
                $('#preview-container').each(function(){
                    $("#qti_validate", this.contentWindow.document).one('click', function(){
                        runner.validate();
                    });
                });
            }
        }
    });
};
define(['jquery'], function($){

    function QtiResultServerApi(endpoint){
        this.endpoint = endpoint;

        //private variable
        var qtiRunner = null;
        this.setQtiRunner = function(runner){
            qtiRunner = runner;
        };
        this.getQtiRunner = function(){
            return qtiRunner;
        };
    }

    QtiResultServerApi.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, params, callback){
        var _this = this;
        $.ajax({
            url : this.endpoint + 'submitResponses' 
                + '?itemId=' + encodeURIComponent(itemId) 
                + '&serviceCallId=' + encodeURIComponent(serviceCallId)
                + '&itemDataPath=' + encodeURIComponent(params.itemDataPath),
            data : JSON.stringify(responses),
            type : 'post',
            contentType : 'application/json',
            dataType : 'json',
            success : function(r){

                var fbCount = 0,
                    qtiRunner = _this.getQtiRunner();

                if(qtiRunner && r.success && r.itemSession){
                    
                    //load feedbacks data into item instance
                    qtiRunner.loadElements(r.feedbacks, function(){

                        //show feedbacks if required
                        fbCount = qtiRunner.showFeedbacks(r.itemSession, callback);
                        if(!fbCount){
                            callback(0);
                        }
                    });
                }
            }
        });
    };

    return QtiResultServerApi;
});
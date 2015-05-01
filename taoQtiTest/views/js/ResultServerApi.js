define(['jquery', 'iframeNotifier'], function($, iframeNotifier) {

    function ResultServerApi(endpoint, params) {

        this.endpoint = endpoint;
        this.testServiceCallId = params.testServiceCallId;
        this.testDefinition = params.testDefinition;
        this.testCompilation = params.testCompilation;
        this.itemDataPath = params.itemDataPath;

        //private variable
        var qtiRunner = null;
        this.setQtiRunner = function(runner) {
            qtiRunner = runner;
        };

        this.getQtiRunner = function() {
            return qtiRunner;
        };
    }

    ResultServerApi.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, params, callback) {

        var that = this;
        var error = function error(){
            //there is no error management, so doing an alert (an eval and I'll burn in hell...)
            //TODO manage errors during the delivery
            alert('An error occurs, please contact your administrator');

            iframeNotifier.parent('unloading');
            callback(0);
        };

        iframeNotifier.parent('loading');

        function onShowCallback(){
            iframeNotifier.parent('unloading');
        }

        $.ajax({
            url : this.endpoint + 'storeItemVariableSet?serviceCallId='
                + encodeURIComponent(this.testServiceCallId)
                + '&QtiTestDefinition='
                + encodeURIComponent(this.testDefinition)
                + '&QtiTestCompilation='
                + encodeURIComponent(this.testCompilation)
                + '&itemDataPath='
                + encodeURIComponent(this.itemDataPath),
            data : JSON.stringify(responses),
            type : 'post',
            contentType : 'application/json',
            dataType : 'json',
            success : function(reply){

                var qtiRunner,
                    fbCount = 0;
                
                if(reply && reply.success){
                    qtiRunner = that.getQtiRunner();
                    if(reply.itemSession && qtiRunner){
                        //load feedbacks data into item instance
                        qtiRunner.loadElements(reply.feedbacks, function(){
                            //show feedbacks if required
                            fbCount = qtiRunner.showFeedbacks(reply.itemSession, callback, onShowCallback);
                            if(!fbCount){
                                onShowCallback();
                                callback(0);
                            }
                        });
                    }

                }else{
                    error();
                }
            },
            error : error
        });
    };

    return ResultServerApi;
});
define(['jquery', 'i18n', 'iframeNotifier'], function($, __, iframeNotifier) {

    // Constants
    var TEST_STATE_SUSPENDED = 3,
        TEST_STATE_CLOSED = 4;

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
        var error = function error(xhr){
            var response;
            var message = __('An error occurs, please contact your administrator');

            if (xhr) {
                if (xhr.status && xhr.status === 403) {
                    // don't show alert, layout/logout-event should be performed
                    message = '';
                } else {
                    try {response = JSON.parse(xhr.responseText);} catch (e) {}
                }
            }

            //there is no error management, so doing an alert (an eval and I'll burn in hell...)
            //TODO manage errors during the delivery
            if (response && response.message) {
                if (response.code === TEST_STATE_CLOSED || response.code === TEST_STATE_SUSPENDED) {
                    message = false;
                } else {
                    message = response.message;
                }
            }
            if (message) {
                alert(message);
            }
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
            success : function(reply, status, xhr){

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
                    error(xhr);
                }
            },
            error : error
        });
    };

    return ResultServerApi;
});
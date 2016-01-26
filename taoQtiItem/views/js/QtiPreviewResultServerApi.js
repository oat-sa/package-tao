define([
    'jquery',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'util/strPad'],
    function($, pciResponse, strPad){

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

    QtiPreviewResultServerApi.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, params, callback){
        var _this = this;
        // Log in preview console
        var previewConsole = $('#preview-console');
        var variableIdentifier;

        for (variableIdentifier in responses) {
            previewConsole.trigger('updateConsole', [
                'Submitted data', strPad(variableIdentifier + ': ', 15, ' ') + pciResponse.prettyPrint(responses[variableIdentifier])
            ]);
        }

        $.ajax({
            url : this.endpoint + 'submitResponses'
                + '?itemId=' + encodeURIComponent(itemId)
                + '&itemUri=' + encodeURIComponent(this.itemUri)
                + '&serviceCallId=' + encodeURIComponent(serviceCallId),
            data : JSON.stringify(responses),
            contentType: 'application/json',
            type : 'post',
            dataType : 'json',
            success : function(r){
                if(r.success){
                    var fbCount = 0;
                    var runner = _this.getQtiRunner();
                    if(r.itemSession){

                        fbCount = runner.showFeedbacks(r.itemSession, callback);

                        for (var variableIdentifier in r.itemSession) {
                            previewConsole.trigger('updateConsole', [
                                'Output data', strPad(variableIdentifier + ': ', 15, ' ') + pciResponse.prettyPrint(r.itemSession[variableIdentifier])
                            ]);
                        }
                    }
                    if(!fbCount){
                        callback();
                    }
                }
            }
        });
    };

    return QtiPreviewResultServerApi;
});

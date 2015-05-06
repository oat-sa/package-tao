define(['jquery'], function($){

    function ConsoleResultServer(){
    }

    ConsoleResultServer.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, callback){

            var previewConsole = $('#preview-console');
            if (previewConsole.length > 0){
                    //In the preview console
                    var niceResponses = '';
                    for (var key in responses) {
                            niceResponses = niceResponses + key + ' = ' + responses[key] + '<br />';
                    }
                    previewConsole.trigger('updateConsole', ['RESPONSES', niceResponses.substring(0, niceResponses.length-2)]);
                    var niceScore = '';
                    for (var key in scores) {
                            niceScore = niceScore + key + ' = ' + scores[key] + ', ';
                    }
                    previewConsole.trigger('updateConsole', ['SCORE', niceScore.substring(0, niceScore.length-2)]);
            } 

        if(typeof(callback) === "function") {
            callback();
        };
    };

    return ConsoleResultServer;

});
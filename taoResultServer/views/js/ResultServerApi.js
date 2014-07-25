function ResultServerApi(endpoint){
	this.endpoint = endpoint;
}

ResultServerApi.prototype.submitItemVariables = function(itemId, serviceCallId, responses, scores, events, callback){
	$.ajax({
		url  		: this.endpoint + 'storeItemVariableSet',
		data 		: {
			itemId: itemId,
			serviceCallId: serviceCallId,
			responseVariables: responses,
			outcomeVariables: scores,
			traceVariables: events
		},
		type 		: 'post',
		dataType	: 'json',
		success		: function(reply) {
			callback();
		}
	});
};
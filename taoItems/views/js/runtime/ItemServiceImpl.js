function ItemServiceImpl(serviceApi) {

	// temporary fix
	if (typeof itemId !== "undefined") {
		this.itemId = itemId;
	}
	
	this.serviceApi = serviceApi;
	this.responses = {};
	this.scores = {};
	this.events = {};
	
	var rawstate = serviceApi.getState();
	var state = (typeof rawstate == 'undefined' || rawstate == null) ? {} : $.parseJSON(rawstate);
	this.stateVariables = typeof state == 'object' ? state : {};
	
	this.resultApi = (typeof resultApi == 'undefined' || resultApi == null) ? null : resultApi;
	
	this.beforeFinishCallbacks = new Array();
}

ItemServiceImpl.prototype.connect = function(frame){
	frame.contentWindow.itemApi = this;
	if (typeof(frame.contentWindow.onItemApiReady) == "function") {
		frame.contentWindow.onItemApiReady(this);
	}
};

// Response 

ItemServiceImpl.prototype.saveResponses = function(valueArray){
	for (var attrname in valueArray) {
		this.responses[attrname] = valueArray[attrname];
	}
};

ItemServiceImpl.prototype.traceEvents = function(eventArray) {
	for (var attrname in eventArray) {
		this.events[attrname] = eventArray[attrname];
	}
};

// Scoring
ItemServiceImpl.prototype.saveScores = function(valueArray) {
	for (var attrname in valueArray) {
		this.scores[attrname] = valueArray[attrname];
	}
};

// Flow
ItemServiceImpl.prototype.beforeFinish = function(callback) {
	this.beforeFinishCallbacks.push(callback);
};

ItemServiceImpl.prototype.finish = function() {
	for (var i = 0; i < this.beforeFinishCallbacks.length; i++) {
		this.beforeFinishCallbacks[i]();
	};

	this.serviceApi.setState(JSON.stringify(this.stateVariables), function(itemApi) {
		
		return function() {
			//todo add item, call id etc
			
			if (itemApi.resultApi != null) {
				itemApi.resultApi.submitItemVariables(
						itemApi.itemId,
						itemApi.serviceApi.getServiceCallId(),
						itemApi.responses,
						itemApi.scores,
						itemApi.events,
						function() {
							itemApi.serviceApi.finish();
						}
				);
			} else {
				itemApi.serviceApi.finish();
			}
		};
	}(this));		
};

ItemServiceImpl.prototype.getVariable = function(identifier, callback) {
	if (typeof callback == 'function') {
		callback((typeof this.stateVariables[identifier] == 'undefined')
			? null
			: this.stateVariables[identifier]
		);
	}
};

ItemServiceImpl.prototype.setVariable = function(identifier, value) {
	this.stateVariables[identifier] = value;
};

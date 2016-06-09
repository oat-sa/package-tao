define([ 'jquery' ], function($) {

    function ItemServiceImpl(data) {

        this.itemId = data.itemId;
        this.serviceApi = data.serviceApi;
        this.resultApi = data.resultApi || null;
        this.params = data.params || {};
        this.responses = {};
        this.scores = {};
        this.events = {};
        this.connected = false;

        var rawstate = this.serviceApi.getState();
        var state = (typeof rawstate === 'undefined' || rawstate === null) ? {}
                : $.parseJSON(rawstate);
        this.stateVariables = typeof state === 'object' ? state : {};

        this.beforeFinishCallbacks = [];
    }

    ItemServiceImpl.prototype.connect = function(frame) {
        if (this.connected === false && frame.contentWindow) {
            frame.contentWindow.itemApi = this;
            
            if (typeof (frame.contentWindow.onItemApiReady) === "function") {
                frame.contentWindow.onItemApiReady(this);
                this.connected = true;
            }
        }
    };

    // Response

    ItemServiceImpl.prototype.saveResponses = function(valueArray) {
        for ( var attrname in valueArray) {
            this.responses[attrname] = valueArray[attrname];
        }
    };

    ItemServiceImpl.prototype.traceEvents = function(eventArray) {
        for ( var attrname in eventArray) {
            this.events[attrname] = eventArray[attrname];
        }
    };

    // Scoring
    ItemServiceImpl.prototype.saveScores = function(valueArray) {
        for ( var attrname in valueArray) {
            this.scores[attrname] = valueArray[attrname];
        }
    };

    ItemServiceImpl.prototype.getUserPropertyValues = function(property,
            callback) {
        this.serviceApi.getUserPropertyValues(property, callback);
    };

    // Flow
    ItemServiceImpl.prototype.beforeFinish = function(callback) {
        this.beforeFinishCallbacks.push(callback);
    };

    ItemServiceImpl.prototype.finish = function() {
        var self = this;
        for ( var i = 0; i < this.beforeFinishCallbacks.length; i++) {
            this.beforeFinishCallbacks[i]();
        }

        this.submit(function() {
            self.serviceApi.finish();
        });
    };

    ItemServiceImpl.prototype.submit = function(after) {
        var self = this;

        this.serviceApi.setState(JSON.stringify(self.stateVariables),
                function() {
                    // todo add item, call id etc

                    if (self.resultApi) {
                        self.resultApi
                                .submitItemVariables(self.itemId,
                                        self.serviceApi.getServiceCallId(),
                                        self.responses, self.scores,
                                        self.events, self.params, after);
                    } else {
                        after();
                    }
                });
    }

    ItemServiceImpl.prototype.onKill = function(callback) {
        this.serviceApi.onKill(callback);
    };

    ItemServiceImpl.prototype.getVariable = function(identifier, callback) {
        if (typeof callback === 'function') {
            callback(this.stateVariables[identifier] || null);
        }
    };

    ItemServiceImpl.prototype.setVariable = function(identifier, value) {
        this.stateVariables[identifier] = value;
        this.serviceApi.setState(JSON.stringify(this.stateVariables));
    };
    
    ItemServiceImpl.prototype.setVariables = function(values) {
        for(var v in values){
            this.stateVariables[v] = values[v];
        }
        this.serviceApi.setState(JSON.stringify(this.stateVariables));
    };
    
    return ItemServiceImpl;
});
define(['jquery', 'urlParser', 'iframeResizer'], function($, UrlParser, iframeResizer){
    
    function ServiceApi(baseUrl, parameters, serviceCallId, stateStorage, userService){
        this.baseUrl = baseUrl;
        this.parameters = parameters;
        this.connected = false;

        this.serviceCallId = serviceCallId; 
        this.state = stateStorage;
        this.userService = userService;

        this.onFinishCallback;
        this.onKillCallback;
        this.onDisplayChangeCallback;
    }

    ServiceApi.SIG_SUCCESS = 0; 
    ServiceApi.SIG_ERROR = 1;
        
    ServiceApi.prototype.loadInto = function(frame, connected){
        var self = this;
        var $frame = $(frame);
        var callUrl = this.getCallUrl();        
        var isCORSAllowed = new UrlParser(callUrl).checkCORS();

        $frame.one('load', function() {
            $(document).on('serviceready', function(){
                self.connect(frame, connected );
            });
            
        }).on('load.cors', function(e){
            //if we are  in the same domain, we add a variable
            //to the frame window, so the frame knows it can communicate
            //with the parent
            if(isCORSAllowed === true){
                frame.contentWindow.__knownParent__ = true;
            }
        });
        $frame.attr('src', callUrl);
    };

    ServiceApi.prototype.connect = function(frame, connected){
        if(this.connected === false && frame.contentWindow){
            //frame.contentWindow.serviceApi = this;
            if (typeof(frame.contentWindow.onServiceApiReady) === "function") {
                frame.contentWindow.onServiceApiReady(this);
                this.connected = true;
                if(typeof connected === 'function'){
                    connected();
                }
            }
        }
    };
    
    /**
     * Get the service call URL
     * @returns {String} the URI
     */
    ServiceApi.prototype.getCallUrl = function(){
        var params = this.parameters || {};
        params.serviceCallId = this.serviceCallId;
        return this.baseUrl + '?' + $.param(params);
    };

    ServiceApi.prototype.getUserPropertyValues = function(property, callback){
    	this.userService.get(property, callback);
    };
    
    //Context
    ServiceApi.prototype.getServiceCallId = function(){
        return this.serviceCallId;
    };

    //Context
    ServiceApi.prototype.getState = function(){
        return this.state.get();
    };

    ServiceApi.prototype.setState = function(state, callback){
        return this.state.set(state, callback);
    };

    // Variables 
    ServiceApi.prototype.getParameter = function(identifier){
        if (typeof(this.parameters[identifier]) !== "undefined") {
            return this.parameters[identifier];
        } else {
            return null;
        }
    };

    ServiceApi.prototype.onFinish = function(callback) {
        this.onFinishCallback = callback;	
    };

    ServiceApi.prototype.onKill = function(callback) {
        this.onKillCallback = callback;	
    };
    
    ServiceApi.prototype.kill = function(callback) {
    	if (typeof this.onKillCallback == 'function') {
    		this.onKillCallback(callback);
    	} else {
    		callback(0);
    	}
    };
    
    // Flow
    // valueArray are return parameters of the service.
    ServiceApi.prototype.finish = function(valueArray) {
            //return execution to service caller
            if (typeof this.onFinishCallback === 'function') {
                    this.onFinishCallback(valueArray);
            }
    };

    return ServiceApi;

});

define(['jquery'], function($){
    
    function UserInfoService(requestUrl, data) {
        this.data = data;
        this.requestUrl = requestUrl;
    }

    UserInfoService.prototype.get = function(property, callback){
        if (this.data.hasOwnProperty(property)) {
            if (typeof callback === "function") {
                    callback(this.data[property]);
            }
        } else {
            $.ajax({
                url : this.requestUrl,
                data 		: {
                    'property' : property
                },
                type        : 'post',
                dataType	: 'json',
                success     : (function(service, callback) {return function(r) {
            		for (key in r.data) {
            			service.data[key] = r.data[key];
            		}
                    if (typeof callback === "function") {
                        callback(service.data[property]);
                    }

                }})(this, callback)
                /*	
            	function(r){
                    if(r.success){
                    	console.log(this.data);
                    	this.data[property] = r.values;
                    	//console.log(this.data);
	                    if (typeof callback === "function") {
	                        callback(this.data[property]);
	                    }
                    }
                }
                */
                
                
            });
        }
    };

    return UserInfoService;
});
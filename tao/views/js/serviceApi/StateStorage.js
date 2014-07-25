define(['jquery'], function($){
    
    function StateStorage(state, submitUrl) {
        this.state = state;
        this.submitUrl = submitUrl;
    }

    StateStorage.prototype.get = function(callback){
        if (typeof callback === 'function') {
                callback(this.state);
        }
        return this.state;
    };

    StateStorage.prototype.set = function(state, callback){

        if (state === this.state) {
            if (typeof callback === "function") {
                    callback();
            }
        } else {
            $.ajax({
                url : this.submitUrl,
                data 		: {
                    'state' : state
                },
                type        : 'post',
                dataType	: 'json',
                success     : typeof callback === "function" ? callback : null
            });
        }
    };

    return StateStorage;
});
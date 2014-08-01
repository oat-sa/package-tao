define(function(){
    
    function PseudoStorage() {
    }

    PseudoStorage.prototype.get = function(callback){
        if (typeof callback === 'function') {
            callback(null);
        }
        return null;
    };

    PseudoStorage.prototype.set = function(state, callback){
        if (typeof callback === "function") {
            callback();
        }
    };

    return PseudoStorage;
});
define(['taoQtiItem/qtiCreator/model/helper/container'], function(containerHelper){

    var methods = {
        createElements : function(body, callback){
            
            var _this = this;
            containerHelper.createElements(_this.getBody(), body, function(newElts){
                callback.call(_this, newElts);
            });

        }
    };

    return methods;
});
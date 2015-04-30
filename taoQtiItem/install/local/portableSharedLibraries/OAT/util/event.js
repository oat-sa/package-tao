define(['OAT/util/EventMgr'], function(EventMgr){

    return {
        addEventMgr : function(instance){

            var eventMgr = new EventMgr();

            instance.on = function on(event, callback){
                eventMgr.on(event, callback);
            };
            instance.off = function off(event){
                eventMgr.on(event);
            };
            instance.trigger = function trigger(event, data){
                eventMgr.trigger(event, data);
            };

        }
    };
});
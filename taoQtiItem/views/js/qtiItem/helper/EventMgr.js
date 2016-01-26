define(['lodash'], function(_){
    
    //@todo : complete with namespace managements
    function EventMgr(){
        
        var events = {};
        
        this.get = function(event){
            if(event && events[event]){
                return _.clone(events[event]);
            }else{
                return [];
            }
        };
        
        this.on = function(event, callback){
            var tokens = event.split('.');
            if(tokens[0]){
                var name = tokens.shift();
                events[name] = events[name] || [];
                events[name].push({
                    ns : tokens,
                    callback : callback
                });
            }
        };
        
        this.off = function(event){
            if(event && events[event]){
                events[event] = [];
            }
        };
        
        this.trigger = function(event, data){
            if(events[event]){
                _.each(events[event], function(e){
                    //@todo check ns:
                    e.callback.apply({
                        type : event,
                        ns : []
                    }, data);
                });
            }
        };
    }
    
    
    return EventMgr;
});
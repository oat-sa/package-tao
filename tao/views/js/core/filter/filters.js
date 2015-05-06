define(['lodash'], function(_){
    var Filters =  {
        
        register : function(name, filter){
             if(!_.isString(name)){
                throw new Error('An filter must have a valid name');
            }
            if(!_.isFunction(filter)){
                throw new Error('Filter must be a function');
            }
            this[name] = filter;
        },
        
        filter : function(name, value){
            if(this[name] && _.isArray(value)){
                return _.filter(value, this[name]);
            }
            return value;
        }
    };
    
    return Filters;
});


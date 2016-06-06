define(function(){
    
    //@todo : need refactoring of qti item mixin with lodash.mixin()
    return {
        augment : function(targetClass, methods, options){
            if(typeof(targetClass) === 'function' && typeof(methods) === 'object'){
                for(var methodName in methods){
                    if(!Object.hasOwnProperty(targetClass.prototype, methodName)){
                        targetClass.prototype[methodName] = methods[methodName];
                    }else{
                        if(options && options.append){
                            var _parent = targetClass.prototype[methodName];
                            targetClass.prototype[methodName] = function(){
                                methods[methodName].apply(this, arguments);
                                return _parent.apply(this, arguments);
                            }
                        }
                    }
                }
            }
        }
    }
});
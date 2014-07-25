define(['lodash', 'jquery'], function(_, $){

    var rendererConfigHelper = {};

    rendererConfigHelper.getOptionsFromArguments = function(args){

        var options = {
            data : {},
            placeholder : null,
            subclass : '',
            renderer : null
        };
        
        _.each(args, function(arg){
            if(arg){
                if(arg.isRenderer){
                    options.renderer = arg;
                }else if(arg instanceof $ && arg.length){
                    options.placeholder = arg;
                }else if(_.isString(arg)){
                    options.subclass = arg;
                }else if(_.isPlainObject(arg)){
                    options.data = arg;
                }else{
                    console.log('invalid arg', arg, args);
                }
            }
        });
        
        return options;
    };
    
    return rendererConfigHelper;
});

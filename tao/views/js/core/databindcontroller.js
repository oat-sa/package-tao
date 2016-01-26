    define(['jquery', 'lodash', 'core/databinder'], function($, _, DataBinder){
    'use strict';
    
    return {
        
        takeControl: function($container, options){
            var control = {};
            var model = {};
            var binderOpts = _.pick(options, function(value, key){
               return key ===  'encoders' || key === 'filters' || key === 'templates';
            });
            
            if(options.get){
                control.get = function get(cb, errBack){
                    $.getJSON(options.get).done(function(data){
                        if(data){
                            model = data;
                            new DataBinder($container, model, binderOpts).bind();
                            if(typeof cb === 'function'){
                                cb(model);
                            }
                        }
                    });
                    return this;
                };
            }
            if(options.save){
                control.save = function save(cb, errBack){
                    var allowSave = true;
                    if(typeof options.beforeSave === 'function'){
                        allowSave = !!options.beforeSave(model);
                    }
                    if(allowSave === true){
                        $.post(options.save, {model : JSON.stringify(model) }, function(data){
                            if(data){
                                if(typeof cb === 'function'){
                                    cb(data);
                                }
                            }
                        }, 'json').fail(function(){
                           if(typeof errBack === 'function'){
                               errBack();
                           }
                        });
                    }
                    return this;
                };
            }
            
            return control;
        }
    };
});


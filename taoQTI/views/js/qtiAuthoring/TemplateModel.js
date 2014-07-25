function TemplateModel(name, url, postRender){
    if(TemplateModel.instances[name]){
        throw 'redeclaration of template with identical name: ' + name;
    }else{
        var _this = this;
        
        if(!name.match(/^#[a-z]*$/i)){
            throw 'a template name must start with "#" and contains only alphanum character or underscore';
        }
        
        //protected properties;
        var thisProps = {
            'name' : name,
            'alias' : null,
            'url' : '',
            'postRender' : function(){
            },
            'compiled' : function(){
            },
            'tpl' : ''
        };

        thisProps.name = name;
        if(url.slice(1) === '#'){
            thisProps.alias = url;
        }else{
            thisProps.url = url;
            if(typeof(postRender) === 'function'){
                thisProps.postRender = postRender;
            }
        }

        this.name = function(){
            return thisProps.name;
        };

        this.alias = function(){
            return thisProps.alias;
        };

        this.url = function(){
            return thisProps.url;
        };

        this.postRender = function(){
            return thisProps.postRender;
        };

        this.tpl = function(){
            return thisProps.tpl;
        };

        this.compile = function(callback){
            if(thisProps.tpl){
                thisProps.compiled = Mustache.compile(tpl);
                callback();
            }else{
                thisProps.load(function(){
                    _this.compile(callback)
                });
            }
        };

        this.load = function(callback){
            $.get(thisProps.url).done(function(tpl){
                thisProps.tpl = tpl;
                callback();
            });
        };

        this.render = function(data, $container, callback){
            if(_this.compiled){
                var html = _this.compiled(data);
                if($container.length){
                    $container.html(html);
                    callback();
                }
            }else{
                _this.compile(function(){
                    _this.render(data, $container, callback)
                });
            }
        }

        this.debug = thisProps;
    }
}
TemplateModel.instances = [];
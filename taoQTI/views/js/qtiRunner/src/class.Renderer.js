Qti.Renderer = Class.extend({
    init : function(){
        this.registerTpls(this.getRawTemplates());
        this.registerPostRenderers(this.getPostRenderers());
    },
    registerTpls : function(rawTpls){
        this.tpls = {};
        for(var name in rawTpls){
            var tpl = rawTpls[name];
            if(typeof tpl === 'string' && tpl.match(/^#[a-z]*$/i)){//check alias
                if(rawTpls[tpl]){
                    this.tpls[name] = tpl;
                }else{
                    throw 'undefined tpl alias: ' + name;
                }
            }else{
                this.tpls[name] = {
                    'raw' : tpl,
                    'compiled' : null
                };
            }
        }
    },
    registerPostRenderers : function(postRenders){
        this.postRenders = {};
        for(var name in postRenders){
            if(!name.match(/^#[a-z]*$/i)){
                throw 'invalid postRenderName';
            }
            var fn = postRenders[name];
            if(typeof fn === 'string' && fn.match(/^#[a-z]*$/i)){//check alias
                if(postRenders[fn]){
                     this.postRenders[name] = fn;
                }else{
                    throw 'undefined postRenders alias: ' + name;
                }
            }else if(typeof fn === 'function'){
                this.postRenders[name] = fn;
            }else{
                'invalid postRender function in declaration';
            }
        }
    },
    buildTpl : function(tpl){
        if(tpl && tpl.join){
            return tpl.join('\n');
        }else if(typeof tpl === 'string'){
            return tpl;
        }else{
            CL('not array');
        }
    },
    renderTpl : function(tplName, data){
        var ret = '';
        if(this.tpls['#' + tplName]){
            var tpl = this.tpls['#' + tplName];
            if(typeof tpl === 'string' && tpl.match(/^#[a-z]*$/i)){
                ret = this.renderTpl(tpl.slice(1), data);//use alias
            }else{
                if(typeof tpl.compiled !== 'function'){
                    tpl.compiled = this.compileTpl(this.buildTpl(tpl.raw), data);
                }
                ret = tpl.compiled(data);
            }
        }else{
            throw 'the template name cannot be found : ' + tplName;
        }

        return ret;
    },
    renderDirect : function(tpl, data){
        return Mustache.render(tpl, data);
    },
    compileTpl : function(tpl){
        return Mustache.compile(tpl);
    },
    getRawTemplates : function(){
        throw 'method to be overwritten';
    },
    postRender:function(postRenderName, qtiElement, data){
        var ret = false;
        if(this.postRenders['#' + postRenderName]){
            var fn = this.postRenders['#' + postRenderName];
            if(typeof fn === 'string' && fn.match(/^#[a-z]*$/i)){
                ret = this.postRender(fn.slice(1), qtiElement, data);//use alias
            }else{
                ret = fn.call(this, qtiElement, data);
            }
        }else{
            //log it, not mandatory
            CL('the postRender callback not found : ' + postRenderName);
        }
        return ret;
    }
});
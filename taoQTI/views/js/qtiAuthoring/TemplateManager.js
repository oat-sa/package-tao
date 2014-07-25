function TemplateManager(){
    this.tpls = {};
}

TemplateManager.prototype.createTpl = function(name, url, postRender){
    if(!this.tpls[name]){
        this.register(new TemplateModel(name, url, postRender));
    }
};

TemplateManager.prototype.register = function(tplModel, onceOnly){
    if(tplModel instanceof TemplateModel){
        if(tplModel.alias()){
            if(!this.tpls[tplModel.alias()]){
                throw 'template alias not registered: ' + tplModel.alias();
            }else{
                this.tpls[tplModel.name()] = tplModel;
            }
        }else{
            if(onceOnly && this.tpls[tplModel.name()]){
                CL('tpl already registered', tplModel.name());
            }else{
                this.tpls[tplModel.name()] = tplModel;
            }
            
        }
    }
};

TemplateManager.prototype.renderTpl = function(tplName, data, callback){
    if(this.tpls['#' + tplName]){
        var tpl = this.tpls['#' + tplName];
        if(typeof(tpl.alias()) === 'string'  && tpl.alias().match(/^#[a-z]*$/i)){
            this.renderTpl(tpl.alias().slice(1), data, callback);//use alias
        }else{
            tpl.render(data, data);
        }
    }else{
        throw 'the template not found : ' + tplName;
    }
};

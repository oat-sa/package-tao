Qti.Element = Class.extend({
    qtiTag : '',
    serial : '',
    attributes : {},
    relatedItem : null,
    init : function(serial, attributes /*,body*/){
        if(!serial){
            serial = Qti.Element.buildSerial(this.qtiTag + '_');
        }
        if(serial && (typeof serial !== 'string' || !serial.match(/^[a-z_0-9]*$/))){
            throw 'invalid QTI serial: (' + (typeof serial) + ') ' + serial;
        }
        if(!Qti.Element.instances[serial]){
            Qti.Element.instances[serial] = this;
            this.serial = serial;
            this.setAttributes(attributes);
        }else{
            throw 'a QTI Element with the same serial already exists ' + serial;
        }
        if(typeof this.initContainer === 'function'){
            this.initContainer(arguments[2] || '');
        }
        if(typeof this.initObject === 'function'){
            this.initObject();
        }
    },
    placeHolder : function(){
        return '{{' + this.serial + '}}';
    },
    getSerial : function(){
        return this.serial;
    },
    id : function(value){
        return this.attr('identifier', value);
    },
    attr : function(name, value){
        if(name){
            if(value){
                this.attributes[name] = value;
            }else{
                return this.attributes[name] || null;
            }
        }
    },
    setAttributes : function(attributes){
        this.attributes = attributes;
    },
    getAttributes : function(){
        var attrs = {};
        for(var name in this.attributes){
            attrs[name] = this.attributes[name];
        }
        return attrs;
    },
    getComposingElements : function(){
        var elts = {};
        if(typeof this.initContainer === 'function'){
            var container = this.getBody();
            elts[container.getSerial()] = container;//pass individual object by ref, instead of the whole list(object)
            elts = $.extend(elts, container.getComposingElements());
        }
        if(typeof this.initObject === 'function'){
            var object = this.getObject();
            elts[object.getSerial()] = object;//pass individual object by ref, instead of the whole list(object)
            elts = $.extend(elts, object.getComposingElements());
        }
        return elts;
    },
    setRelatedItem : function(item, recursive){

        recursive = (typeof recursive === 'undefined') ? true : recursive;

        if(item instanceof Qti.Item){
            this.relatedItem = item;
            var composingElts = this.getComposingElements();
            for(var i in composingElts){
                composingElts[i].setRelatedItem(item, false);
            }
        }

    },
    getRelatedItem : function(){
        if(this.relatedItem instanceof Qti.Item){
            return this.relatedItem;
        }
    },
    setRenderer : function(renderer){
        if(renderer instanceof Qti.Renderer){
            this.renderer = renderer;
            var elts = this.getComposingElements();
            for(var serial in elts){
                elts[serial].setRenderer(renderer);
            }
        }else{
            throw 'invalid qti rendering engine';
        }
    },
    getRenderer : function(){
        return this.renderer;
    },
    render : function(data, $container, tplName){
        var renderer = this.getRenderer();
        if(!renderer){
            throw 'no renderer found';
        }

        var defaultData = {
            'tag' : this.qtiTag,
            'serial' : this.serial,
            'attributes' : this.attributes
        };

        if(typeof this.initContainer === 'function'){
            defaultData.body = this.getBody().render(renderer);
        }
        if(typeof this.initObject === 'function'){
            defaultData.object = {
                attributes : this.object.getAttributes()
            };
            //@todo clean this;
            var url = defaultData.object.attributes.data;
            if(typeof(qti_base_www) !== 'undefined'){
                if(!/^http(s)?:\/\//.test(url)){
                    defaultData.object.attributes.data = qti_base_www + url;
                }
            }
        }

        var tplData = $.extend(true, defaultData, data || {});
        var rendering = renderer.renderTpl(tplName || this.qtiTag, tplData);
        if($container){
            if($container.length && $container.replaceWith){
                //if is a jquery container
                $container.replaceWith(rendering);
            }else{
                throw 'invalid jQuery container in arg';
            }
        }

        return rendering;
    },
    toArray : function(){
        var arr = {
            serial : this.serial,
            type : this.qtiTag,
            attributes : this.getAttributes()
        }
        if(typeof this.initContainer === 'function'){
            arr.body = this.getBody().toArray();
        }
        if(typeof this.initObject === 'function'){
            arr.object = this.object.toArray();
        }

        return arr;
    }
});
Qti.Element.instances = [];
Qti.Element.buildSerial = function(prefix){
    var id = prefix || '';
    var chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    for(var i = 0; i < 22; i++){
        id += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return id;
}


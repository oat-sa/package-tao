define(['class', 'lodash', 'taoQtiItem/qtiItem/helper/util', 'taoQtiItem/qtiItem/helper/rendererConfig'], function(Class, _, util, rendererConfig){

    var _instances = {};

    var Element = Class.extend({
        qtiClass : '',
        serial : '',
        relatedItem : null,
        init : function(serial, attributes){

            //init own attributes
            this.attributes = {};

            //system properties, for item creator internal use only
            this.metaData = {};

            //init call in the format init(attributes)
            if(typeof(serial) === 'object'){
                attributes = serial;
                serial = '';
            }

            if(!serial){
                serial = util.buildSerial(this.qtiClass + '_');
            }

            if(serial && (typeof serial !== 'string' || !serial.match(/^[a-z_0-9]*$/i))){
                throw 'invalid QTI serial : (' + (typeof serial) + ') ' + serial;
            }

            if(!_instances[serial]){
                _instances[serial] = this;
                this.serial = serial;
                this.setAttributes(attributes || {});
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
        is : function(qtiClass){
            return (qtiClass === this.qtiClass);
        },
        placeholder : function(){
            return '{{' + this.serial + '}}';
        },
        getSerial : function(){
            return this.serial;
        },
        getUsedIdentifiers : function(){
            var usedIds = {};
            var elts = this.getComposingElements();
            for(var i in elts){
                var elt = elts[i];
                var id = elt.attr('identifier');
                if(id){
                    //warning: simplistic implementation, allow only one unique identifier in the item no matter the element class/type
                    usedIds[id] = elt;
                }
            }
            return usedIds;
        },
        attr : function(name, value){
            if(name){
                if(value !== undefined){
                    this.attributes[name] = value;
                }else{
                    if(typeof(name) === 'object'){
                        for(var prop in name){
                            this.attr(prop, name[prop]);
                        }
                    }else if(typeof(name) === 'string'){
                        if(this.attributes[name] === undefined){
                            return undefined;
                        }else{
                            return this.attributes[name];
                        }
                    }
                }
            }
            return this;
        },
        data : function(name, value){
            if(name){
                if(value !== undefined){
                    this.metaData[name] = value;
                    $(document).trigger('metaChange.qti-widget', {element : this, key : name, value : value});
                }else{
                    if(typeof(name) === 'object'){
                        for(var prop in name){
                            this.data(prop, name[prop]);
                        }
                    }else if(typeof(name) === 'string'){
                        if(this.metaData[name] === undefined){
                            return undefined;
                        }else{
                            return this.metaData[name];
                        }
                    }
                }
            }
            return this;
        },
        removeData : function(name){
            delete this.metaData[name];
            return this;
        },
        removeAttr : function(name){
            return this.removeAttributes(name);
        },
        setAttributes : function(attributes){
            this.attributes = attributes;
            return this;
        },
        getAttributes : function(){
            return _.clone(this.attributes);
        },
        removeAttributes : function(attrNames){
            if(typeof(attrNames) === 'string'){
                attrNames = [attrNames];
            }
            for(var i in attrNames){
                delete this.attributes[attrNames[i]];
            }
            return this;
        },
        getComposingElements : function(){
            var elts = {};
            if(typeof this.initContainer === 'function'){
                var container = this.getBody();
                elts[container.getSerial()] = container;//pass individual object by ref, instead of the whole list(object)
                elts = _.extend(elts, container.getComposingElements());
            }
            if(typeof this.initObject === 'function'){
                var object = this.getObject();
                elts[object.getSerial()] = object;//pass individual object by ref, instead of the whole list(object)
                elts = _.extend(elts, object.getComposingElements());
            }
            return elts;
        },
        getUsedClasses : function(){

            var ret = [this.qtiClass],
                composingElts = this.getComposingElements();

            _.each(composingElts, function(elt){
                ret.push(elt.qtiClass);
            });

            return _.uniq(ret);
        },
        find : function(serial){

            var found = null;

            if(typeof this.initObject === 'function'){
                var object = this.getObject();
                if(object.serial === serial){
                    found = {'parent' : this, 'element' : object, 'location' : 'object'};
                }
            }

            if(!found && typeof this.initContainer === 'function'){
                found = this.getBody().find(serial, this);
            }

            return found;
        },
        parent : function(){
            var item = this.getRelatedItem();
            if(item){
                var found = item.find(this.getSerial());
                if(found){
                    return found.parent;
                }
            }
            return null;
        },
        setRelatedItem : function(item, recursive){

            recursive = (typeof recursive === 'undefined') ? true : recursive;

            if(Element.isA(item, 'assessmentItem')){
                this.relatedItem = item;
                var composingElts = this.getComposingElements();
                for(var i in composingElts){
                    composingElts[i].setRelatedItem(item, false);
                }
            }

        },
        getRelatedItem : function(){
            var ret = null;
            if(Element.isA(this.relatedItem, 'assessmentItem')){
                ret = this.relatedItem;
            }
            return ret;
        },
        setRenderer : function(renderer){
            if(renderer && renderer.isRenderer){
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
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments);
            var renderer = args.renderer || this.getRenderer();

            var tplData = {},
                defaultData = {
                'tag' : this.qtiClass,
                'serial' : this.serial,
                'attributes' : this.getAttributes()
            };

            if(!renderer){
                throw 'render: no renderer found for the element ' + this.qtiClass + ':' + this.serial;
            }

            if(typeof this.initContainer === 'function'){
                defaultData.body = this.getBody().render(renderer);
            }
            if(typeof this.initObject === 'function'){
                defaultData.object = {
                    attributes : this.object.getAttributes()
                };
                var url = defaultData.object.attributes.data;
                var baseUrl = renderer.getOption('baseUrl');
                if(baseUrl){
                    if(!/^http(s)?:\/\//.test(url)){
                        defaultData.object.attributes.data = baseUrl + url;
                    }
                }
            }

            tplData = _.merge(defaultData, args.data || {});
            tplData = renderer.getData(this, tplData, args.subclass);
            var rendering = renderer.renderTpl(this, tplData, args.subclass);
            if(args.placeholder){
                args.placeholder.replaceWith(rendering);
            }

            return rendering;
        },
        postRender : function(data, altClassName, renderer){

            renderer = renderer || this.getRenderer();

            if(typeof this.initContainer === 'function'){
                //post render body element
                this.getBody().postRender({}, '', renderer);
            }

            if(renderer){
                return renderer.postRender(this, data, altClassName);
            }else{
                throw 'postRender: no renderer found for the element ' + this.qtiClass + ':' + this.serial;
            }
        },
        getContainer : function($scope, subclass){
            var renderer = this.getRenderer();
            if(renderer){
                return renderer.getContainer(this, $scope, subclass);
            }else{
                throw 'getContainer: no renderer found for the element ' + this.qtiClass + ':' + this.serial;
            }
        },
        toArray : function(){
            var arr = {
                serial : this.serial,
                type : this.qtiClass,
                attributes : this.getAttributes()
            };

            if(typeof this.initContainer === 'function'){
                arr.body = this.getBody().toArray();
            }
            if(typeof this.initObject === 'function'){
                arr.object = this.object.toArray();
            }

            return arr;
        },
        isEmpty : function(){
            //tells whether the element should be considered empty or not, from the rendering point of view
            return false;
        },
        addClass : function(className){
            var clazz = this.attr('class') || '';
            if(!_containClass(clazz, className)){
                this.attr('class', clazz + (clazz.length ? ' ' : '') + className);
            }
        },
        hasClass : function(className){
            return _containClass(this.attr('class'), className);
        },
        removeClass : function(className){

            var clazz = this.attr('class') || '';
            if(clazz){
                var regex = new RegExp('(?:^|\\s)' + className + '(?:\\s|$)', '');
                clazz = clazz.replace(regex, '').replace(/^\s+/, '');
                if(clazz){
                    this.attr('class', clazz);
                }else{
                    this.removeAttr('class');
                }
            }
        }
    });

    var _containClass = function(allClassStr, className){
        var regex = new RegExp('(?:^|\\s)' + className + '(?:\\s|$)', '');
        return allClassStr && regex.test(allClassStr);
    };

    //helpers
    Element.isA = function(qtiElement, qtiClass){
        return (qtiElement instanceof Element && qtiElement.is(qtiClass));
    };

    Element.getElementBySerial = function(serial){
        return _instances[serial];
    };

    Element.unsetElement = function(serial){
        delete _instances[serial];
    };

    return Element;
});



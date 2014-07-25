define(['lodash', 'taoQtiItem/qtiItem/core/Element', 'taoQtiItem/qtiItem/helper/rendererConfig'], function(_, Element, rendererConfig){

    var _stripMathTags = function(mathML, nsName){
        var regex = new RegExp('<(\/)?' + (nsName ? nsName + ':' : '') + 'math[^>]*>', 'g');
        return mathML.replace(regex, '')
            .replace(/^\s*[\r\n]/gm, '')//remove first blank line
            .replace(/\s*[\r\n]$/gm, '');//last blank line
    };

    var _stripNamespace = function(mathML, nsName){
        var regex = new RegExp('<(\/)?' + (nsName ? nsName + ':' : ''), 'g');
        return mathML.replace(regex, '<$1');
    };

    var Math = Element.extend({
        qtiClass : 'math',
        defaultNsName : 'm',
        defaultNsUri : 'http://www.w3.org/1998/Math/MathML',
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.ns = null;
            this.mathML = '';
            this.annotations = {};
        },
        getNamespace : function(){

            if(this.ns && this.ns.name && this.ns.uri){
                return _.clone(this.ns);
            }else{
                
                var relatedItem = this.getRelatedItem();
                if(relatedItem){
                    var namespaces = relatedItem.getNamespaces();
                    for(ns in namespaces){
                        if(namespaces[ns].indexOf('MathML') > 0){
                            return {
                                name : ns,
                                uri : namespaces[ns]
                            };
                        }
                    }
                    //if no ns found in the item, set the default one!
                    relatedItem.namespaces[this.defaultNsName] = this.defaultNsUri;
                    return {
                        name : this.defaultNsName,
                        uri : this.defaultNsUri
                    };
                }
            }

            return {};
        },
        setNamespace : function(name, uri){
            this.ns = {
                name : name,
                uri : uri
            };
        },
        setAnnotation : function(encoding, value){
            this.annotations[encoding] = value;
        },
        getAnnotation : function(encoding){
            return this.annotations[encoding];
        },
        removeAnnotation : function(encoding){
            delete this.annotations[encoding];
        },
        setMathML : function(mathML){
            var ns = this.getNamespace(),
                nsName = (ns.name && ns.uri) ? ns.name : '';

            mathML = _stripMathTags(mathML, nsName);
            if(ns){
                mathML = _stripNamespace(mathML, nsName)
            }
            this.mathML = mathML;
        },
        getMathML : function(){
            return this.mathML;
        },
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                tag = this.qtiClass,
                raw = this.mathML,
                body = raw,
                ns = this.getNamespace(),
                annotations = '';

            for(var encoding in this.annotations){
                annotations += '<annotation encoding="' + encoding + '">' + this.annotations[encoding] + '</annotation>';
            }

            if(annotations){
                if(raw.indexOf('</semantics>') > 0){
                    raw = raw.replace('</semantics>', annotations + '</semantics>');
                }else{
                    raw = '<semantics>' + raw + annotations + '</semantics>';
                }
            }

            if(ns && ns.name){
                body = raw.replace(/<(\/)?([^!])/g, '<$1' + ns.name + ':$2');
                tag = ns.name + ':' + tag;
            }

            var defaultData = {
                block : (this.attr('display') === 'block') ? true : false,
                body : body,
                raw : raw,
                tag : tag,
                ns : ns
            };

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        },
        isEmpty : function(){
            return _isEmptyMathML(this.mathML) && (!this.annotations['latex'] || !this.annotations['latex'].trim());
        }
    });

    var _isEmptyMathML = function(mathStr){

        var hasContent = false;

        if(mathStr && mathStr.trim()){
            var $math = $(mathStr);
            hasContent = !!$math.text();
        }

        return !hasContent;
    }

    return Math;
});
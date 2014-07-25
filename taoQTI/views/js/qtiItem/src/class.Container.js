Qti.Container = Qti.Element.extend({
    qtiTag : '_container',
    init : function(body){
        if(body && typeof body !== 'string'){
            throw 'the body of a container must be a string';
        }
        this.bdy = '';
        this.body(body || '');
        this.elements = [];
        this._super();//generate serial, attributes array always empty
    },
    body : function(body){
        if(typeof body === 'undefined'){
            return this.bdy;
        }else{
            if(typeof body === 'string'){
                this.bdy = body;
            }else{
                throw 'body must be a string';
            }
        }
    },
    setElement : function(element, body){
        var returnValue = false;
        if(element instanceof Qti.Element){
            element.setRelatedItem(this.getRelatedItem() || null);
            this.elements[element.getSerial()] = element;
            this.body(body);
            returnValue = true;
        }else{
            throw 'expected a qti element';
        }
        return returnValue;
    },
    getElements : function(type){
        var elts = {};
        if(typeof(type) === 'string'){
            if(Qti[type]){
                for(var serial in this.elements){
                    if(this.elements[serial] instanceof Qti[type]){
                        elts[serial] = this.elements[serial];
                    }
                }
            }else{
                throw 'Qti.' + type + ' is not a valid Qti Element subclass';
            }
        }else{
            elts = this.elements;
        }
        return elts;
    },
    getComposingElements : function(){
        var elements = this.getElements();
        var elts = {};
        for(var serial in elements){
            elts[serial] = elements[serial];//pass individual object by ref, instead of the whole list(object)
            elts = $.extend(elts, elements[serial].getComposingElements());
        }
        return elts;
    },
    render : function(renderer){
        var data = [];
        var tpl = this.body();
        for(var serial in this.elements){
            var elt = this.elements[serial];
            if(typeof elt.render === 'function'){
                tpl = tpl.replace(elt.placeHolder(), '{{{' + serial + '}}}');
                data[serial] = elt.render();
            }else{
                throw 'render() is not defined for the qti element: ' + serial;
            }
        }

        if(renderer instanceof Qti.Renderer){
            return renderer.renderDirect(tpl, data);
        }else{
            throw 'invalid qti renderer for qti container';
        }
    },
    postRender : function(renderer){
        for(var serial in this.elements){
            var elt = this.elements[serial];
            if(typeof elt.postRender === 'function'){
                elt.postRender();
            }else{
//                CL('no postRender() method defined for the qti element: ' + serial);
            }
        }
    },
    toArray : function(){
        var arr = {
            serial : this.serial,
            body : this.bdy,
            elements:{}
        };
        
        for(var serial in this.elements){
            arr.elements[serial] = this.elements[serial].toArray();
        }
        
        return arr;
    }
});



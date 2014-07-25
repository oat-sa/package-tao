Qti.Interaction = Qti.Element.extend({
    init : function(serial, attributes, body){
        this._super(serial, attributes);
        this.choices = [];
    },
    addChoice : function(choice){
        choice.setRelatedItem(this.getRelatedItem() || null);
        this.choices[choice.getSerial()] = choice;
    },
    getChoices : function(){
        var choices = {};
        for(var i in this.choices){//prevent passing the whole array by ref
            choices[i] = this.choices[i];
        }
        return choices;
    },
    getComposingElements : function(){
        var elts = this._super();
        //recursive to choices:
        for(var serial in this.choices){
            if(this.choices[serial] instanceof Qti.Choice){
                elts[serial] = this.choices[serial];
                elts = $.extend(elts, this.choices[serial].getComposingElements());
            }
        }
        return elts;
    },
    getResponseDeclaration : function(){
        var response = null;
        var responseId = this.attr('responseIdentifier');
        if(responseId){
            var item = this.getRelatedItem();
            if(item){
                response = item.getResponseDeclaration(responseId);
            }else{
                throw 'cannot get response of an interaction out of its item context';
            }
        }else{
            //create one:

        }
        return response;
    },
    render : function(data, $container){
        var defaultData = {
            '_type' : this.qtiTag.replace(/([A-Z])/g, function($1){
                return "_" + $1.toLowerCase();
            }),
            'choices' : []
        };
        
        try{
            var choices = (this.attr('shuffle')) ? this.shuffleChoices() : this.getChoices();
            for(var i in choices){
                if(choices[i] instanceof Qti.Choice){
                    defaultData.choices.push(choices[i].render());
                }
            }
        }catch(e){
            //leave choices empty in case of error
        }
        
        var tplData = $.extend(true, defaultData, data || {});

        return this._super(tplData, $container);
    },
    postRender : function(data){
        var choices = this.getChoices();
        for(var i in choices){
            var c = choices[i];
            if(c instanceof Qti.ContainerChoice){
                c.getBody().postRender();
            }
        }
        var renderer = this.getRenderer();
        if(renderer){
            renderer.postRender(this.qtiTag, this, data);
        }else{
            throw 'no renderer found';
        }
    },
    setResponse : function(values){
        var ret = null;
        var renderer = this.getRenderer();
        if(renderer){
            ret = renderer.setResponse(this, values);
        }else{
            throw 'no renderer found';
        }
        return ret;
    },
    getResponse : function(){
        var ret = null;
        var renderer = this.getRenderer();
        if(renderer){
            ret = renderer.getResponse(this);
        }else{
            throw 'no renderer found';
        }
        return ret;
    },
    toArray : function(){
        var arr = this._super();
        arr.choices = {};
        for(var serial in this.choices){
            if(this.choices[serial] instanceof Qti.Choice){
                arr.choices[serial] = this.choices[serial].toArray();
            }
        }
        return arr;
    },
    shuffleChoices : function(choices){
        if(!choices)
            choices = this.getChoices();
        var r = [];//returned array
        var f = {};//fixed choices array
        var j = 0;
        for(var i in choices){
            if(choices[i] instanceof Qti.Choice){
                var choice = choices[i];
                if(choice.attr('fixed')){
                    f[j] = choice;
                }
                r.push(choice);
                j++;
            }else{
                throw 'invalid element in array: is not a qti choice';
            }
        }
        for(var n = 0; n < r.length - 1; n++){
            if(f[n]){
                continue;
            }
            var k = -1;
            do{
                k = n + Math.floor(Math.random() * (r.length - n));
            }while(f[k]);

            var tmp = r[k];
            r[k] = r[n];
            r[n] = tmp;
        }
        return r;
    }
});
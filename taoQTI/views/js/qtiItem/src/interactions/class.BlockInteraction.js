Qti.BlockInteraction = Qti.Interaction.extend({
    init : function(serial, attributes){
        this._super(serial, attributes);
        this.prompt = new Qti.Prompt('');
    },
    getComposingElements : function(){
        var elts = this._super();
        elts = $.extend(elts, this.prompt.getComposingElements());
        elts[this.prompt.getSerial()] = this.prompt;
        return elts;
    },
    render : function(data, $container){

        var defaultData = {
            'prompt' : this.prompt.render()
        };
        var tplData = $.extend(true, defaultData, data || {});

        return this._super(tplData, $container);

    },
    postRender : function(data){
        this.prompt.getBody().postRender();
        this._super(data);
    },
    toArray : function(){
        var arr = this._super();
        arr.prompt = this.prompt.toArray();
        return arr;
    }
});


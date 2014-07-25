Qti.TextVariableChoice = Qti.Choice.extend({
    init : function(serial, attributes, text){
        this._super(serial, attributes);
        this.val(text || '');
    },
    val : function(text){
        if(typeof text === 'undefined'){
            return this.text;
        }else{
            if(typeof text === 'string'){
                this.text = text;
            }else{
                throw 'text must be a string';
            }
        }
    },
    render : function(data, $container, tplName){
        var data = {
            'body' : this.text
        };
        return this._super(data, $container, tplName);
    }
});



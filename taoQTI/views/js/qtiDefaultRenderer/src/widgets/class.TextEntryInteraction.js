QtiWidget.DefaultWidget.TextEntryInteraction = QtiWidget.DefaultWidget.StringInteraction.extend({
    render : function(){
        //adapt the field length
        if(this.options['expectedLength']){
            var length = parseInt(this.options['expectedLength']);
            $('#' + this.id).css('width', length + 'em');
        }
        
        this._super();
    }
});
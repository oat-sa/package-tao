define(['taoQtiItem/qtiDefaultRenderer/widgets/StringInteraction'], function(StringInteraction){

    var TextEntryInteraction = StringInteraction.extend({
        render : function(){
            //adapt the field length
            if(this.options['expectedLength']){
                var length = parseInt(this.options['expectedLength']);
                $('#' + this.id).css('width', length + 'em');
            }

            this._super();
        }
    });
    
    return TextEntryInteraction;
});
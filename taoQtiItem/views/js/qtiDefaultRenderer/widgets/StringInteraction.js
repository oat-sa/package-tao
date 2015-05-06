define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(Widget){
    
    var StringInteraction = Widget.extend({
        render : function(){

            //add the error class if the value don't match the given pattern
            if(this.options['patternMask']){
                var pattern = new RegExp("^" + this.options['patternMask'] + "$");
                $('#' + this.id).keyup(function(){
                    $(this).removeClass('field-error');
                    if(!pattern.test($(this).val())){
                        $(this).addClass('field-error');
                    }
                });
            }

            //create a 2nd field to capture the string if the stringIdentifier has been defined
            if(this.options['stringIdentifier']){
                $('#' + this.id).after("<input type='hidden' id='" + this.options['stringIdentifier'] + "' />");
                $("#" + this.options['stringIdentifier']).addClass('qti_text_entry_interaction');
                $('#' + this.id).change(function(){
                    $("#" + this.options['stringIdentifier']).val($(this).val());
                });
            }

        },
        setResponse : function(values){
            var value = values;
            if(typeof(value) === 'string' && value != ''){
                $('#' + this.id).val(value);
            }
        },
        getResponse : function(){
            return this.resultCollector.text();
        }
    });

    return StringInteraction;
});
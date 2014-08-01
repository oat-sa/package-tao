define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(Widget){
    var InlineChoiceInteraction = Widget.extend({
        render : function(){
            //nothing to do;
        },
        setResponse : function(values){
            if(values){
                var value = values;
                if(typeof(value) === 'string' && value !== ''){
                    $('#' + this.id + " option[value='" + value + "']").attr('selected', true);
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.inline_choice();
        }
    });
    return InlineChoiceInteraction;
});
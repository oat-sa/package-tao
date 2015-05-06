define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(Widget){

    var HottextInteraction = Widget.extend({
        render : function(){
            var ctx = this;
            //the hottext behavior depends on the maxChoice value
            var maxChoices = (ctx.options['maxChoices']) ? parseInt(ctx.options['maxChoices']) : 1;
            $('#' + ctx.id + " .hottext_choice").click(function(){

                //no behavior restriction
                if(maxChoices === 0){
                    $(this).toggleClass('hottext_choice_on')
                        .toggleClass('hottext_choice_off');
                }

                //only one selected at a time 
                if(maxChoices === 1){
                    if($('#' + ctx.id + " .hottext_choice").length === 1){
                        $(this).toggleClass('hottext_choice_on').toggleClass('hottext_choice_off');
                    }else{
                        $('#' + ctx.id + " .hottext_choice").removeClass('hottext_choice_on').addClass('hottext_choice_off');
                        $(this).removeClass('hottext_choice_off').addClass('hottext_choice_on');
                    }
                }

                //there is only maxChoices selected at a time
                if(maxChoices > 1){
                    if($('#' + ctx.id + " .hottext_choice_on").length < maxChoices || $(this).hasClass('hottext_choice_on')){
                        $(this).toggleClass('hottext_choice_on').toggleClass('hottext_choice_off');
                    }
                }
            });
        },
        setResponse : function(values){

            var ctx = this;
            if(typeof(values) === 'string' && values != ''){
                $('#' + ctx.id + " #hottext_choice_" + values).addClass('hottext_choice_on').removeClass('hottext_choice_off');
            }else if(typeof(values) === 'object'){
                for(var i in values){
                    var value = values[i];
                    if(typeof(value) === 'string' && value != ''){
                        $('#' + ctx.id + " #hottext_choice_" + value).addClass('hottext_choice_on').removeClass('hottext_choice_off');
                    }
                }
            }

        },
        getResponse : function(){
            return this.resultCollector.hottext();
        }
    });
    
    return HottextInteraction;
});
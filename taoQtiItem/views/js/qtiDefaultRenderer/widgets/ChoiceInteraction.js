define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(Widget){

    var ChoiceInteraction = Widget.extend({
        render : function(){
            var maxChoices = parseInt(this.options["maxChoices"]);
            if(maxChoices > 1 || maxChoices === 0){
                this.renderMultiChoice();
            }else{
                this.renderSingleChoice();
            }
        },
        setResponse : function(values){
            var maxChoices = parseInt(this.options["maxChoices"]);
            if(maxChoices > 1 || maxChoices === 0){
                this.setMultiChoiceResponse(values);
            }else{
                this.setSingleChoiceResponse(values);
            }
        },
        getResponse : function(){
            return this.resultCollector.choice();
        },
        renderSingleChoice : function(){
            var ctx = this;

            //add the main class
            $('#' + this.id).addClass('qti_simple_interaction');

            //change the class to activate the choice on click
            $('#' + this.id + " ul li").bind("click", function(){
                $('#' + ctx.id + " ul li").removeClass("tabActive");
                $(this).addClass("tabActive");
            });
        },
        renderMultiChoice : function(){
            var ctx = this;

            $('#' + ctx.id).addClass('qti_multi_interaction');

            //change the class to activate the choices on click
            $('#' + ctx.id + " ul li").bind("click", function(){
                if($(this).hasClass("tabActive")){
                    $(this).removeClass("tabActive");
                }else{
                    if($('#' + ctx.id + " ul li.tabActive").length < ctx.options["maxChoices"] || ctx.options["maxChoices"] == 0){
                        $(this).addClass("tabActive");
                    }
                }
            });
        },
        setSingleChoiceResponse : function(value){
            if(value){
                if(typeof(value) === 'string' && value !== ''){
                    $('#' + this.id + " ul li#" + value).addClass("tabActive");
                }
            }
        },
        setMultiChoiceResponse : function(values){
            if(values){
                if(typeof(values) === 'object'){
                    for(var i in values){
                        var value = values[i];
                        if(typeof(value) === 'string' && value !== ''){
                            $('#' + this.id + " ul li#" + value).addClass("tabActive");
                        }
                    }
                }
            }
        }
    });
    
    return ChoiceInteraction;
});
define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(Widget){

    var OrderInteraction = Widget.extend({
        render : function(){

            var ctx = this;
            var suffixe = "";

            // test direction
            if(ctx.options.orientation == "horizontal"){
                // horizontal sortable options
                $('#' + ctx.id + " .qti_choice_list").removeClass("qti_choice_list").addClass("qti_choice_list_horizontal");

                var sortableOptions =
                    {
                        placeholder : 'sort-placeholder',
                        axis : 'x',
                        containment : '#' + ctx.id,
                        tolerance : 'pointer',
                        forcePlaceholderSize : true,
                        opacity : 0.8,
                        start : function(event, ui){
                            $('#' + ctx.id + " .sort-placeholder").width($("#" + ui.helper[0].id).width() + 4);
                            $('#' + ctx.id + " .sort-placeholder").height($("#" + ui.helper[0].id).height() + 4);
                            $("#" + ui.helper[0].id).css("top", "-4px");
                        },
                        beforeStop : function(event, ui){
                            $("#" + ui.helper[0].id).css("top", "0");
                        }
                    };
                // create suffix for common treatment
                suffixe = "_horizontal";
            }else{
                // verticale sortable options
                var sortableOptions =
                    {
                        placeholder : 'sort-placeholder',
                        axis : 'y',
                        containment : '#' + ctx.id,
                        tolerance : 'pointer',
                        forcePlaceholderSize : true,
                        opacity : 0.8,
                        start : function(event, ui){
                            $('#' + ctx.id + " .sort-placeholder").width($("#" + ui.helper[0].id).width() + 4);
                            $('#' + ctx.id + " .sort-placeholder").height($("#" + ui.helper[0].id).height() + 4);
                            $("#" + ui.helper[0].id).css("top", "-4px");
                        },
                        beforeStop : function(event, ui){
                            $("#" + ui.helper[0].id).css("top", "0");
                        }
                    };
            }
            //for an horizontal sortable list

            if(ctx.options.orientation == "horizontal"){
                $('#' + ctx.id).append("<div class='sizeEvaluator'></div>");
                $('#' + ctx.id + " .sizeEvaluator").css("font-size", $('#' + ctx.id + " .qti_choice_list_horizontal li").css("font-size"));

                var containerWidth = 0;
                $('#' + ctx.id + " .qti_choice_list_horizontal li").each(function(){
                    $('#' + ctx.id + " .sizeEvaluator").text($(this).text());
                    var liSize = $('#' + ctx.id + " .sizeEvaluator").width();

                    var liChildren = $(this).children();
                    if(liChildren.length > 0){
                        $.each(liChildren, function(index, elt){
                            liSize += $(elt).width();
                        });
                    }
                    $(this).width(liSize + 10);
                    containerWidth += liSize + 20;
                });
                if(parseInt($('#' + ctx.id).width()) < containerWidth){
                    $('#' + ctx.id).width(containerWidth + 'px')
                        .css({'overflow-x' : 'auto'});
                }

                $('#' + ctx.id + " .sizeEvaluator").remove();

            }
            $('#' + ctx.id + " .qti_choice_list" + suffixe).sortable(sortableOptions);
            $('#' + ctx.id + " .qti_choice_list" + suffixe).disableSelection();

            // container follow the height dimensions
            $('#' + ctx.id).append("<div class='breaker'> </div>");
        },
        setResponse : function(values){
            var ctx = this;
            //if the values are defined
            if(typeof(values) === 'object'){

                //we take the list element corresponding to the given ids 
                var list = new Array();
                for(var i in values){
                    var value = values[i];
                    if(typeof(value) === 'string' && value != ''){
                        list.push($('#' + ctx.id + " ul.qti_choice_list li#" + value));
                    }
                }

                //and we reorder the elements in the list
                if(list.length === $('#' + ctx.id + " ul.qti_choice_list li").length && list.length > 0){
                    $('#' + ctx.id + " ul.qti_choice_list").empty();
                    for(var i in list){
                        $('#' + ctx.id + " ul.qti_choice_list").append(list[i]);
                    }
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.order();
        }
    });

    return OrderInteraction;
});
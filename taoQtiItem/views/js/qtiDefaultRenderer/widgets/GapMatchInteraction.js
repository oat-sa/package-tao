define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget', 'jqueryui'], function(Widget){

    var GapMatchInteraction = Widget.extend({
        maxBoxSize : 0,
        init : function(interaction){
            this._super(interaction);
            this.options.matchMaxes = {};
            for(var i in interaction.choices){
                var choice = interaction.choices[i];
                this.options.matchMaxes[choice.attr('identifier')] = {
                    'matchMax' : parseInt(choice.attr('matchMax')),
                    'matchGroup' : [], //deprecated in qti 2.1
                    'current' : 0
                }
            }
            this.maxBoxSize = 0;
        },
        render : function(){

            var ctx = this;

            //add the container to the words cloud 
            $('#' + ctx.id + " .qti_choice_list").wrap("<div class='qti_gap_match_container'></div>");
            $('#' + ctx.id + " .qti_choice_list li").wrapInner("<div></div>");

            //add breaker
            $('#' + ctx.id + " .qti_choice_list li:last").after("<li><div></div></li>");
            $('#' + ctx.id + " .qti_choice_list li:last").css('clear', 'both');

            $('#' + ctx.id + " ul.qti_choice_list li > div").each(function(){
                if($(this).width() > ctx.maxBoxSize){
                    ctx.maxBoxSize = $(this).width();
                }
            });
            ctx.maxBoxSize = ((parseInt(ctx.maxBoxSize) / 2) + 5) + 'px';
            $('#' + ctx.id + " .gap").css({"padding-left" : ctx.maxBoxSize, "padding-right" : ctx.maxBoxSize});

            //drag element from words cloud
            $('#' + ctx.id + " .qti_gap_match_container .qti_choice_list li > div").draggable({
                drag : function(event, ui){
                    // label go on top of the others elements
                    $(ui.helper).css("z-index", "999");
                },
                containment : '#' + ctx.id,
                revert : true,
                cursor : "move"
            });

            $('#' + ctx.id + " .gap").html('&nbsp;');

            // pair box are droppable
            $('#' + ctx.id + " .gap").droppable({
                drop : function(event, ui){

                    var draggedId = $(ui.draggable).parent().attr("id");

                    //prevent of re-filling the gap and dragging between the gaps
                    if($(this).find("#gap_" + draggedId).length > 0 || /^gap_/.test($(ui.draggable).attr('id'))){
                        return false;
                    }

                    //remove the old element
                    if($(this).html() != ''){
                        $('.filled_gap', $(this)).each(function(){
                            ctx.removeFilledGap($(this));
                        });
                    }

                    //fill the gap
                    ctx.fillGap($(this), $(ui.draggable));

                },
                hoverClass : 'active'
            });

        },
        setResponse : function(values){
            if(typeof(values) === 'object'){
                for(var i in values){
                    var value = values[i];
                    if(value['0'] && value['1']){
                        var gap = value['0'];
                        var choice = value['1'];
                        this.fillGap($('#' + this.id + " .gap#" + gap), $('#' + this.id + " .qti_choice_list li#" + choice + " > div"));
                    }
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.gap_match();
        },
        /**
         * Fill a gap with an element of the word's cloud
         * @param {jQuery} jDropped
         * @param {jQuery} jDragged
         */
        fillGap : function(jDropped, jDragged){

            var ctx = this;
            var draggedId = jDragged.parent().attr("id");
            var _matchMax = Number(ctx.options["matchMaxes"][draggedId]["matchMax"]);
            var _current = Number(ctx.options["matchMaxes"][draggedId]["current"]);

            jDropped.addClass('dropped_gap');

            if(_current < _matchMax){
                _current++;
                ctx.options["matchMaxes"][draggedId]["current"] = _current;
            }
            if(_matchMax && _current >= _matchMax){
                jDragged.hide();
            }

            setTimeout(function(){
                //fix weird ff block displacement issue
                jDropped.css({'padding-left' : '5px', 'padding-right' : '5px'});
                jDropped.html("<span id='gap_" + draggedId + "' class='filled_gap'>" + jDragged.text() + "</span>");

                //enable to drop it back to remove it from the gap
                $('#' + ctx.id + " .filled_gap").draggable({
                    drag : function(event, ui){
                        // label go on top of the others elements
                        $(ui.helper).css("z-index", "999");
                        $(this).parent().addClass('ui-state-highlight');
                    },
                    stop : function(){
                        $(this).parent().removeClass('ui-state-highlight');
                        ctx.removeFilledGap($(this));
                    },
                    revert : false,
                    containment : '#' + ctx.id,
                    cursor : "move"
                });

            }, 500);
        },
        /**
         * remove an element from the filled gap
         * @param {jQuery} jElement
         */
        removeFilledGap : function(jElement){
            var filledId = jElement.attr("id").replace('gap_', '');
            var _matchMax = Number(this.options["matchMaxes"][filledId]["matchMax"]);
            var _current = Number(this.options["matchMaxes"][filledId]["current"]);
            if(_current > 0){
                this.options["matchMaxes"][filledId]["current"] = _current - 1;
            }
            jElement.parent().css({
                "padding-left" : this.maxBoxSize,
                "padding-right" : this.maxBoxSize
            }).removeClass('dropped_gap').html('&nbsp;');
            jElement.remove();
            if(_current >= _matchMax){
                $("#" + filledId + " div").show();
            }
        }
    });
    
    return GapMatchInteraction;
});
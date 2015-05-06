define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget', 'jqueryui'], function(Widget){

    var AssociateInteraction = Widget.extend({
        init : function(interaction, context){
            this._super(interaction, context);
            this.options.matchMaxes = {};
            for(var i in interaction.choices){
                var choice = interaction.choices[i];
                this.options.matchMaxes[choice.attr('identifier')] = {
                    'matchMax' : parseInt(choice.attr('matchMax')),
                    'matchGroup' : [], //deprecated in qti 2.1
                    current : 0
                }
            }
        },
        render : function(){

            var ctx = this;

            // max size of a text box to define target max size
            // create empty element in order to droppe in any other element of the item
            $('#' + ctx.id + " .qti_choice_list li").wrapInner("<div></div>");

            // calculate max size of a word in the cloud
            // give a size to the words cloud to avoid graphical "jump" when items are dropped
            var listHeight = parseInt($('#' + ctx.id + " .qti_associate_interaction_container").height());
            var liMaxHeight = 0;
            var maxBoxSize = 0;

            var calculateSizes = function($choice){
                if($choice.width() > maxBoxSize){
                    maxBoxSize = $choice.width();
                }
                var liHeight = parseInt($choice.height());
                if(liHeight > liMaxHeight){
                    liMaxHeight = liHeight;
                }
                if(liHeight > listHeight){
                    listHeight = liHeight + 10;
                }
            }

            var updateSizes = function(){
                $('#' + ctx.id + " .qti_associate_interaction_container .qti_choice_list").height(listHeight + 10);

                // set the size of the drop box to the max size of the cloud words 
                $('#' + ctx.id + " .qti_association_pair li").width(maxBoxSize + 4);

                // size the whole pair box to center it
                var pairBoxWidth = 0;

                $('#' + ctx.id + " .qti_association_pair:first li").each(function(){
                    pairBoxWidth += $(this).width();
                });
                $('#' + ctx.id + " .qti_pair_container").css({position : "relative", width : ((pairBoxWidth + 20) * 2) + 30, margin : "0 auto 0 auto", top : "10px"});
                $('#' + ctx.id + " .qti_association_pair").width(pairBoxWidth + 90);
                $('#' + ctx.id + " .qti_association_pair li").height(liMaxHeight);

                //reset the .qti_association_pair:
                $('#' + ctx.id + " .qti_link_associate").remove();
                $.each($('#' + ctx.id + " .qti_association_pair"), function(index, elt){
                    $(elt).after("<div class='qti_link_associate'></div>");
                    $('#' + ctx.id + " .qti_link_associate:last").css("top", $(this).position().top + 25);
                    $('#' + ctx.id + " .qti_link_associate:last").css("left", maxBoxSize + 33);
                });

                $('#' + ctx.id).height(($('#' + ctx.id + " .qti_association_pair:last").offset().top - $('#' + ctx.id).offset().top) + liMaxHeight);
            }

            // create the pair of box specified in the maxAssociations attribute	
            var pairSize = (ctx.options["maxAssociations"] > 0) ? ctx.options["maxAssociations"] : parseInt($('#' + ctx.id + " .qti_choice_list li").length / 2);
            var pairContainer = $("<div class='qti_pair_container'></div>");
            for(var a = 1; a <= pairSize; a++){
                var currentPairName = ctx.id + "_" + a;
                pairContainer.append("<ul class='qti_association_pair' id='" + currentPairName + "'><li id='" + currentPairName + "_A" + "'></li><li id='" + currentPairName + "_B" + "'></li></ul>");
            }
            $('#' + ctx.id + " .qti_associate_interaction_container").after(pairContainer);

            $('#' + ctx.id + " ul.qti_choice_list li > div").each(function(){
                var $choice = $(this);
                $(function(){
                    calculateSizes($choice);
                    updateSizes();
                });
                $choice.find('img').load(function(){
                    calculateSizes($choice);
                    updateSizes();
                });
            });

            //drag element from words cloud
            $('#' + ctx.id + " .qti_associate_interaction_container .qti_choice_list li > div").draggable({
                drag : function(event, ui){
                    // label go on top of the others elements
                    $(ui.helper).css("z-index", "999");
                },
                containment : '#' + ctx.id,
                cursor : "move",
                revert : true
            });

            // pair box are droppable
            $('#' + ctx.id + " .qti_association_pair li").droppable({
                drop : function(event, ui){

                    var draggedId = $(ui.draggable).parents().attr('id');

                    //prevent of re-filling the gap and dragging between the gaps
                    if($(this).find("#pair_" + draggedId).length > 0 || /^pair_/.test($(ui.draggable).attr('id'))){
                        return false;
                    }

                    var _matchGroup = ctx.options["matchMaxes"][draggedId]["matchGroup"];

                    //Check the matchGroup of the dropped item or the opposite in the pair 
                    if(/A$/.test($(this).attr('id'))){
                        var opposite = $('#' + $(this).attr('id').replace(/_A$/, "_B")).find('.filled_pair:first');
                    }
                    else{
                        var opposite = $('#' + $(this).attr('id').replace(/_B$/, "_A")).find('.filled_pair:first');
                    }
                    if(opposite.length > 0){
                        var oppositeId = opposite.attr('id').replace('pair_', '');
                        if(_matchGroup.length > 0){
                            if($.inArray(oppositeId, _matchGroup) < 0){
                                $(this).effect("highlight", {color : '#B02222'}, 1000);
                                return false;
                            }
                        }

                        var _oppositeMatchGroup = ctx.options["matchMaxes"][oppositeId]["matchGroup"];
                        if(_oppositeMatchGroup.length > 0){
                            if($.inArray(draggedId, _oppositeMatchGroup) < 0){
                                $(this).effect("highlight", {color : '#B02222'}, 1000);
                                return false;
                            }
                        }
                    }

                    //remove the old element
                    if($(this).html() != ''){
                        $('.filled_pair', $(this)).each(function(){
                            ctx.removeFilledPair($(this));
                        });
                    }
                    $(ui.helper).css({top : "0", left : "0"});

                    //fill the gap
                    ctx.fillPair($(this), $(ui.draggable));
                },
                hoverClass : 'active'
            });

        },
        setResponse : function(values){
            var ctx = this;
            var index = 1;
            if(typeof(values) === 'object'){
                for(var i in values){
                    var pair = values[i];
                    if(pair.length === 2){
                        var valA = pair[0];
                        if(typeof(valA) === 'string' && valA != ''){
                            if($('#' + ctx.id + " li#" + valA).length === 1){
                                this.fillPair($('#' + ctx.id + "_" + index + "_A"), $('#' + ctx.id + " .qti_associate_interaction_container .qti_choice_list li#" + valA + " > div"));
                            }
                        }
                        var valB = pair[1];
                        if(typeof(valB) === 'string' && valB != ''){
                            if($('#' + ctx.id + " li#" + valB).length === 1){
                                this.fillPair($('#' + ctx.id + "_" + index + "_B"), $('#' + ctx.id + " .qti_associate_interaction_container .qti_choice_list li#" + valB + " > div"));
                            }
                        }
                        index++;
                    }
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.associate();
        },
        /**
         * Fill a pair gap by a cloud element
         * @param  {jQuery} jDropped
         * @param  {jQuery} jDragged
         */
        fillPair : function(jDropped, jDragged){

            var ctx = this;
            if(jDropped.length && jDragged.length){
                // add class to highlight current dropped item in pair boxes
                jDropped.addClass('ui-state-highlight');

                var draggedId = jDragged.parents().attr('id');

                // add new element inside the box that received the cloud element
                jDropped.html("<div id='pair_" + draggedId + "' class='filled_pair'>" + jDragged.html() + "</div>");

                // give a size to the dropped item to overlapp perfectly the pair box
                $('#' + ctx.id + " #pair_" + draggedId).width($('#' + ctx.id + " .qti_association_pair li").width());
                $('#' + ctx.id + " #pair_" + draggedId).height($('#' + ctx.id + " .qti_association_pair li").height());

                var _matchMax = Number(ctx.options["matchMaxes"][draggedId]["matchMax"]);
                var _current = Number(ctx.options["matchMaxes"][draggedId]["current"]);

                if(_current < _matchMax){
                    _current++;
                    ctx.options["matchMaxes"][draggedId]["current"] = _current;
                }
                if(_current >= _matchMax && _matchMax > 0){
                    jDragged.hide();
                }

                // give this new element the ability to be dragged
                $('#' + ctx.id + " #pair_" + draggedId).draggable({
                    drag : function(event, ui){
                        // element is on top of the other when it's dragged
                        $(this).css("z-index", "999");
                    },
                    stop : function(event, ui){
                        ctx.removeFilledPair($(this));
                        return true;
                    },
                    containment : '#' + ctx.id,
                    cursor : "move"
                });
            }

        },
        /**
         * remove an element from the filled gap
         * @param {jQuery} jElement
         */
        removeFilledPair : function(jElement){
            var ctx = this;
            var filledId = jElement.attr("id").replace('pair_', '');
            var _matchMax = Number(ctx.options["matchMaxes"][filledId]["matchMax"]);
            var _current = Number(ctx.options["matchMaxes"][filledId]["current"]);

            if(_current > 0){
                ctx.options["matchMaxes"][filledId]["current"] = _current - 1;
            }
            jElement.parents('li').removeClass('ui-state-highlight');
            jElement.remove();
            if(_current >= _matchMax){
                $("#" + filledId + " div").show();
            }
        }
    });
    
    return AssociateInteraction;
});
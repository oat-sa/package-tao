define(['taoQtiItem/qtiDefaultRenderer/widgets/GraphicInteraction', 'taoQtiItem/qtiDefaultRenderer/widgets/Widget', 'raphael-collision', 'jqueryui'], function(GraphicInteraction, Widget, raphaelcollision){

    var GraphicGapMatchInteraction = GraphicInteraction.extend({
        init : function(interaction, context){
            this._super(interaction, context);

            //add match max information:
            var choices = interaction.getChoices();
            for(var i in choices){
                this.options.graphicChoices[choices[i].id()].matchMax = choices[i].attr('matchMax') || 0;
            }

            var gapImgs = interaction.getGapImgs();
            for(var i in gapImgs){
                this.options.graphicChoices[gapImgs[i].id()] = {
                    'matchMax' : gapImgs[i].attr('matchMax') || 0,
                    'current' : 0
                };
            }
        },
        render : function(){

            var ctx = this;
            ctx.shapes = [];

            $('#' + ctx.id + ' ul.choice_list').hide();

            $('#' + ctx.id + ' .qti_graphic_gap_match_spotlist li').wrapInner('<div></div>');

            //add breaker
            $('#' + ctx.id + " .qti_graphic_gap_match_spotlist li:last").after("<li></li>");
            $('#' + ctx.id + " .qti_graphic_gap_match_spotlist li:last").css('clear', 'both');

            //adapt the spot container size
            var containerHeight = 0;
            $('#' + ctx.id + ' .qti_graphic_gap_match_spotlist li').each(function(){
                var height = parseInt($(this).height());
                if(height > containerHeight){
                    containerHeight = height;
                }
            });

            var containerWidth = 5;
            $('#' + ctx.id + ' .qti_graphic_gap_match_spotlist li').each(function(){
                containerWidth += parseInt($(this).width()) + 4;
            });
            $('#' + ctx.id + ' .qti_graphic_gap_match_spotlist')
                .width(containerWidth)
                .height(containerHeight);

            //drag element from the spot container
            $('#' + ctx.id + " .qti_graphic_gap_match_spotlist li > div").draggable({
                drag : function(event, ui){
                    // label go on top of the others elements
                    $(ui.helper).css("z-index", "888");
                },
                containment : '#' + ctx.id,
                revert : true,
                cursor : 'move'
            });

            var imageHeight = parseInt(ctx.options.imageHeight);
            var imageWidth = parseInt(ctx.options.imageWidth);

            // offset position
            var itemHeight = parseInt($('#' + ctx.id).height());
            $('#' + ctx.id).css("height", itemHeight + imageHeight);

            //if the width and height are not given we preload the images to calculate them
            if(isNaN(imageHeight) || isNaN(imageWidth)){

                $('#' + ctx.id).append("<div class='img-loader' style='visibility:hidden;'></div>");
                var $loadedImg = $("<img src='" + ctx.options.imagePath + "' />");
                $loadedImg.load(function(){
                    var imageWidth = parseInt($(this).width());
                    var imageHeight = parseInt($(this).height());
                    $('#' + ctx.id + ' .img-loader').remove();

                    //we create the area with the calculated size 
                    ctx.createArea(imageWidth, imageHeight);
                });
                $('#' + ctx.id + ' .img-loader').append($loadedImg);

            }else{	//we use thos providen
                ctx.createArea(imageWidth, imageHeight);
            }

        },
        setResponse : function(values){
            if(typeof(values) === 'object'){
                for(var i in values){
                    var value = values[i];
                    if(value['0'] && value['1']){
                        var hotspot = value['0'];
                        var gapImg = value['1'];
                        this.fillGap(hotspot, gapImg);
                    }
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.graphic_gap_match();
        },
        /**
         * Create the area with the image in background
         * @param {int} imageWidth
         * @param {int} imageHeight
         */
        createArea : function(imageWidth, imageHeight){

            var ctx = this;

            // load image in rapheal area
            $('#' + ctx.id + ' .qti_graphic_gap_match_spotlist').before("<div class='svg-container'></div>");
            var paper = Raphael($('#' + ctx.id + ' .svg-container')[0], imageWidth, imageHeight);
            paper.image(ctx.options.imagePath, 0, 0, imageWidth, imageHeight);
            ctx.paper = paper;

            var collisables = [];
            for(var choiceId in ctx.options.graphicChoices){

                var currentHotSpotShape = ctx.options.graphicChoices[choiceId].shape;
                var currentHotSpotCoords = ctx.options.graphicChoices[choiceId].coords;
                if(currentHotSpotShape && currentHotSpotCoords){
                    //a hotspot:
                    var currentShape = this.buildShape(currentHotSpotShape, currentHotSpotCoords.split(','));
                    currentShape.toFront();
                    currentShape.id = choiceId;
                    Widget.setDeactivatedShapeAttr(currentShape);

                    ctx.shapes[choiceId] = currentShape;
                    collisables.push(currentShape);
                }

            }

            var offset = $('#' + ctx.id + ' .svg-container').offset();

            var collisionContext = {
                raphShape : paper,
                collisables : collisables,
                offsetLeft : offset.left,
                offsetTop : offset.top
            };

            //the all image is droppable
            $('#' + ctx.id + ' .svg-container').droppable({
                accept : '#' + ctx.id + " .qti_graphic_gap_match_spotlist li > div",
                drop : function(event, ui){
                    //detect if the mouse is inside a shape
                    var result = ctx.detectMouseCollision(event, collisionContext);
                    if(result.length > 0){
                        var gapImgId = $(ui.draggable).parent().attr("id");
                        return ctx.fillGap(result[0][2].id, gapImgId);
                    }
                }
            });

        },
        /**
         * Detect if the pointer is inside a shape of the raphShape SVG Element
         * 
         * @param {Event} event
         * 
         * @param {Object} 	params
         * @param {Raphael} [params.raphShape]
         * @param {Array} 	[params.collisables]
         * @param {Float} 	[params.offsetLeft = 0]
         * @param {Float} 	[params.offsetTop = 0]
         * 
         * @return {Boolean}
         */
        detectMouseCollision : function(event, params){
            return raphaelcollision(
                params.raphShape,
                params.collisables,
                event.pageX - ((params.offsetLeft) ? params.offsetLeft : 0),
                event.pageY - ((params.offsetTop) ? params.offsetTop : 0)
                );
        },
        getFilledId : function(hotspotId, gapImgId){
            return 'gap_' + hotspotId + '_' + gapImgId;
        },
        /**
         * Fill a gap with an element of the word's cloud
         * @param {jQuery} jDropped
         * @param {jQuery} jDragged
         * @param {String} hotspotId
         * @param {Raphael} raphShape
         */
        fillGap : function(hotspotId, gapImgId){

            var ctx = this;
            var raphShape = ctx.shapes[hotspotId];
            var $jDropped = $('#' + ctx.id + ' .svg-container');
            var $jDragged = $('#' + gapImgId + ' div.ui-draggable');
            var filledId = ctx.getFilledId(hotspotId, gapImgId);

            if(!raphShape){
                throw 'No shape found for the hotspot: ' + hotspotId;
            }

            if($('#' + ctx.id + ' #' + filledId).length > 0){
                return false;
            }

            var _hotspotMatchMax = parseInt(ctx.options.graphicChoices[hotspotId].matchMax);
            var _hotspotCurrent = parseInt(ctx.options.graphicChoices[hotspotId].current);
            _hotspotCurrent = isNaN(_hotspotCurrent) ? 0 : _hotspotCurrent;
            if(_hotspotMatchMax == 0 || _hotspotCurrent < _hotspotMatchMax){
                //ok, allow dropping
                ctx.options.graphicChoices[hotspotId].current = parseInt(_hotspotCurrent + 1);
            }else{
                Widget.animateForbiddenShape(raphShape);
                return false
            }

            //ok, set response : 
            Widget.animateAllowedShape(raphShape);
            var responseId = ctx.resultCollector.append({'hotspot' : hotspotId, 'gapImg' : gapImgId});

            //check the matchMax of the element
            var _gapImgMatchMax = parseInt(ctx.options.graphicChoices[gapImgId].matchMax);
            var _gapImgCurrent = parseInt(ctx.options.graphicChoices[gapImgId].current);
            _gapImgCurrent = isNaN(_gapImgCurrent) ? 0 : _gapImgCurrent;
            if(_gapImgMatchMax == 0 || _gapImgCurrent < _gapImgMatchMax){
                _gapImgCurrent++;
                ctx.options["graphicChoices"][gapImgId].current = _gapImgCurrent;
                if(_gapImgMatchMax > 0 && _gapImgCurrent >= _gapImgMatchMax){
                    $jDragged.hide();
                }
            }

            // add the new element inside the box that received the cloud element
            $jDropped.append("<div id='" + filledId + "' class='filled_gap'>" + $jDragged.html() + "</span>");
            var $image = $jDragged.find('img');
            var gapCenter = Widget.getShapeCenter(raphShape);
            $('#' + ctx.id + ' #' + filledId).css({
                'top' : gapCenter.y - $image.attr('height') / 2,
                'left' : gapCenter.x - $image.attr('width') / 2
            });

            //enable to drop it back to remove it from the gap
            $('#' + ctx.id + ' #' + filledId).draggable({
                drag : function(event, ui){
                    // label go on top of the others elements
                    $(ui.helper).css("z-index", "999");
                },
                stop : function(){
                    ctx.resultCollector.remove(responseId);
                    ctx.removeFilledGap(hotspotId, gapImgId);
                },
                revert : false,
                containment : '#' + ctx.id,
                cursor : "move"
            });
        },
        /**
         * remove an element from the filled gap
         * @param {jQuery} jElement
         */
        removeFilledGap : function(hotspotId, gapImgId){

            var ctx = this;

            //update gapImg 
            if(gapImgId.length){
                var _matchMax = Number(ctx.options.graphicChoices[gapImgId].matchMax);
                var _current = Number(ctx.options.graphicChoices[gapImgId].current);
                if(_current > 0)
                    _current--;
                ctx.options.graphicChoices[gapImgId].current = _current;
                if(_current < _matchMax){
                    $('#' + ctx.id + ' #' + gapImgId + " div").show();
                }
            }
            $('#' + ctx.getFilledId(hotspotId, gapImgId)).remove();

            //update hotspot's matchMax'
            if(hotspotId.length){
                var _matchMax = Number(ctx.options.graphicChoices[hotspotId].matchMax);
                var _current = Number(ctx.options.graphicChoices[hotspotId].current);
                if(_current > 0)
                    _current--;
                ctx.options.graphicChoices[hotspotId].current = _current;
            }
        }
    });
    
    return GraphicGapMatchInteraction;
});
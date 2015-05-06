define(['taoQtiItem/qtiDefaultRenderer/widgets/GraphicInteraction', 'taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(GraphicInteraction, Widget){

    var GraphicAssociateInteraction = GraphicInteraction.extend({
        render : function(){

            var ctx = this;
            ctx.countChoices = ctx.options.maxAssociations;
            ctx.pointsPair = [];

            var imageHeight = parseInt(ctx.options.imageHeight);
            var imageWidth = parseInt(ctx.options.imageWidth);

            // offset position
            $('#' + ctx.id).append('<div class="link_counter">0</div>').append('<div class="sub_counter"></div>');

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
                //a simple pair:
                if(typeof(values[0]) === 'string' && typeof(values[1]) === 'string'){
                    this.associate(values[0], values[1]);
                }
                //a collection of pairs:
                for(var index in values){
                    var value = values[index];
                    if(typeof(value) === 'object'){
                        if(typeof(value[0]) === 'string' && typeof(value[1]) === 'string'){
                            this.associate(value[0], value[1]);
                        }
                    }
                }

            }
        },
        getResponse : function(){
            return this.resultCollector.graphic_associate();
        },
        /**
         * Create the area with the image in background
         * @param {int} imageWidth
         * @param {int} imageHeight
         */
        createArea : function(imageWidth, imageHeight){

            var ctx = this;

            // load image in rapheal area
            $('#' + ctx.id).prepend("<div class='svg-container'></div>");
            ctx.$raphaelCanvas = $('#' + ctx.id + ' .svg-container');
            var paper = Raphael(ctx.$raphaelCanvas[0], imageWidth, imageHeight);
            paper.image(ctx.options.imagePath, 0, 0, imageWidth, imageHeight);
            ctx.paper = paper;
            ctx.shapes = {};
            ctx.line_ref_obj = {};

            // display max link or inifinite
            if(ctx.options.maxAssociations > 0){
                $('#' + ctx.id + " .link_counter").text(ctx.options.maxAssociations);
            }else{
                $('#' + ctx.id + " .link_counter").html("<span class='infiniteSize'>∞</span>");
            }

            // inti red cross delete button
            ctx.$deleteButton = $('<span id="qti-graphic-associate-delete-link"></span>').appendTo('#' + ctx.id);
            ctx.$deleteButton.hide();

            // create hotspot
            for(var choiceId in ctx.options.graphicChoices){

                var currentHotSpotShape = ctx.options.graphicChoices[choiceId].shape;
                var currentHotSpotCoords = ctx.options.graphicChoices[choiceId].coords.split(",");
                var currentHotSpotX, currentHotSpotY, currentHotSpotSize, currentHotSpotTopX, currentHotSpotTopY;
                var currentHotSpotBottomX, currentHotSpotBottomY, currentHotSpotHradius, currentHotSpotVradius;
                var pointerSize = 6, pointer, polyCoords, currentShape, shapeWidth, shapeHeight;

                switch(currentHotSpotShape){
                    case "circle":
                        currentHotSpotX = Number(currentHotSpotCoords[0]);
                        currentHotSpotY = Number(currentHotSpotCoords[1]);
                        currentHotSpotSize = currentHotSpotCoords[2];
                        currentShape = paper[currentHotSpotShape](currentHotSpotX, currentHotSpotY, currentHotSpotSize);
                        if(ctx.graphicDebug)
                            currentShape.attr("stroke-width", "3px");
                        pointer = paper.circle(currentHotSpotX, currentHotSpotY, pointerSize);
                        break;
                    case "rect":
                        currentHotSpotTopX = Number(currentHotSpotCoords[0]);
                        currentHotSpotTopY = Number(currentHotSpotCoords[1]);
                        currentHotSpotBottomX = currentHotSpotCoords[2] - currentHotSpotTopX;
                        currentHotSpotBottomY = currentHotSpotCoords[3] - currentHotSpotTopY;
                        currentShape = paper[currentHotSpotShape](currentHotSpotTopX, currentHotSpotTopY, currentHotSpotBottomX, currentHotSpotBottomY);
                        if(ctx.graphicDebug)
                            currentShape.attr("stroke-width", "3px");
                        shapeWidth = currentShape.getBBox().width;
                        shapeHeight = currentShape.getBBox().height;
                        pointer = paper.circle(currentHotSpotTopX + (shapeWidth / 2), currentHotSpotTopY + (shapeHeight / 2), pointerSize);
                        break;
                    case "ellipse":
                        currentHotSpotX = Number(currentHotSpotCoords[0]);
                        currentHotSpotY = Number(currentHotSpotCoords[1]);
                        currentHotSpotHradius = currentHotSpotCoords[2];
                        currentHotSpotVradius = currentHotSpotCoords[3];
                        currentShape = paper[currentHotSpotShape](currentHotSpotX, currentHotSpotY, currentHotSpotHradius, currentHotSpotVradius);
                        if(ctx.graphicDebug)
                            currentShape.attr("stroke-width", "3px");
                        pointer = paper.circle(currentHotSpotX, currentHotSpotY, pointerSize);
                        break;
                    case "poly":
                        polyCoords = Widget.polyCoordonates(ctx.options.graphicChoices[choiceId]["coords"]);
                        currentShape = paper.path(polyCoords);

                        if(ctx.graphicDebug)
                            currentShape.attr("stroke-width", "3px");

                        var polyCenter = Widget.getShapeCenter(currentShape);
                        pointer = paper.circle(polyCenter.x, polyCenter.y, pointerSize);
                        break;
                }

                if(!currentShape){
                    return;
                }

                var maxSubLink = ctx.options.graphicChoices[choiceId].matchMax || 0;

                ctx.shapes[choiceId] = {
                    pointer : pointer,
                    shape : currentShape,
                    pointerState : "hidden",
                    linkRelation : {},
                    maxSubLinkLength : maxSubLink
                };

                pointer.attr("fill", "black");
                pointer.attr("opacity", "0");
                currentShape.toFront();
                Widget.setDeactivatedShapeAttr(currentShape);

                // add a reference to newly created object
                ctx.options[currentShape.node] = choiceId;
                $(currentShape.node).bind("mousedown", {
                    'choiceId' : choiceId
                }, function(e){
                    ctx.select(e.data.choiceId);
                });

            }

        },
        select : function(choiceId){

            var ctx = this;

            for(var i in ctx.line_ref_obj){
                ctx.line_ref_obj[i].attr("stroke", "black").attr('stroke-width', '4');
            }

            ctx.$deleteButton.off('mousedown');

            var pointer = ctx.shapes[choiceId].pointer;
            ctx.$deleteButton.hide();
            pointer.attr("fill", "red");
            pointer.attr("stroke", "white");
            if(ctx.pointsPair.length < 1){
                ctx.pointsPair.push(choiceId);
                var maxSubLinkLength = ctx.shapes[choiceId].maxSubLinkLength;
                if(maxSubLinkLength < 1 || isNaN(maxSubLinkLength)){
                    $('#' + ctx.id + " .sub_counter").html("<span class='infiniteSize'>&#8734;</span>");//infinite symbol
                }else{
                    var currentLinkLength = ctx.displayedSubLink(ctx.shapes[choiceId].linkRelation);
                    var maxLinkAvalaible = maxSubLinkLength - currentLinkLength;
                    $('#' + ctx.id + " .sub_counter").text(maxLinkAvalaible);
                }
            }else{
                ctx.pointsPair.push(choiceId);

                // store bilateral relation
                var startId = ctx.pointsPair[0];
                var endId = ctx.pointsPair[1];

                //define begin and end point
                var startPointer = ctx.shapes[ctx.pointsPair[0]].pointer;
                var endPointer = ctx.shapes[ctx.pointsPair[1]].pointer;
                var relation = ctx.pointsPair[0] + ' ' + ctx.pointsPair[1];
                var targetRelation = ctx.pointsPair[1] + ' ' + ctx.pointsPair[0];

                // avoid double click on same pointer
                if(startPointer == endPointer){
                    startPointer.attr("fill", "black");
                    startPointer.attr("stroke", "black");
                    endPointer.attr("fill", "black");
                    endPointer.attr("stroke", "black");
                    ctx.hideSinglePoint(ctx.shapes, ctx.pointsPair[0], ctx.pointsPair[1], startPointer, endPointer);
                    ctx.emptyArray(ctx.pointsPair);
                    $('#' + ctx.id + " .sub_counter").text("");
                    startPointer.attr("opacity", "0");
                    return;
                }

                // block if maxAssociations are reached
                if(ctx.countChoices == 0 && ctx.options.maxAssociations != 0){
                    startPointer.attr("fill", "black");
                    startPointer.attr("stroke", "black");
                    endPointer.attr("fill", "black");
                    endPointer.attr("stroke", "black");
                    ctx.hideSinglePoint(ctx.shapes, ctx.pointsPair[0], ctx.pointsPair[1], startPointer, endPointer);
                    ctx.emptyArray(ctx.pointsPair);
                    return;
                }
                // verify maxSubLink
                var subLinkLength = ctx.displayedSubLink(ctx.shapes[ctx.pointsPair[0]].linkRelation);
                var targetSubLinkLength = ctx.displayedSubLink(ctx.shapes[ctx.pointsPair[1]].linkRelation);

                // block max sub relation
                // case infinite
                if(ctx.shapes[ctx.pointsPair[0]].maxSubLinkLength == 0){
                    if((targetSubLinkLength + 1) > ctx.shapes[ctx.pointsPair[1]].maxSubLinkLength && ctx.shapes[ctx.pointsPair[1]].maxSubLinkLength != 0){
                        startPointer.attr("fill", "black");
                        startPointer.attr("stroke", "black");
                        endPointer.attr("fill", "black");
                        endPointer.attr("stroke", "black");
                        ctx.hideSinglePoint(ctx.shapes, ctx.pointsPair[0], ctx.pointsPair[1], startPointer, endPointer);
                        ctx.emptyArray(ctx.pointsPair);
                        return;
                    }

                }else
                // case finite sublink length
                if(subLinkLength >= ctx.shapes[ctx.pointsPair[0]].maxSubLinkLength || (targetSubLinkLength + 1) > ctx.shapes[ctx.pointsPair[1]].maxSubLinkLength){
                    if(ctx.shapes[ctx.pointsPair[1]].maxSubLinkLength != 0){
                        // empty current pair
                        startPointer.attr("fill", "black");
                        startPointer.attr("stroke", "black");
                        endPointer.attr("fill", "black");
                        endPointer.attr("stroke", "black");
                        ctx.hideSinglePoint(ctx.shapes, ctx.pointsPair[0], ctx.pointsPair[1], startPointer, endPointer);
                        ctx.emptyArray(ctx.pointsPair);
                        return;
                    }
                }

                // bug peux plus creer de sublien (a tester)
                if(ctx.shapes[ctx.pointsPair[0]].linkRelation[relation] != undefined){
                    ctx.emptyArray(ctx.pointsPair);
                    return;
                }

                var startPointX = startPointer.getBBox().x + startPointer.getBBox().width / 2;
                var startPointY = startPointer.getBBox().y + startPointer.getBBox().width / 2;
                var endPointX = endPointer.getBBox().x + endPointer.getBBox().width / 2;
                var endPointY = endPointer.getBBox().y + endPointer.getBBox().width / 2;
                var drawingPath = "M" + startPointX + " " + startPointY + "L" + endPointX + " " + endPointY;
                // trace line between dots
                var line = ctx.paper.path(drawingPath);
                line.attr('stroke-width', '3');
                line.toFront();
                // black pointer
                startPointer.attr("fill", "black");
                startPointer.attr("stroke", "black");
                endPointer.attr("fill", "black");
                endPointer.attr("stroke", "black");

                ctx.shapes[startId].linkRelation[relation] = {};
                ctx.shapes[startId].linkRelation[relation].lineRef = line;
                ctx.shapes[startId].linkRelation[relation].endRef = endPointer;

                ctx.shapes[endId].linkRelation[targetRelation] = {};
                ctx.shapes[endId].linkRelation[targetRelation].lineRef = line;
                ctx.shapes[endId].linkRelation[targetRelation].endRef = startPointer;

                ctx.line_ref_obj[relation] = line;
                var pairs = [];
                for(var aPair in ctx.line_ref_obj){
                    pairs.push(aPair);
                }
                $('#' + ctx.id).data('pairs', pairs);//@todo: ugly, please replace it with resultCollector asap

                ctx.emptyArray(ctx.pointsPair);

                var pairOfPoints = ctx.pointsPair;

                // click on line
                $(line.node).on("mousedown", {
                    zeline : line,
                    centerX : (startPointX + endPointX) / 2,
                    centerY : (startPointY + endPointY) / 2,
                    zerelation : relation
                }, function(e){

                    var localRelation = e.data.zerelation;
                    var localLine = e.data.zeline;

                    // color all line in black
                    for(var i in ctx.line_ref_obj){
                        ctx.line_ref_obj[i].attr("stroke", "black");
                    }

                    //move the ALREADY created delete button to the right location:
                    ctx.$deleteButton.show();
                    ctx.$deleteButton.css("left", ctx.$raphaelCanvas.offset().left + e.data.centerX - 7);
                    ctx.$deleteButton.css("top", ctx.$raphaelCanvas.offset().top + e.data.centerY - 7);
//                ctx.$deleteButton.toFront();//z index?

                    //reset delete button event listener:
                    ctx.$deleteButton.off("mousedown").on("mousedown", function(e){

                        e.preventDefault();

                        delete ctx.shapes[startId].linkRelation[localRelation];
                        delete ctx.shapes[endId].linkRelation[targetRelation];

                        localLine.remove();
                        $(this).hide();

                        if(ctx.options.maxAssociations > 0 && ctx.countChoices < ctx.options.maxAssociations){
                            ctx.countChoices++;
                            $('#' + ctx.id + " .link_counter").text(ctx.countChoices);
                        }

                        ctx.hideSinglePoint(ctx.shapes, startId, endId, startPointer, endPointer);

                        delete ctx.line_ref_obj[localRelation];

                        ctx.emptyArray(pairOfPoints);

                        var pairs = [];

                        for(var aPair in ctx.line_ref_obj){
                            pairs.push(aPair);
                        }
                        $('#' + ctx.id).data('pairs', pairs);
                        $(this).unbind("mousedown");
                    });

                    var line = e.data.zeline;
                    line.attr("stroke", "red");
                });//end of event binding line.node.mousedown

                // link counter displayed (except if ctx.options.maxAssociations=0)
                if(ctx.options.maxAssociations > 0){
                    ctx.countChoices--;
                    $('#' + ctx.id + " .link_counter").text(ctx.countChoices);
                }
                ctx.shapes[choiceId].shape.toFront();
            }

            ctx.shapes[choiceId].pointer.attr("opacity", "1");
            ctx.shapes[choiceId].pointerState = "show";

            for(var t in ctx.shapes){
                ctx.shapes[t].shape.toFront();
            }
        },
        //create a pair by triggering the clicks 
        associate : function(hotspot1, hotspot2){
            this.select(hotspot1);
            this.select(hotspot2);
        },
        hideSinglePoint : function(obj, startId, endId, startPointer, endPointer){

            var rela = 0;
            // hide points alone
            for(var z in obj[startId].linkRelation){
                rela++;
            }

            var relb = 0;
            for(var e in obj[endId].linkRelation){
                relb++;
            }

            if(rela < 1){
                startPointer.attr("opacity", "0");
            }
            if(relb < 1){
                endPointer.attr("opacity", "0");
            }

        },
        emptyArray : function(arr){
            while(arr.length > 0){
                arr.pop();
            }
        },
        displayedSubLink : function(obj){
            var i = 0;
            for(var a in obj){
                i++;
            }
            return i;
        }
    });
    
    return GraphicAssociateInteraction;
});
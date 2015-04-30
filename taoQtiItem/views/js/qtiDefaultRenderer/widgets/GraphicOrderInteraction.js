define(['taoQtiItem/qtiDefaultRenderer/widgets/GraphicInteraction', 'taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(GraphicInteraction, Widget){

    var GraphicOrderInteraction = GraphicInteraction.extend({
        countChoices : 0,
        shapes : [],
        orderedElements : [],
        paper : null,
        pickedNumber : 0,
        init : function(interaction, context){
            this._super(interaction, context);
            if(!this.options.maxChoices){
                var choicesCount = 0;
                for(var i in this.options.graphicChoices){
                    if(this.options.graphicChoices.hasOwnProperty(i)){
                        choicesCount++;
                    }
                }
                this.options.maxChoices = choicesCount;
            }
        },
        render : function(){

            var ctx = this;
            this.countChoices = 0;
            this.shapes = {};

            var imageHeight = parseInt(ctx.options.imageHeight);
            var imageWidth = parseInt(ctx.options.imageWidth);

            // offset position
            $('#' + ctx.id + " .qti_graphic_order_spotlist li").css("display", "none");
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
                    ctx.createArea(imageWidth, imageHeight, itemHeight);
                });
                $('#' + ctx.id + ' .img-loader').append($loadedImg);

            }else{	//we use thos providen
                ctx.createArea(imageWidth, imageHeight, itemHeight);
            }

        },
        setResponse : function(values){

            if(typeof(values) === 'object'){
                var list = new Array(values.length);
                //we take the list element corresponding to the given ids 
                for(var index in values){
                    var value = values[index];
                    if(typeof(value) === 'string' && value != ''){
                        this.setOrder(parseInt(index) + 1, value);//warning: choice selectiong starts at index=1
                    }
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.graphic_order();
        },
        /**
         * Create the area with the image in background
         * @param {int} imageWidth
         * @param {int} imageHeight
         */
        createArea : function(imageWidth, imageHeight, itemHeight){

            this.orderedElements = {};

            var ctx = this;

            // load image in rapheal area
            $('#' + ctx.id).append("<div class='svg-container'></div>");
            var paper = Raphael($('#' + ctx.id + ' .svg-container')[0], imageWidth, imageHeight);
            paper.image(ctx.options.imagePath, 0, 0, imageWidth, imageHeight);
            this.paper = paper;

            // create pickup zone
            $('#' + ctx.id).append("<ul class='pickup_area'></ul>");
            for(var a = 1; a <= ctx.options.maxChoices; a++){
                $('#' + ctx.id + " .pickup_area").append('<li id="picked_num_' + a + '">' + a + "</li>");
            }
            // li behavior
            $('#' + ctx.id + " .pickup_area li").each(function(e){
                $(this).bind("click", function(e){
                    var nbr = parseInt($(this).attr('id').replace('picked_num_', ''));
                    ctx.pickNumber(nbr);
                });
            });

            // redim container and pickup area
            $('#' + ctx.id + " .pickup_area").width(imageWidth - 10 - 4);
            var pickup_area_height = parseInt($('#' + ctx.id + " .pickup_area").height());
            $('#' + ctx.id).css("height", itemHeight + imageHeight + pickup_area_height + 20);

            // resize container
            $('#' + ctx.id).height(parseInt($('#' + ctx.id).height()) + pickup_area_height);

            // create hotspot
            for(var choiceId in ctx.options.graphicChoices){
                var currentHotSpotShape = ctx.options.graphicChoices[choiceId]["shape"];
                var currentHotSpotCoords = ctx.options.graphicChoices[choiceId]["coords"].split(",");
                var currentShape = this.buildShape(currentHotSpotShape, currentHotSpotCoords);

                if(currentShape){

                    ctx.shapes[choiceId] = currentShape;

                    currentShape.toFront();
                    Widget.setDeactivatedShapeAttr(currentShape);

                    ctx.orderedElements[choiceId] = {state : "empty", order : 0, numberIn : null, numberOut : null, choiceItemRef : null};

                    // add a reference to newly created object
                    $(currentShape.node).bind("mousedown", {
                        'choiceId' : choiceId
                    }, function(e){
                        ctx.setOrder(ctx.pickedNumber, e.data.choiceId);
                    });
                }
            }

        },
        pickNumber : function(nbr){
            $('#' + this.id + ' .pickup_area li').removeClass("selected");
            $('#' + this.id + ' .pickup_area li#picked_num_' + nbr).addClass("selected");
            this.pickedNumber = nbr;
        },
        releaseNumber : function(nbr){
            $('#' + this.id + " .pickup_area li#picked_num_" + nbr).css("visibility", "hidden").removeClass("selected");
            this.pickedNumber = 0;
        },
        setOrder : function(number, choiceId){

            var ctx = this;
            if(!ctx.orderedElements[choiceId]){
                throw 'undefined choiceid in graphic order: ' + choiceId;
            }
            if(ctx.orderedElements[choiceId].state === "empty" && number === 0){
                return;
            }
            if(ctx.orderedElements[choiceId].state === "empty" && number > 0){

                var currentShape = this.shapes[choiceId];

                ctx.orderedElements[choiceId].state = "filled";
                ctx.orderedElements[choiceId].order = number;

                Widget.setActivatedShapeAttr();
                var coords = Widget.getShapeCenter(currentShape);
                var orderInfo = ctx.paper.text(coords.x, coords.y, number);
                var orderInfo1 = ctx.paper.text(coords.x, coords.y, number);

                ctx.orderedElements[choiceId].numberIn = orderInfo;
                ctx.orderedElements[choiceId].numberOut = orderInfo1;
                var $choiceItemRef = $('#' + ctx.id + " .pickup_area li#picked_num_" + number);
                ctx.orderedElements[choiceId].choiceItemRef = $choiceItemRef;

                orderInfo.attr("font-family", "verdana");
                orderInfo.attr("font-size", 16);
                orderInfo.attr("font-weight", "bold");
                orderInfo.attr("fill", "#009933");
                orderInfo.attr("stroke", "#009933");
                orderInfo.attr("stroke-width", "3px");

                orderInfo1.attr("font-family", "verdana");
                orderInfo1.attr("font-size", 16);
                orderInfo1.attr("font-weight", "bold");
                orderInfo1.attr("fill", "#ffffff");

                currentShape.toFront();

                //reset picked number
                ctx.releaseNumber(number);

            }else{

                Widget.setDeactivatedShapeAttr(currentShape, 400);
                ctx.orderedElements[choiceId].numberIn.remove();
                ctx.orderedElements[choiceId].numberOut.remove();
                ctx.orderedElements[choiceId].state = "empty";
                ctx.orderedElements[choiceId].choiceItemRef.css("visibility", "visible");

            }

            //after each setOrder, update the response:
            var response = {};
            for(var id in ctx.orderedElements){
                var orderedElt = ctx.orderedElements[id];
                if(orderedElt.state !== 'empty'){
                    response[parseInt(orderedElt.order) - 1] = id;
                }
            }
            ctx.resultCollector.set(response);
        }
    });
    
    return GraphicOrderInteraction;
});
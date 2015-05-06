define(['taoQtiItem/qtiDefaultRenderer/widgets/GraphicInteraction', 'taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(GraphicInteraction, Widget){

    var HotspotInteraction = GraphicInteraction.extend({
        countChoices : 0,
        shapes : [],
        paper : null,
        render : function(){

            var ctx = this;
            ctx.countChoices = 0;
            ctx.shapes = [];

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
                    ctx.createArea(imageWidth, imageHeight, itemHeight);
                });
                $('#' + ctx.id + ' .img-loader').append($loadedImg);

            }
            else{	//we use thos providen
                ctx.createArea(imageWidth, imageHeight, itemHeight);
            }

        },
        setResponse : function(values){
            if(typeof(values) === 'object'){
                for(var i in values){
                    this.select(values[i]);
                }
            }
            if(typeof(values) === 'string'){
                this.select(values);
            }
        },
        getResponse : function(){
            return this.resultCollector.hotspot();
        },
        /**
         * Create the area with the image in background
         * @param {int} imageWidth
         * @param {int} imageHeight
         */
        createArea : function(imageWidth, imageHeight, itemHeight){
            var ctx = this;
            // load image in rapheal area
            $('#' + ctx.id).append("<div class='svg-container'></div>");
            var paper = Raphael($('#' + ctx.id + ' .svg-container')[0], imageWidth, itemHeight + imageHeight);
            paper.image(ctx.options.imagePath, 0, 0, imageWidth, imageHeight);
            this.paper = paper;

            // create hotspot
            for(var choiceId in ctx.options.graphicChoices){

                var currentHotSpotShape = ctx.options.graphicChoices[choiceId]["shape"];
                var currentHotSpotCoords = ctx.options.graphicChoices[choiceId]["coords"].split(",");

                var currentShape = this.buildShape(currentHotSpotShape, currentHotSpotCoords);

                if(currentShape){
                    currentShape.toFront();
                    Widget.setDeactivatedShapeAttr(currentShape);
                    // add a reference to newly created object
                    ctx.shapes[choiceId] = currentShape;
                    $(currentShape.node).bind("mousedown", {
                        'choiceId' : choiceId
                    }, function(e){
                        ctx.select(e.data.choiceId);
                    });

                }
            }

        },
        select : function(choiceId){
            var responseIndex = this.resultCollector.isSet(choiceId)
            if(responseIndex >= 0){
                this.countChoices -= 1;
                Widget.setDeactivatedShapeAttr(this.shapes[choiceId]);
                this.resultCollector.remove(responseIndex);
            }else{
                if(this.countChoices >= this.options.maxChoices){
                    return;
                }
                this.countChoices += 1;
                Widget.setActivatedShapeAttr(this.shapes[choiceId]);
                this.paper.safari();
                this.resultCollector.append(choiceId);
            }
        }
    });
    
    return HotspotInteraction;
});
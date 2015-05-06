define(['taoQtiItem/qtiDefaultRenderer/widgets/GraphicInteraction'], function(GraphicInteraction){
    
    var SelectPointInteraction = GraphicInteraction.extend({
        countChoices : 0,
        render : function(){

            var ctx = this;

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

            $('#' + ctx.id + " .svg-container").bind("click", function(e){
                var relativeXmouse = e.pageX - 5;
                var relativeYmouse = e.pageY - 5;
                ctx.setPoint($(this), relativeXmouse, relativeYmouse);
            });
        },
        setResponse : function(values){
            var $containerArea = $('#' + this.id + " .svg-container");
            if(typeof(values) === 'object'){
                for(var i in values){
                    var value = values[i];
                    if(typeof(value) === 'object'){
                        if(value['0'] && value['1']){
                            this.setPoint($containerArea, $containerArea.offset().left + parseInt(value['0']), $containerArea.offset().top + parseInt(value['1']));
                        }
                    }
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.select_point();
        },
        /**
         * Create the area with the image in background
         * @param {int} imageWidth
         * @param {int} imageHeight
         */
        createArea : function(imageWidth, imageHeight, itemHeight){
            // load image in rapheal area
            $('#' + this.id).append("<div class='svg-container'></div>");
            var paper = Raphael($('#' + this.id + ' .svg-container')[0], imageWidth, itemHeight + imageHeight);
            paper.image(this.options.imagePath, 0, 0, imageWidth, imageHeight);
        },
        /**
         * place an removable image on the selected point
         * @param {jQuery} jContainer
         * @param {integer} x
         * @param {integer} y
         */
        setPoint : function($jContainer, x, y){

            var ctx = this;
            var maxChoices = parseInt(ctx.options.maxChoices);
            if(ctx.countChoices >= maxChoices && maxChoices !== 0){
                return;
            }
            ctx.countChoices++;

            var offset = $jContainer.offset();
            var point = {
                'x' : parseInt(x - offset.left),
                'y' : parseInt(y - offset.top)
            };
            var responseId = ctx.resultCollector.append(point);
            $jContainer.append('<span class="select_point_cross"/>')
                .find('span:last').css({position : "absolute", top : y + 'px', left : x + 'px', "cursor" : "pointer"}).on('click', function(e){
                ctx.countChoices--;
                $(this).remove();
                ctx.resultCollector.remove(responseId);
                return false;
            });
        }
    });
    
    return SelectPointInteraction;
});
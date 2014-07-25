define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget', 'raphael'], function(Widget){

    var GraphicInteraction = Widget.extend({
        init : function(interaction, context){
            this._super(interaction, context);
            var imgUrl = interaction.object.attr('data');
            if(!imgUrl.match(/^http/i)){
                //append the contextual base www:
                imgUrl = this.wwwPath + imgUrl;
            }
            this.options.imagePath = imgUrl;
            this.options.imageWidth = interaction.object.attr('width');
            this.options.imageHeight = interaction.object.attr('height');

            //init graphicChoices if needed
            this.options.graphicChoices = {}
            var choices = interaction.getChoices();
            for(var i in choices){
                this.options.graphicChoices[choices[i].id()] = {
                    'shape' : choices[i].attr('shape'),
                    'coords' : choices[i].attr('coords')
                }
            }
        },
        buildShape : function(shape, coords){

            var raphaelShape = null;

            if(this.paper){
                // create pointer to validate interaction
                // map QTI shape to Raphael shape
                // Depending the shape, options may vary
                switch(shape){
                    case "circle":
                        var x = Number(coords[0]);
                        var y = Number(coords[1]);
                        var size = coords[2];
                        raphaelShape = this.paper[shape](x, y, size);
                        if(this.graphicDebug)
                            raphaelShape.attr("stroke-width", "3px");
                        break;
                    case "rect":
                        var topX = Number(coords[0]);
                        var topY = Number(coords[1]);
                        var bottomX = coords[2] - topX;
                        var bottomY = coords[3] - topY;
                        raphaelShape = this.paper[shape](topX, topY, bottomX, bottomY);
                        if(this.graphicDebug)
                            raphaelShape.attr("stroke-width", "3px");
                        break;
                    case "ellipse":
                        var x = Number(coords[0]);
                        var y = Number(coords[1]);
                        var hRadius = coords[2];
                        var vRadius = coords[3];
                        raphaelShape = this.paper[shape](x, y, hRadius, vRadius);
                        if(this.graphicDebug)
                            raphaelShape.attr("stroke-width", "3px");
                        break;
                    case "poly":
                        var polyCoords = Widget.polyCoordonates(coords);
                        raphaelShape = this.paper["path"](polyCoords);
                        if(this.graphicDebug)
                            raphaelShape.attr("stroke-width", "3px");
                        break;
                }
            }else{
                throw 'cannot build shape if the raphael paper has not been initialized in this.paper';
            }

            return raphaelShape;
        }
    });

    return GraphicInteraction;
});
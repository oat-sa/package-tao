define(['class', 'taoQtiItem/qtiDefaultRenderer/widgets/ResultCollector'], function(Class, ResultCollector){
    
    var Widget = Class.extend({
        init : function(interaction, context){

            this.id = interaction.attr('identifier');
            this.options = interaction.getAttributes();

            if(context){
                this.wwwPath = context.wwwPath || '';
                this.graphicDebug = context.graphicDebug || false;
            }

            var respDecl = interaction.getResponseDeclaration();
            if(respDecl){
                this.options.responseBaseType = respDecl.attr('baseType');
            }
            this.resultCollector = new ResultCollector(interaction);
        },
        render : function(){
            throw 'method to be implemented by inherited widget';
        },
        setResponse : function(){
            throw 'method to be implemented by inherited widget';
        },
        getResponse : function(){
            throw 'method to be implemented by inherited widget';
        }
    });

    /**
     * Utilities
     */

    /**
     * Get the coordinates of the center of the polygon, relative to the parent canvas
     * @param raphaelPolygon 
     * @param $relativeCanvas 
     * @returns {Object}
     */
    Widget.getShapeCenter = function(raphaelShape){
        var box = raphaelShape.getBBox();
        return {
            x : box.x + (box.width / 2),
            y : box.y + (box.height / 2)
        }
    };

    /**
     * Get the coordinates of a poly shape reagrding it's path
     * @function
     * @param path 
     * @returns {Array}
     */
    Widget.polyCoordonates = function(path){

        var pathArray = [];
        if(typeof(path) === 'string'){
            pathArray = path.split(",");
        }else if(typeof(path) === 'array'){
            pathArray = path;
        }else if(typeof(path) === 'object'){
            for(var i in path){
                pathArray.push(path[i]);
            }
        }else{
            throw 'invalid argument type for path : ' + typeof(path);
        }

        var pathArrayLength = pathArray.length;
        // autoClose if needed
        if((pathArray[0] !== pathArray[pathArrayLength - 2]) && (pathArray[1] !== pathArray[pathArrayLength - 1])){
            pathArray.push(pathArray[0]);
            pathArray.push(pathArray[1]);
        }
        // move to first point
        pathArray[0] = "M" + pathArray[0];
        for(var a = 1; a < pathArrayLength; a++){
            if(Widget.isPair(a)){
                pathArray[a] = "L" + pathArray[a];
            }
        }
        return pathArray.join(" ");
    };

    /**
     * Check if number is pair or not
     * @function
     * @param number
     * @returns {Number}
     */
    Widget.isPair = function(number){
        return (!number % 2);
    };

    /**
     * Set the style for visible but deactivated raphael shape
     * @function
     * @param raphaelShape
     * @param animationDuration (in ms)
     */
    Widget.setDeactivatedShapeAttr = function(raphaelShape, animationDuration){
        if(raphaelShape && raphaelShape.animate){
            raphaelShape.animate({
                'fill' : '#E0DCDD',
                'fill-opacity' : .3,
                'stroke-opacity' : .8,
                'stroke' : '#ABA9AA'
            }, (animationDuration) ? animationDuration : 100);
        }
    };

    /**
     * Set the style for activated raphael shape
     * @function
     * @param raphaelShape
     */
    Widget.setActivatedShapeAttr = function(raphaelShape){
        if(raphaelShape && raphaelShape.animate){
            raphaelShape.animate({
                'fill' : 'green',
                'fill-opacity' : .2,
                'stroke-opacity' : .5,
                'stroke' : 'green'
            }, 100);
        }
    };

    /**
     * Set the style for activated raphael shape
     * @function
     * @param raphaelShape
     */
    Widget.setForbiddenShapeAttr = function(raphaelShape){
        if(raphaelShape && raphaelShape.attr){
            raphaelShape.animate({
                'fill' : 'red',
                'fill-opacity' : .2,
                'stroke-opacity' : .5,
                'stroke' : 'red'
            }, 100);
        }
    };

    /**
     * Animate the raphael shape
     * @function
     * @param raphaelShape
     */
    Widget.animateForbiddenShape = function(raphaelShape){
        Widget.setForbiddenShapeAttr(raphaelShape);
        setTimeout(function(){
            Widget.setDeactivatedShapeAttr(raphaelShape, 400);
        }, 400);
    };

    /**
     * Animate the raphael shape
     * @function
     * @param raphaelShape
     */
    Widget.animateAllowedShape = function(raphaelShape){
        Widget.setActivatedShapeAttr(raphaelShape);
        setTimeout(function(){
            Widget.setDeactivatedShapeAttr(raphaelShape, 400);
        }, 400);
    };

    return Widget;
});
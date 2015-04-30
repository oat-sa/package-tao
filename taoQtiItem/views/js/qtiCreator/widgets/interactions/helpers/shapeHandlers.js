/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 'raphael'
], function($, _, Raphael){

    /**
     * Creates handlers around a shape
     * @exports qtiCreator/widgets/interaction/helpers/shapeHandlers
     * @param {Raphael.Paper} paper - the raphael paper
     * @param {Raphael.Element} element - the shape
     * @returns {Array<Raphael.Element>} the handlers 
     */
    var shapeHandlers = function (paper, element){

        var getCoords = function getHandlingCoords(element){
            var coords;
            var scaleFactor = paper.w < paper.width ? 1 : paper.w / paper.width;
            var bbox        = element.getBBox();
            var size        = (bbox.width > 25 ? 6 : 4) * scaleFactor;
            var half        = size / 2;
            var halfWidth   = bbox.width / 2;
            var halfHeight  = bbox.height / 2;
            
            switch(element.type){
                case 'circle' :
                    coords = {
                        midTop      : [bbox.x + halfWidth - half, bbox.y - size, size, size],
                        midRight    : [bbox.x + bbox.width, bbox.y + halfHeight - half, size, size],
                        midBot      : [bbox.x + halfWidth - half, bbox.y + bbox.height, size, size],
                        midLeft     : [bbox.x - size, bbox.y + halfHeight - half, size, size]
                    };
                    break;
                case 'path' :
                    coords =  {};
                    _.forEach(Raphael.parsePathString(element.attr('path')), function(segment, index){
                        if(segment.length === 3){
                            coords['point' + index] = segment.slice(1).concat(half);
                        }
                    });
                    break;
                case 'rect' :
                case 'ellipse' :
                    coords = {
                        topLeft     : [bbox.x - size, bbox.y - size, size, size],
                        midTop      : [bbox.x + halfWidth - half, bbox.y - size, size, size],
                        topRight    : [bbox.x + bbox.width, bbox.y - size, size, size],
                        midRight    : [bbox.x + bbox.width, bbox.y + halfHeight - half, size, size],
                        botRight    : [bbox.x + bbox.width, bbox.y + bbox.height, size, size],
                        midBot      : [bbox.x + halfWidth - half, bbox.y + bbox.height, size, size],
                        botLeft     : [bbox.x - size, bbox.y + bbox.height, size, size],
                        midLeft     : [bbox.x - size, bbox.y + halfHeight - half, size, size]
                    };
                    break;
            }
            return coords;
        };

        return _.map(getCoords(element), function(handlerCoords, name){
            var handler, cursor, constraints, hshape;
            if(element.type === 'path'){
                cursor = 'move';
                hshape = 'circle'; 
            } else {
                hshape = 'rect'; 
                switch(name){
                    case 'topLeft' :
                        cursor = 'nw-resize';
                        constraints = { ix : true, iy : true };
                        break;
                    case 'midTop' :
                        cursor = 'ns-resize';
                        constraints = { iy : true };
                        break;
                    case 'topRight' :
                        cursor = 'ne-resize';
                        constraints = { x : true, iy : true };
                        break;
                    case 'midRight' :
                        cursor = 'ew-resize';
                        constraints = { x : true };
                        break;
                    case 'botRight' :
                        cursor = 'se-resize';
                        break;
                    case 'midBot' :
                        cursor = 'ns-resize';
                        constraints = { y : true };
                        break;
                    case 'botLeft' :
                        cursor = 'sw-resize';
                        constraints = { ix : true, y : true };
                        break;
                    case 'midLeft' :
                        cursor = 'ew-resize';
                        constraints = { ix : true };
                        break;
                }
            }
            return paper[hshape].apply(paper, handlerCoords)
                        .attr({
                            'fill' : '#eeeeee',
                            'cursor' : cursor
                        })
                        .data('constraints', constraints);
        });
    };

    return shapeHandlers;
});

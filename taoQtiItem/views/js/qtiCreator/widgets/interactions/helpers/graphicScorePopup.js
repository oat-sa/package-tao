/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 
    'lodash' 
], function($, _){


    /**
     * Creates a popup relative to shape in a paper
     * @param {Raphael.Element} shape - the relative shape
     * @param {jQueryElement} $container - the svg container
     * @returns {jQueryElement} the popup
     */
    return function createShapePopups(paper, shape, $container, isResponsive){
        var wfactor;
        var margin      = 10;
        var $shape      = $(shape.node);
        var $element    = $('<div class="mapping-editor arrow-left-top"></div>'); 
        var boxOffset   = $container.offset();
        var offset      = $shape.offset();
        var bbox        = shape.getBBox();
        var width       = bbox.width; 
           
        if(isResponsive){
            wfactor = paper.w / paper.width;
            width   = Math.round(width * (2 - wfactor));
        }
 
        //style and attach the form
        $element.css({
            'top'       : offset.top - boxOffset.top - margin,
            'left'      : offset.left - boxOffset.left + width + margin
        }).appendTo($container);

        return $element;
    };  
});

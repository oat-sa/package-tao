define([
    'IMSGlobal/jquery_2_1_1'
], function(
    $
    ){

    'use strict';

    /**
     * Reading the transform property will return a matrix
     *
     * @param element
     * @returns matrix
     */
    function getCssMatrix(element) {
        element = $(element);
        return element.css('transform')
            || element.css('-webkit-transform')
            || element.css('-moz-transform')
            || element.css('-ms-transform')
            || element.css('-o-transform');
    }

    /**
     * Transform the matrix to an object
     *
     * Note: this will only work if in the CSS are only translate and rotate!
     *
     * @param matrix
     * @returns {{}}
     */
    function matrixToTransformObj(matrix) {
        if(matrix === 'none') {
            matrix = 'matrix(0,0,0,0,0)';
        }

        var obj = {},
            values = matrix.match(/([-+]?[\d\.]+)/g);
        obj.rotate = (Math.round(Math.atan2(parseFloat(values[1]), parseFloat(values[0])) * (180/Math.PI)) || 0)
            .toString() + 'deg';
        obj.translate  = values[5] ? values[4] + 'px, ' + values[5] + 'px' : (values[4] ? values[4] + 'px' : '');
        return obj;
    }

    /**
     * Shortcut to get transformation directly from CSS
     *
     * @param element
     * @returns {{}}
     */
    function cssTransformObj(element) {
        return matrixToTransformObj(getCssMatrix(element));
    }

    /**
     * Convert transform object to CSS
     * @param obj
     * @returns {string}
     */
    function transformObjToCss(obj) {
        var type, css = '';
        for(type in obj){
            if(obj[type]) {
                css += type + '(' + obj[type] + ') ';
            }
        }
        return css.trim();
    }

    /**
     * Shortcut to apply a transform object to a single dom element
     * @param obj
     * @param element
     */
    function applyTransformObj(obj, element) {
        if(element instanceof jQuery) {
            element = element[0];
        }

        var translation = obj.translate.split(',');
        delete(obj.translate);
        if(translation.length < 2){
            translation[1] = 0;
        }
        translation[0] = translation[0].trim();
        translation[1] = translation[1].trim();

        element.style.left = translation[0].trim();
        element.style.top  = translation[1].trim();

        element.style.webkitTransform = element.style.msTransform = element.style.transform = transformObjToCss(obj);
    }

    /**
     * Get rotation center, either the center of the rectangle or a point defined by transform-origin
     *
     * @param rotatable
     * @returns {*}
     */
    function getRotationCenter(rotatable) {
        var $rotatable = $(rotatable),
            // compute origin based on CSS
            tOrigin = $rotatable.css('transform-origin')
                || $rotatable.css('-webkit-transform-origin')
                || $rotatable.css('-moz-transform-origin')
                || $rotatable.css('-ms-transform-origin')
                || $rotatable.css('-o-transform-origin'),
            defaultOrigin = {
                x: $rotatable.width() / 2,
                y: $rotatable.height() / 2
            },
            i,
            dim;


        if (!tOrigin || tOrigin === '50% 50%') {
            return defaultOrigin;
        }

        tOrigin = tOrigin.split(/\s+/);
        if (!tOrigin.length) {
            return defaultOrigin;
        }

        if (tOrigin.length === 1) {
            tOrigin[1] = tOrigin[0];
        }

        i = tOrigin.length;
        while (i--) {
            dim = i === 0 ? 'width' : 'height';
            switch (tOrigin[i]) {
                case 'left':
                    tOrigin[i] = 0;
                    break;
                case 'center':
                    tOrigin[i] = $rotatable[dim]() / 2;
                    break;
                case 'right':
                case 'bottom':
                    tOrigin[i] = $rotatable[dim]();
                    break;
                case 'top':
                    tOrigin[i] = 0;
                    break;
            }
            tOrigin[i] = parseFloat(tOrigin[i]);
        }
        return {
            x: tOrigin[0],
            y: tOrigin[1]
        };

    }


    return {
        getCssMatrix: getCssMatrix,
        matrixToTransformObj: matrixToTransformObj,
        transformObjToCss: transformObjToCss,
        cssTransformObj: cssTransformObj,
        applyTransformObj: applyTransformObj,
        getRotationCenter: getRotationCenter
    };

});






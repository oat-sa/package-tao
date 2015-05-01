define([
    'IMSGlobal/jquery_2_1_1',
    'OAT/interact',
    'OAT/sts/transform-helper'
], function(
    $,
    interact,
    transformHelper
    ){

    'use strict';


    /**
     * Start rotation, this will work on on mobile and desktop
     * Note: this will work on ONE rotatable only!
     *
     * @param rotatable single DOM element
     * @param handleSelector
     */
    function init (rotatable, handleSelector) {

        var handles,
            angle = 0,
            origin,
            fn = (!!interact.supportsTouch() ? 'gesturable' : 'draggable'),
            i;

        handles = handleSelector ? rotatable.querySelectorAll(handleSelector) : [rotatable];
        origin  = transformHelper.getRotationCenter(rotatable);
        i       = handles.length;


        while(i--) {
            interact(handles[i])[fn]({
                onmove: function (event) {
                    var handleAngle, offset, hypotenuse, transformObject,
                        currentAngle, R, S, RS, sides, absPosition, vectors,
                        vector, length;

                    handleAngle = event.target.className.indexOf('sts-handle-rotate-l') > -1
                        ? -90
                        : 90;

                    offset = $(rotatable).offset();


                    // This section calculates the absolute position of the centre of the shape.
                    // TODO: take account of the larger square
                    // origin x and y are here the sides of the triangle
                    hypotenuse = Math.sqrt((origin.x * origin.x) + (origin.y * origin.y));

                    // get currently applied angle
                    transformObject = transformHelper.cssTransformObj(rotatable);
                    currentAngle    = parseInt(transformObject.rotate, 10);

                    // angles
                    // R requires us to get the real rotation, not the applied rotation that is based
                    // on the rotation helpers
                    R = (currentAngle - handleAngle) * (Math.PI / 180);
                    S = Math.atan2(origin.x, origin.y) - (Math.PI / 2.0);
                    RS = R + S;

                    // new sides
                    sides = {
                        b: Math.abs(Math.sin(RS) * hypotenuse),
                        c: Math.abs(Math.cos(RS) * hypotenuse)
                    };

                    absPosition =  {
                        x: sides.b + offset.left,
                        y: sides.c + offset.top
                    };

                    // Using the absolute position, turn towards the mouse position
                    vectors = {
                        x: absPosition.x - event.clientX,
                        y: -(absPosition.y - event.clientY)
                    };

                    length = Math.sqrt((vectors.x * vectors.x) + (vectors.y * vectors.y));

                    for(vector in vectors) {
                        vectors[vector] /= length;
                    }

                    angle = Math.atan2(vectors.x, vectors.y) * (180 / Math.PI);

                    // The rotation helper's angle to the real rotation needs to be taken into account
                    angle += handleAngle;

                    rotatable.style.webkitTransform = rotatable.style.transform = 'rotate(' + (angle).toString() + 'deg)';

                }
            });
        }
    }

    return {
        init: init
    }

});

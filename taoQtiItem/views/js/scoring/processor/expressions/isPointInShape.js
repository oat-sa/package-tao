/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * Shared util to check wheter a point is in a shape (using QTI shape, coords and point format)
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
], function(_){
    'use strict';


    /**
     * Check wheter a point is in a shape
     *
     * @exports taoQtiItem/scoring/processor/expressions/isPointInShape
     *
     * @param {String} shape - the QTI shape (rect, circle, ellipse or poly)
     * @param {Array<Number>} point - [x,y] point
     * @param {Array<Number>} coords - the shape coordinates as per QTI standard
     * @returns {Boolean} true if the point is inside the shape.
     */
    function isPointInShape(shape, point, coords){

        var x = point[0];
        var y = point[1];

        var p2      = _.partialRight(Math.pow, 2);

        //to be called dynamically like isPointIn['rect']();
        var isPointIn = {
            rect : function isPointInRect(){
                var left    = coords[0];
                var top     = coords[1];
                var right   = coords[2];
                var bottom  = coords[3];

                return x >= left && x <= right && y >= top && y <= bottom;
            },

            circle : function isPointInCircle(){

                var centerx = coords[0];
                var centery = coords[1];
                var radius  = coords[2];

                return p2(x - centerx) + p2(y - centery) < p2(radius);
            },

            ellipse : function isPointInEllipse(){

                var centerx = coords[0];
                var centery = coords[1];
                var radiush = coords[2];
                var radiusv = coords[3];
                var distx   = x - centerx;
                var disty   = y - centery;

                return ( p2(distx) / p2(radiush) ) + ( p2(disty) / p2(radiusv) ) <= 1;
            },

            poly : function isPointInPoly(){
                var i, j, xi, yi, xj, yj;
                var inside = false;
                var intersect = false;

                //transform the coords in vertices
                var vertx = _.reduce(coords, function(acc, coord, index){
                    if (index % 2 === 0) {
                         acc.push([coord]);
                    } else {
                        acc[acc.length -1][1] = coord;
                    }
                    return acc;
                }, []);

                var vSize = vertx.length;

                /*
                 * ray-casting algorithm based on
                 * http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html
                 * and a js implentation from https://github.com/substack/point-in-polygon
                 */
                for (i = 0, j = vSize - 1; i < vSize; j = i++) {
                    xi = vertx[i][0];
                    yi = vertx[i][1];
                    xj = vertx[j][0];
                    yj = vertx[j][1];

                    intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                    if (intersect) {
                        inside = !inside;
                    }
                }
                return inside;
            },

            'default' : function(){
                return true;
            }
        };

        if(_.isFunction(isPointIn[shape])){
            return isPointIn[shape]();
        }

        return false;
    }

    return isPointInShape;
});


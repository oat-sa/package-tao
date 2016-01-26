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
 * Copyright (c) 2015 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * The mapResponsePoint expression processor.
 *
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10581
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/isPointInShape',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, isPointInShape, errorHandler){
    'use strict';

    /**
     * The MapResponsePoint Processor
     * @type {ExpressionProcesssor}
     * @exports taoQtiItem/scoring/processor/expressions/mapResponsePoint
     */
    var mapResponseProcessor = {

        /**
         * Process the expression
         * @returns {ProcessingValue} the value from the expression
         */
        process : function(){

            var self = this;
            var points,
                defaultValue,
                lowerBound,
                upperBound,
                filtered;
            var identifier = this.expression.attributes.identifier;
            var variable   = this.state[identifier];
            var result     = {
                cardinality : 'single',
                baseType    : 'float'
            };

            if(typeof variable === 'undefined' || variable === null){
                 return errorHandler.throw('scoring', new Error('No variable found with identifier ' + identifier ));
            }

            if(typeof variable.mapping === 'undefined' || variable.mapping.qtiClass !== 'areaMapping'){
                 return errorHandler.throw('scoring', new Error('The variable ' + identifier + ' has no areaMapping, how can I execute a mapResponsePoint on it?'));
            }

            if(variable.baseType !== 'point'){
                 return errorHandler.throw('scoring', new Error('The variable ' + identifier + ' must be of type point, but it is a ' + variable.baseType));
            }

            //cast the variable value
            variable = this.preProcessor.parseVariable(variable);


            //retrieve attributes
            defaultValue = parseFloat(variable.mapping.attributes.defaultValue) || 0;
            if(typeof variable.mapping.attributes.lowerBound !== 'undefined'){
                lowerBound = parseFloat(variable.mapping.attributes.lowerBound);
            }
            if(typeof variable.mapping.attributes.upperBound !== 'undefined'){
                upperBound = parseFloat(variable.mapping.attributes.upperBound);
            }

            //points are in an array
            if(variable.cardinality === 'single'){
                points = [variable.value];
            } else {
                points = variable.value;
            }

            //resolve the mapping

            //filter entries that match
            filtered = _.filter(variable.mapping.mapEntries, function(mapEntry){
                    var found = _.filter(points, function(point){
                        var coords = _.map(mapEntry.coords.split(','), parseFloat);

                        return isPointInShape(mapEntry.shape, point, coords);
                    });
                    return found.length > 0;
                });

            //then sum the entries values
            if(filtered.length){
                result.value = _.reduce(filtered, function(acc, mapEntry){
                    var value = parseFloat(mapEntry.mappedValue);
                    return acc + (_.isNaN(value) ? 0 : value);
                }, 0);
            }

            // apply attributes
            if(!_.isNumber(result.value)){
                result.value = defaultValue;
            }
            if(_.isNumber(lowerBound) && result.value < lowerBound){
               result.value = lowerBound;
            }
            if(_.isNumber(upperBound) && result.value > upperBound){
               result.value = upperBound;
            }

            return result;
        }
    };

    return mapResponseProcessor;
});

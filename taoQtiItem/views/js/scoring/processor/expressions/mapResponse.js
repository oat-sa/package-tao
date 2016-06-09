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
 * The mapResponse expression processor.
 *
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10579
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler, preProcessor){
    'use strict';

    /**
     * The MapResponse Processor
     * @type {ExpressionProcesssor}
     * @exports taoQtiItem/scoring/processor/expressions/mapResponse
     */
    var mapResponseProcessor = {

        /**
         * Process the expression
         * @returns {ProcessingValue} the value from the expression
         */
        process : function(){

            var self = this;
            var mapEntries,
                mapResult,
                defaultValue,
                lowerBound,
                upperBound;
            var identifier = this.expression.attributes.identifier;
            var variable   = this.state[identifier];
            var result     = {
                cardinality : 'single',
                baseType    : 'float'
            };

            if(typeof variable === 'undefined'){
                 return errorHandler.throw('scoring', new Error('No variable found with identifier ' + identifier ));
            }

            if(variable === null || typeof variable.mapping === 'undefined' || variable.mapping.qtiClass !== 'mapping'){
                 return errorHandler.throw('scoring', new Error('The variable ' + identifier + ' has no mapping, how can I execute a mapResponse on it?'));
            }

            //cast the variable value
            variable = this.preProcessor.parseVariable(variable);

            //cast each map value
            mapEntries = _.map(variable.mapping.mapEntries, function(mapEntry){
                mapEntry.mapKey = self.preProcessor.parseValue(mapEntry.mapKey, variable.baseType, 'single');
                return mapEntry;
            });

            //retrieve attributes
            defaultValue = parseFloat(variable.mapping.attributes.defaultValue) || 0;
            if(typeof variable.mapping.attributes.lowerBound !== 'undefined'){
                lowerBound = parseFloat(variable.mapping.attributes.lowerBound);
            }
            if(typeof variable.mapping.attributes.upperBound !== 'undefined'){
                upperBound = parseFloat(variable.mapping.attributes.upperBound);
            }

            //resolve the mapping
            if(variable.cardinality === 'single'){

                //find the map entry that matches with the value
                mapResult = _.find(mapEntries, function(mapEntry){
                    if(variable.baseType === 'string' && mapEntry.attributes.caseSensitive === false){
                        return _.isEqual(mapEntry.mapKey.toLowerCase(), variable.value.toLowerCase());
                    }
                    return _.isEqual(mapEntry.mapKey, variable.value);
                });

                if(mapResult !== undefined){
                    result.value = parseFloat(mapResult.mapValue);
                }

            } else if (variable.cardinality === 'multiple' || variable.cardinality === 'ordered'){

                //get the entries that matches and sum their values
                mapResult = _(mapEntries)
                    .filter(function(mapEntry){
                        var found;
                        if(variable.baseType === 'string' && mapEntry.attributes.caseSensitive === false){
                            return _.contains( _.invoke(variable.value, 'toLowerCase'),  mapEntry.mapKey.toLowerCase());
                        }
                        if(_.isArray(mapEntry.mapKey)){
                            found = _.find(variable.value, mapEntry.mapKey);
                            return found && found.length > 0;
                        }
                        return _.contains(variable.value,  mapEntry.mapKey);
                    })
                    .reduce(function(sum, mapEntry){
                        return sum + parseFloat(mapEntry.mapValue);
                    }, 0);

                if(mapResult !== undefined){
                    result.value = mapResult;
                }
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

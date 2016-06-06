/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
define(['lodash', 'taoQtiItem/qtiCommonRenderer/helpers/PciPrettyPrint'], function(_, pciPrettyPrint){
    'use strict';

    var _qtiModelPciResponseCardinalities = {
        single : 'base',
        multiple : 'list',
        ordered : 'list',
        record : 'record'
    };

    return {

        /**
         * Parse a response variable formatted according to IMS PCI: http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         *
         * @see serialize
         * @param {Object} response
         * @param {Object} interaction
         * @returns {Array}
         */
        unserialize : function(response, interaction){

            var ret = [],
                responseDeclaration = interaction.getResponseDeclaration(),
                baseType = responseDeclaration.attr('baseType'),
                cardinality = responseDeclaration.attr('cardinality'),
                mappedCardinality;

            if(_qtiModelPciResponseCardinalities[cardinality]){
                mappedCardinality = _qtiModelPciResponseCardinalities[cardinality];
                var responseValues = response[mappedCardinality];

                if(responseValues === null){
                    ret = [];
                }else if(_.isObject(responseValues)){
                    if(responseValues[baseType] !== undefined){
                        ret = responseValues[baseType];
                        ret = _.isArray(ret) ? ret : [ret];
                    }else{
                        throw 'invalid response baseType';
                    }
                }else{
                    throw 'invalid response cardinality, expected '+cardinality+' ('+mappedCardinality+')';
                }
            }else{
                throw 'unknown cardinality in the responseDeclaration of the interaction';
            }

            return ret;
        },
        /**
         * Serialize the input response array into the format to be send to result server according to IMS PCI recommendation :
         * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * With the only exception for empty response, which is represented by a javascript "null" value
         *
         * @see http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * @param {Array} responseValues
         * @param {Object} interaction
         * @returns {Object|null}
         */
        serialize : function(responseValues, interaction){

            if(!_.isArray(responseValues)){
                throw 'invalid argument : responseValues must be an Array';
            }

            var response = {},
                responseDeclaration = interaction.getResponseDeclaration(),
                baseType = responseDeclaration.attr('baseType'),
                cardinality = responseDeclaration.attr('cardinality'),
                mappedCardinality;

            if(_qtiModelPciResponseCardinalities[cardinality]){
                mappedCardinality = _qtiModelPciResponseCardinalities[cardinality];
                if(mappedCardinality === 'base'){
                    if(responseValues.length === 0){
                        //return empty response:
                        response.base = null;
                    }else{
                        response.base = {};
                        response.base[baseType] = responseValues[0];
                    }
                }else{
                    response[mappedCardinality] = {};
                    response[mappedCardinality][baseType] = responseValues;
                }
            }else{
                throw 'unknown cardinality in the responseDeclaration of the interaction';
            }

            return response;
        },
        isEmpty : function(response){
            return (
                response === null
                || _.isEmpty(response)
                || response.base === null
                || _.isArray(response.list) && _.isEmpty(response.list)
                || _.isArray(response.record) && _.isEmpty(response.record)
            );
        },

        /**
         * Print a PCI JSON response into a human-readable string.
         *
         * @param {Object} response A response in PCI JSON representation.
         * @returns {String} A human-readable version of the PCI JSON representation.
         */
        prettyPrint: function(response) {
            var print = '';

            if (typeof response.base !== 'undefined') {
                // -- BaseType.
                print += pciPrettyPrint.printBase(response, true);
            }
            else if (typeof response.list !== 'undefined') {
                // -- ListType
                print += pciPrettyPrint.printList(response, true);
            }
            else if (typeof response.record !== 'undefined') {
                // -- RecordType
                print += pciPrettyPrint.printRecord(response, true);
            }
            else {
                throw 'Not a valid PCI JSON Response';
            }

            return print;
        }
    };
});

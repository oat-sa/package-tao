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
 * The isNull operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10616
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * Process operands and returns a boolean of the  isNull.
     * @type {OperatorProcesssor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/isNull
     */
    var isNullProcessor = {

        constraints : {
            minOperand  : 1,
            maxOperand  : 1,
            cardinality : ['single', 'multiple', 'ordered', 'record'],
            baseType    : ['identifier', 'boolean', 'integer', 'float', 'string', 'point', 'pair', 'directedPair', 'duration', 'file', 'uri', 'intOrIdentifier']
        },

        operands   : [],

        /**
         * Check if the unique operand is null.
         * @returns {?ProcessingValue} a single boolean
         */
        process : function(){

            var result = {
                cardinality : 'single',
                baseType    : 'boolean',
                value       : false
            };

            if(this.operands[0] === null || this.operands[0].value === null){
                result.value = true;
            }

            return result;
        }
    };

    return isNullProcessor;
});

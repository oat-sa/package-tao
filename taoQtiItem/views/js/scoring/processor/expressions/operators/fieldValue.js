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
 * The fieldValue operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10621
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * Process operands and returns fieldValue result.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/fieldValue
     */
    var fieldValueProcessor = {

        constraints : {
            minOperand  : 1,
            maxOperand  : 1,
            cardinality : ['record'],
            baseType    : [undefined]
        },

        operands   : [],

        /**
         * @returns {?ProcessingValue} a single boolean
         */
        process : function(){

            var result = {};

            var fieldIdentifier = this.expression.attributes.fieldIdentifier;

            var record = this.preProcessor
                .parseVariable(this.operands[0]).value;
                //.parseOperands(this.operands).valueOf()[0];

            if (!record[fieldIdentifier]) {
                return null;
            }

            result.value = record[fieldIdentifier].value;
            result.baseType = record[fieldIdentifier].baseType;
            result.cardinality = record[fieldIdentifier].cardinality;
            return result;
        }

    };


    return fieldValueProcessor;
});


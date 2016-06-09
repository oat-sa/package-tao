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
 * The anyN operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10641
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * Process operands and returns the anyN.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/anyN
     */
    var anyNProcessor = {

        constraints : {
            minOperand : 1,
            maxOperand : -1,
            cardinality : ['single'],
            baseType : ['boolean']
        },

        operands   : [],

        /**
         * Process the anyN of the operands.
         * @returns {?ProcessingValue} is anyN or null
         */
        process : function(){

            var result = {
                cardinality : 'single',
                baseType : 'boolean'
            };

            var min = this.preProcessor.parseValue(this.expression.attributes.min, 'integerOrVariableRef'),
                max = this.preProcessor.parseValue(this.expression.attributes.max, 'integerOrVariableRef');

            var counted = this.preProcessor.parseOperands(this.operands).countBy().value();
            if (counted.true >= min && counted.true <= max ){
                result.value = true;
            }else

            if (counted.true + counted.null >= min && counted.true + counted.null <= max) {
                // It could have match if nulls were true values.
                return null;
            }else{
                result.value = false;
            }

            return result;
        }
    };

    return anyNProcessor;
});

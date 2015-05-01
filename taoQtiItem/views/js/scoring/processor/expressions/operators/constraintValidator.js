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
 * This module provide constraint validation of operators
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler){
    'use strict';

    /**
     * Validate if operator match constraints
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/constraintValidator
     */
    var validator = {

        /**
         * @param {OperatorProcessor} processor
         * @param {Array} [operands] - the operands for an operator processor
         * @returns {Boolean}
         */
         validate: function validate(processor, operands){

            var size = 0;
            var minOperand  = processor.constraints.minOperand;
            var maxOperand  = processor.constraints.maxOperand;

            var hasWrongType = function hasWrongType(operand){
                return !_.contains(processor.constraints.baseType, operand.baseType);
            };

            var hasWrongCardinality = function hasWrongCardinality(operand){
                return !_.contains(processor.constraints.cardinality, operand.cardinality);
            };

            if(!_.isArray(operands)){
                return errorHandler.throw('scoring', new TypeError('Processor ' + name + ' requires operands to be an array : ' +  (typeof operands)  + ' given'));
            }
            size = _.size(operands);

            if(minOperand > 0 && size < minOperand){
                return errorHandler.throw('scoring', new TypeError('Processor ' + name + ' requires at least ' + minOperand + ' operands, ' + size + ' given'));
            }
            if(maxOperand > -1 && size > maxOperand){
                return errorHandler.throw('scoring', new TypeError('Processor ' + name + ' requires maximum ' + maxOperand + ' operands, ' + size + ' given'));
            }
            if(_.some(operands, hasWrongType)){
                return errorHandler.throw('scoring', new TypeError('An operand given to processor ' + name + ' has an unexpected baseType'));
            }
            if(_.some(operands, hasWrongCardinality)){
                return errorHandler.throw('scoring', new TypeError('An operand given to processor ' + name + ' has an unexpected cardinality'));
            }
            return true;
        }

    };

    return validator.validate;
});

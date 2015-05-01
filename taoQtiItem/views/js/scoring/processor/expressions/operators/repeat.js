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
 * The repeat operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element106215
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler){
    'use strict';

    /**
     * Process operands and returns repeat result.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/repeat
     */
    var repeatProcessor = {

        constraints : {
            minOperand  : 0,
            maxOperand  : -1,
            cardinality : ['single', 'ordered'],
            baseType    : ['identifier', 'boolean', 'integer', 'float', 'string', 'point', 'pair', 'directedPair', 'duration', 'file', 'uri', 'intOrIdentifier']
        },

        operands   : [],
        state: {},

        /**
         * @returns {?ProcessingValue} a single boolean
         */
        process : function(){

            var result = {
                cardinality : 'ordered',
                value : []
            };

            var numberRepeats = this.preProcessor.parseValue(this.expression.attributes.numberRepeats, 'integerOrVariableRef');

            //if all one operands are null or no operands, then break and return null
            if (numberRepeats < 1 || _.every(this.operands, _.isNull) === true || this.operands.length === 0) {
                return null;
            }

            var filteredOperands = _(this.operands).filter(_.isObject).value();

            if (Object.keys(_.countBy(filteredOperands, 'baseType')).length !== 1) {
                errorHandler.throw('scoring', new Error('operands must be of the same type'));
                return null;
            }

            result.baseType = this.operands[0].baseType;

            var value = this.preProcessor.parseOperands(filteredOperands).value();

            while (numberRepeats-- > 0) {
                result.value.push(value);
            }

            result.value = _.flatten(result.value, true);

            return result;
        }

    };


    return repeatProcessor;
});


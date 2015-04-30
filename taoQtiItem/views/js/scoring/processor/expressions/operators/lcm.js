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
 * The lcm operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element106213
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    var lcmProcessor = {

        constraints : {
            minOperand : 1,
            lcmOperand : -1,
            cardinality : ['single', 'multiple', 'ordered'],
            baseType : ['integer']
        },

        operands   : [],

        /**
         * Process operands and returns the lcm.
         * @type {OperatorProcessor}
         * @exports taoQtiItem/scoring/processor/expressions/operators/lcm
         */
        process : function(){

            var result = {
                cardinality : 'single',
                baseType : 'integer'
            };

            //if at least one operand is null or infinity, then break and return null
            if(_.some(this.operands, _.isNull) === true){
                return null;
            }

            var castedOperands  = this.preProcessor.parseOperands(this.operands);

            //if at least one operand is a not a number,  then break and return null
            if (!castedOperands.every(this.preProcessor.isNumber)) {
                return null;
            }

            if (castedOperands.value().indexOf(0) !== -1 ) {
                result.value = 0;
                return result;
            }

            result.value = lcm(castedOperands.value());

            return result;
        }
    };

    /**
     * Calculates least common multiple for two ore more integers
     * @param {Array<number|Number>}numbers
     * @returns {number} least common multiple
     */
    function lcm(numbers)
    {
        var n = numbers.length, a = Math.abs(numbers[0]);
        for (var i = 1; i < n; i++)
        { var b = Math.abs(numbers[i]), c = a;
            while (a && b){ a > b ? a %= b : b %= a; }
            a = Math.abs(c*numbers[i])/(a+b);
        }
        return a;
    }

    return lcmProcessor;
});

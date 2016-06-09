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
 * The gcd operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element106211
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * Process operands and returns the gcd.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/gcd
     */
    var gcdProcessor = {

        constraints : {
            minOperand : 1,
            gcdOperand : -1,
            cardinality : ['single', 'multiple', 'ordered'],
            baseType : ['integer']
        },

        operands   : [],

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

            if (castedOperands.max().value() === 0 && castedOperands.min().value() === 0) {
                result.value = 0;
                return result;
            }

            result.value = gcd(castedOperands.value());

            return result;
        }
    };

    /**
     * Helps to calculate greatest common divisor for two ore more numbers
     * @param {Array<number|Number>}
     * @returns {number} greatest common divisor
     */
    function gcd(numbers)
    {
        var n = numbers.length,
            y = 0,
            x = Math.abs(numbers[0]);
        for (var i = 1; i < n; i++) {
            y = Math.abs(numbers[i]);
            while (x && y) {
                x > y ? x %= y : y %= x;
            }
            x += y;
        }
        return x;
    }

    return gcdProcessor;
});

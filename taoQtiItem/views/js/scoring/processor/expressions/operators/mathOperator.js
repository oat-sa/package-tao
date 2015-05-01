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
 * The mathOperator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element106451
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * Process operands and returns the mathOperator result.
     * @type {OperandProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/mathOperator
     */
    var mathOperatorProcessor = {

        accuracy : 0.00000000000001,

        constraints : {
            minOperand : 1,
            maxOperand : -1,
            cardinality : ['single'],
            baseType : ['float','integer']
        },

        operands   : [],

        /**
         * Process the mathOperator of the operands.
         * @returns {?ProcessingValue} the and or null
         */
        process : function(){

            var result = {
                cardinality : 'single',
                baseType : 'float'
            };

            //if at least one operand is null, then break and return null
            if(_.some(this.operands, _.isNull) === true){
                return null;
            }

            var name = this.expression.attributes.name;

            if (!_.contains(Object.keys(functions), name)) {
                return null;
            }

            var operands = this.preProcessor.parseOperands(this.operands).value();

            if (_.some(operands, _.isNaN)) {
                return null;
            }

            if (_.isFunction(functions[name])) {
                result.value = functions[name](operands[0]);
            } else {
                result.value = functions[name].func(operands);
            }

            if (_.isNull(result.value) || _.isNaN(result.value)) {
                return null;
            }
            return result;
        }
    };

    var functions = {
        sin: Math.sin,
        cos: Math.cos,
        tan: Math.tan,
        sec: reciprocalFactory(Math.cos),
        csc: reciprocalFactory(Math.sin),
        cot: reciprocalFactory(Math.tan),
        asin: Math.asin,
        acos: Math.acos,
        atan: Math.atan,
        atan2: {
            func: atan2
        },
        asec: inversedFactory(Math.acos),
        acsc: inversedFactory(Math.asin),
        acot: acot,
        sinh: sinh,
        cosh: cosh,
        tanh: tanh,
        sech: reciprocalFactory(cosh),
        csch: reciprocalFactory(sinh),
        coth: reciprocalFactory(tanh),
        log: log10,
        ln: Math.log,
        exp: Math.exp,
        abs: Math.abs,
        signum: signum,
        floor: Math.floor,
        ceil: Math.ceil,
        toDegrees: toDegrees,
        toRadians: toRadians
    };

    /**
     /**
     * Calculate inverse trigonometric tangent
     * @param {Array<Number>}arg
     * @returns {Number}
     */
    function atan2(arg) {
        return Math.atan2(arg[0], arg[1]);
    }

    /**
     * Calculate reciprocal trigonometric function
     * @param {Function}trigonometric
     * @returns {Function}
     */
    function reciprocalFactory(trigonometric) {
        return function (operands) {
            var trig = trigonometric(operands);

            if (close(trig, 0, mathOperatorProcessor.accuracy)) {
                return null;
            }
            return 1 / trig;
        };
    }

    /**
     * Calculate inversed trigonometric function
     * @param {Function}trigonometric
     * @returns {Function}
     */
    function inversedFactory(trigonometric) {
        return function (operand) {

            if (Math.abs(operand) < 1) {
                return null;
            }

            return trigonometric([1 / operand]);
        };
    }

    /**
     * The inverse trigonometric cotangent
     * @param {Number} operand
     * @returns {Number}
     */
    function acot(operand) {
        return Math.atan([1 / operand]);
    }

    /**
     * The hyperbolic sine
     * @param {Number} operand
     * @returns {Number}
     */
    function sinh(operand) {
        var y = Math.exp(operand);
        return (y - 1 / y) / 2;
    }

    /**
     * The hyperbolic cosine
     * @param {Number}operand
     * @returns {Number}
     */
    function cosh(operand) {
        var y = Math.exp(operand);
        return (y + 1 / y) / 2;
    }

    /**
     * The hyperbolic tangent
     * @param {Number}operand
     * @returns {Number}
     */
    function tanh (operand){
        if (operand === Infinity) {
            return 1;
        } else if (operand === -Infinity) {
            return -1;
        } else {
            var y = Math.exp(2 * operand);
            return (y - 1) / (y + 1);
        }
    }

    /**
     * The logarithm to base 10
     * @param {Number} operand
     * @returns {Number}
     */
    function log10(operand){
        return Math.log(operand) / Math.LN10;
    }

    /**
     * The signum function
     * @param {Number}operand
     * @returns {Number|null}
     */
    function signum(operand) {
        if (operand === 0) {
            return 0;
        }
        return operand > 0 ? 1 : -1;
    }

    /**
     * Convert radians to degrees
     * @param {Number}operand
     * @returns {Number}
     */
    function toDegrees(operand) {
        return operand * (180 / Math.PI);
    }

    /**
     * Convert degrees to raians
     * @param {Number}operand
     * @returns {Number}
     */
    function toRadians(operand) {
        return operand * (Math.PI / 180);
    }

    /**
     * Check if actual value is close to expected with given accuracy
     * @param {Number} actual
     * @param {Number} expected
     * @param {Number} maxDifference
     * @returns {boolean}
     */
    function close(actual, expected, maxDifference) {
        return ((actual === expected) ? 0 : Math.abs(actual - expected)) <= maxDifference;
    }

    return mathOperatorProcessor;
});

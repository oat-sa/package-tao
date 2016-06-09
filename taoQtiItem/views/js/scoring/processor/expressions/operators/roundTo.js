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
 * The roundTo operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element106470
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler){
    'use strict';

    /**
     * Process operands and returns roundTo result.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/roundTo
     */
    var roundToProcessor = {

        engines: {
            significantFigures: function (value, exp) {
                return decimalAdjust('round', value, exp);
            },
            decimalPlaces: function (value, exp) {
                return decimalAdjust('floor', value, exp);
            }
        },

        constraints : {
            minOperand  : 1,
            maxOperand  : 1,
            cardinality : ['single'],
            baseType    : ['integer', 'float']
        },

        operands   : [],

        /**
         * @returns {?ProcessingValue} a single boolean
         */
        process : function(){

            var roundingMode = _.isFunction(this.engines[this.expression.attributes.roundingMode]) ? this.engines[this.expression.attributes.roundingMode] : this.engines.significantFigures;
            var figures = this.preProcessor.parseValue(this.expression.attributes.figures, 'integerOrVariableRef');

            if (!this.preProcessor.isNumber(figures)) {
                errorHandler.throw('scoring', new Error('figures must be numeric'));
                return null;
            }

            if (figures <= 1 && roundingMode === this.engines.significantFigures) {
                errorHandler.throw('scoring', new Error('significantFigures must be numeric'));
                return null;
            }

            var result = {
                cardinality : 'single',
                baseType    : 'float'
            };

            //if at least one operand is null, then break and return null
            if(_.some(this.operands, _.isNull) === true){
                return null;
            }

            var value = this.preProcessor.parseVariable(this.operands[0]).value;

            if ( !_.isFinite(value) ) {
                result.value = value;
                return result;
            }

            result.value = roundingMode(value, figures);

            return result;
        }
    };


    /**
     * Decimal adjustment of a number.
     *
     * @param {String}  type  The type of adjustment.
     * @param {Number}  value The number.
     * @param {Integer} exp   The exponent (the 10 logarithm of the adjustment base).
     * @returns {Number} The adjusted value.
     * @private
     */
    function decimalAdjust(type, value, exp) {

        // Shift
        value = value.toString().split('e');
        value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));
        // Shift back
        value = value.toString().split('e');
        return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
    }

    return roundToProcessor;
});


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
 * The statsOperator operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element106460
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * Process operands and returns the statsOperator.
     * @type {OperandProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/statsOperator
     */
    var statsOperator = {

        constraints : {
            minOperand : 1,
            maxOperand : 1,
            cardinality : ['multiple','ordered'],
            baseType : ['float','integer']
        },

        operands   : [],

        algorithms: {
            mean: mean,
            sampleVariance: sampleVariance,
            sampleSD: sampleSD,
            popVariance: popVariance,
            popSD: popSD
        },

        /**
         * Process the statsOperator of the operands.
         * @returns {?ProcessingValue} the and or null
         */
        process: function () {

            var result = {
                cardinality: 'single',
                baseType: 'float'
            };

            //if at least one operand is null, then break and return null
            if (_.some(this.operands, _.isNull) === true) {
                return null;
            }

            var operand = this.preProcessor.parseVariable(this.operands[0]).value;
            if (!_.every(operand, this.preProcessor.isNumber)) {
                return null;
            }

            var name = this.expression.attributes.name;

            result.value = _.isFunction(this.algorithms[name]) ? this.algorithms[name](operand) : null;

            if (_.isNull(result.value)) {
                return null;
            }

            return result;
        }
    };

    /**
     * Calculate the arithmetic mean
     * @param {Array<Number>}values
     * @returns {number}
     */
    function mean(values) {
        return _(values).reduce(function (s, v) {
                return s + v;
            }) / values.length;
    }

    /**
     * Calculate the variance of observations
     * @param {Array<number>}values
     * @returns {number|null}
     */
    function sampleVariance(values) {
        return getVariance(values, true);
    }

    /**
     * Calculate Standard deviation of observations
     * @param {Array<number>}values
     * @returns {number|null}
     */
    function sampleSD(values) {
        return getDeviation(values, true);
    }

    /**
     * Calculate Population Variance
     * @param {Array<number>}values
     * @returns {number|null}
     */
    function popVariance(values) {
        return getVariance(values, false);
    }

    /**
     * Calculate Standard deviation
     * @param {Array<number>}values
     * @returns {number|null}
     */
    function popSD(values) {
        return getDeviation(values, false);
    }

    /**
     * Calculate Standard variance
     * @param {Array<number>}values
     * @param {boolean}c apply correction
     * @returns {*}
     */
    function getVariance(values, c) {
        if (values.length === 1) {
            return null;
        }
        var mean2 = mean(values),
            s = 0;
        return _.reduce(values, function (s, v) {
                return s + Math.pow(mean2 - v, 2);
            }, s) / (c ? values.length - 1 : values.length);
    }

    /**
     * Calculate Standard deviation
     * @param {Array<number>}values
     * @param {boolean}c apply correction
     * @returns {*}
     */
    function getDeviation(values, c) {
        if (values.length === 1) {
            return null;
        }
        var mean2 = mean(values),
            s = 0;
        return Math.sqrt(_.reduce(values, function (s, v) {
            return s + Math.pow(mean2 - v, 2);
        }, s) / (c ? values.length - 1 : values.length));
    }

    return statsOperator;
});

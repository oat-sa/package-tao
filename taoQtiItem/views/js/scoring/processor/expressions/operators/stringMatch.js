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
 * The stringMatch operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10647
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function (_) {
    'use strict';

    /**
     * Process operands and returns the stringMatch.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/stringMatch
     */
    var stringMatchProcessor = {

        constraints: {
            minOperand: 2,
            maxOperand: 2,
            cardinality: ['single'],
            baseType: ['string']
        },

        operands: [],

        /**
         * Process the stringMatch of the operands.
         * @returns {?ProcessingValue} the stringMatch or null
         */
        process: function () {
            var result = {
                cardinality: 'single',
                baseType: 'boolean'
            };

            //if at least one operand is null, then break and return null
            if (_.some(this.operands, _.isNull) === true) {
                return null;
            }
            var values = this.operands,
                caseSensitive = _.isBoolean(this.expression.attributes.caseSensitive) ? this.expression.attributes.caseSensitive : true;

            var v1 = this.preProcessor.parseVariable(values[0]).value,
                v2 = this.preProcessor.parseVariable(values[1]).value;


            result.value = caseSensitive ? v1 === v2 : v1.toUpperCase() === v2.toUpperCase();

            return result;
        }

    };

    return stringMatchProcessor;
});

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
 * The equal operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10654
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler){
    'use strict';

    /**
     * Process operands and returns the equal.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/equal
     */
    var equalProcessor = {

        //equality algos based on different tolerance modes
        engines: {
            exact: function (x, y) {
                return x === y;
            },
            absolute: function (x, y, includeLowerBound, includeUpperBound, tolerance) {
                var lower = includeLowerBound ? y >= x - tolerance[0] : y > x - tolerance[0],
                    upper = includeUpperBound ? y <= x + tolerance[1] : y < x + tolerance[1];
                return lower && upper;
            },
            relative: function (x, y, includeLowerBound, includeUpperBound, tolerance) {
                var lower = includeLowerBound ? y >= x - (1 - tolerance[0] / 100) : y > x - (1 - tolerance[0] / 100),
                    upper = includeUpperBound ? y <= x + (1 - tolerance[1] / 100) : y < x + (1 - tolerance[1] / 100);

                return lower && upper;
            }
        },

        constraints : {
            minOperand : 2,
            maxOperand : 2,
            cardinality : ['single'],
            baseType : ['integer', 'float']
        },

        operands   : [],

        /**
         * Process the equal of the operands.
         * @returns {?ProcessingValue} is equal or null
         */
        process : function(){

            var result = {
                cardinality : 'single',
                baseType : 'boolean'
            };

            var attributes          = this.expression.attributes;
            var toleranceMode       = attributes.toleranceMode || 'exact';
            var engine              = this.engines[toleranceMode];
            var tolerance           = attributes.tolerance ? attributes.tolerance.toString().split(' ') : [];
            var includeLowerBound   = _.isString(attributes.includeLowerBound) || _.isBoolean(attributes.includeLowerBound) ? this.preProcessor.parseValue(attributes.includeLowerBound, 'boolean') : true;
            var includeUpperBound   = _.isString(attributes.includeUpperBound) || _.isBoolean(attributes.includeUpperBound) ? this.preProcessor.parseValue(attributes.includeUpperBound, 'boolean') : true;


            if (!_.isFunction(engine) || (_.contains(['absolute', 'relative'], toleranceMode) && tolerance.length === 0)) {
                return errorHandler.throw('scoring', new Error('tolerance must me specified'));
            }

            //if at least one operand is null, then break and return null
            if(_.some(this.operands, _.isNull) === true){
                return null;
            }

            tolerance = _(tolerance).map(function (t) {
                return equalProcessor.preProcessor.parseValue(t, 'floatOrVariableRef');
            }).value();

            // if only one tolerance bound is given it is used for both.
            if (tolerance.length === 1) {
                tolerance.push(tolerance[0]);
            }

            result.value = engine(this.preProcessor.parseVariable(this.operands[0]).value,
                this.preProcessor.parseVariable(this.operands[1]).value, includeLowerBound, includeUpperBound, tolerance);

            return result;
        }
    };

    return equalProcessor;
});

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
 * The randomInteger expression processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10584
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler){
    'use strict';

    /**
     * Correct expression
     * @type {ExpressionProcesssor}
     * @exports taoQtiItem/scoring/processor/expressions/randomInteger
     */
    var randomIntegerProcessor = {

        /**
         * Process the expression
         * @returns {ProcessingValue} the value from the expression
         */
        process : function(){

            var range;
            var min         = this.preProcessor.parseValue(this.expression.attributes.min, 'integerOrVariableRef');
            var max         = this.preProcessor.parseValue(this.expression.attributes.max, 'integerOrVariableRef');
            var step        = typeof this.expression.attributes.step !== 'undefined' ? this.preProcessor.parseValue(this.expression.attributes.step, 'integerOrVariableRef') : 1;

            var result = {
                cardinality : 'single',
                baseType : 'integer'
            };

            //verfiy attributes
            if(_.isNaN(min) || !_.isFinite(min)){
                min = 0;
            }
            if(_.isNaN(max) || !_.isFinite(max)){
                return errorHandler.throw('scoring', new Error('The max value of a randomInteger expresssion should be a finite integer.'));
            }

            if(min > max){
                return errorHandler.throw('scoring', new Error('Come on! How am I supposed to generate a random number from a negative range : min > max'));
            }

            if(_.isNaN(step) || !_.isFinite(step)){
                step = 1;
            }

            //get the random value
            if(step !== 1){
                range = _.range(min, max, step);
                if(!_.contains(range, max)){
                    range.push(max);
                }
                result.value = _.sample(range);
            } else {
                result.value = _.random(min, max);
            }

            return result;
        }
    };

    return randomIntegerProcessor;
});

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
 * The randomFloat expression processor.
 *
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10588
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
     * @exports taoQtiItem/scoring/processor/expressions/randomFloat
     */
    var randomFloatProcessor = {

        /**
         * Process the expression
         * @returns {ProcessingValue} the value from the expression
         */
        process : function(){

            var range;
            var min         = this.preProcessor.parseValue(this.expression.attributes.min, 'floatOrVariableRef');
            var max         = this.preProcessor.parseValue(this.expression.attributes.max, 'floatOrVariableRef');

            var result = {
                cardinality : 'single',
                baseType    : 'float'
            };

            //verfiy attributes
            if(_.isNaN(min) || !_.isFinite(min)){
                min = 0;
            }
            if(_.isNaN(max) || !_.isFinite(max)){
                return errorHandler.throw('scoring', new Error('The max value of a randomFloat expresssion should be a finite integer.'));
            }

            if(min > max){
                return errorHandler.throw('scoring', new Error('Come on! How am I supposed to generate a random number from a negative range : min > max'));
            }

            //get the random value
            result.value = _.random(min, max, true);

            return result;
        }
    };

    return randomFloatProcessor;
});

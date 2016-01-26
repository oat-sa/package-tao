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
 * The match operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10645
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * Process operands and returns the match.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/substring
     */
    var matchProcessor = {

        constraints : {
            minOperand : 2,
            maxOperand : 2,
            cardinality : ['single', 'multiple', 'ordered'],
            baseType : ['string', 'identifier', 'boolean', 'integer', 'float', 'pair', 'directedPair']
        },

        operands   : [],

        /**
         * Process the match of the operands.
         * @returns {?ProcessingValue} the match or null
         */
        process : function(){

            var result = {
                cardinality : 'single',
                baseType : 'boolean'
            };

            //if at least one operand is null, then break and return null
            if(_.some(this.operands, _.isNull) === true){
                return null;
            }

            result.value = _.isEqual(
                this.preProcessor.parseVariable(this.operands[0]),
                this.preProcessor.parseVariable(this.operands[1])
            );

            return result;
        }

};

    return matchProcessor;
});

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
 * The member operator processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10626
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler){
    'use strict';

    /**
     * Process operands and returns member result.
     * @type {OperatorProcessor}
     * @exports taoQtiItem/scoring/processor/expressions/operators/member
     */
    var memberProcessor = {

        constraints : {
            minOperand  : 2,
            maxOperand  : 2,
            cardinality : ['multiple', 'ordered', 'single'],
            baseType    : ['identifier', 'boolean', 'integer', 'string', 'point', 'pair', 'directedPair', 'file', 'uri', 'intOrIdentifier']
        },

        operands   : [],

        /**
         * @returns {?ProcessingValue} a single boolean
         */
        process : function(){

            var result = {
                cardinality : 'single',
                baseType    : 'boolean'
            };

            //if at least one operand is null, then break and return null
            if(_.some(this.operands, _.isNull) === true){
                return null;
            }

            if (Object.keys(_.countBy(this.operands, 'baseType')).length !== 1) {
                errorHandler.throw('scoring', new Error('operands must be of the same baseType'));
                return null;
            }

            if (this.operands[0].cardinality !== 'single' || ['multiple', 'ordered'].indexOf(this.operands[1].cardinality) === -1) {
                errorHandler.throw('scoring', new Error('operands must be single and multiply|ordered cardinality respectively'));
                return null;
            }

            var op1 = this.preProcessor.parseVariable(this.operands[0]).value,
                op2 = this.preProcessor.parseVariable(this.operands[1]).value;


            if (_.isArray(op1)) {
                result.value = _.flatten(op2).join().indexOf(op1) !== -1;
            } else {
                result.value = op2.indexOf(op1) !== -1;
            }

            return result;
        }

    };


    return memberProcessor;
});


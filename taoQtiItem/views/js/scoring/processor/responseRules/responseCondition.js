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
 * The responseCondition processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10421
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/scoring/processor/expressions/engine',
    'taoQtiItem/scoring/processor/errorHandler'
], function(expressionEngineFactory, errorHandler){
    'use strict';

    /**
     * The rule processor.
     *
     * @type {responseRuleProcessor}
     * @exports taoQtiItem/scoring/processor/responseRules/responseCondition
     */
    var responseConditionProcessor = {

        /**
         * Process the rule
         */
        process : function(){

            var index = 0;
            var expressionEngine = expressionEngineFactory(this.state);

            //eval a condition using the expression engine
            var evalRuleCondition = function evalRuleCondition(rule){
                var expressionResult;
                if(!rule.expression){
                    return false;
                }

                expressionResult = expressionEngine.execute(rule.expression);

                return expressionResult && expressionResult.value === true;
            };


            //the if condition
            if(evalRuleCondition(this.rule.responseIf)){
                return this.rule.responseIf.responseRules;
            }

            //else if conditions
            if(this.rule.responseElseIfs){

                for(index in this.rule.responseElseIfs){
                    if(evalRuleCondition(this.rule.responseElseIfs[index])){
                        return this.rule.responseElseIfs[index].responseRules;
                    }
                }
            }

            //the else otherwise
            if(this.rule.responseElse){
                return this.rule.responseElse.responseRules;
            }
            return [];
        }
    };

    return responseConditionProcessor;
});

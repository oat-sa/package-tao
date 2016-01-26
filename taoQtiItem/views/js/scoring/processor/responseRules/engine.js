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
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * The engine that process QTI responses rules.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/responseRules/processor',
    'taoQtiItem/scoring/processor/responseRules/rules'
], function(_, processorFactory, rules){
    'use strict';

    //regsiter rules processors
    _.forEach(rules, function(rule, name){
        processorFactory.register(name, rule);
    });

    //list supported rules
    var supportedRules = _.keys(rules);

    /**
     * Creates an engine that can look over the rule and execute them accordingy.
     *
     * @exports taoQtiItem/scoring/processor/expressions/engine
     * @param {Object} state - the item session state (response and outcome variables)
     * @returns {Object} the rule engine that expose an execute method
     */
    var ruleEngineFactory = function ruleEngineFactory (state){

        return {

            /**
             * Check if rule is supported by the engine
             * @param {string} rule
             * @returns {Boolean}
             */
            isRuleSupported: function isRuleSupported(rule) {
                return _.contains(supportedRules, rule.qtiClass);
            },

            /**
             * Execute the engine on the given rule tree
             * @param {Array<Object>} rules - the rules to process
             * @return {Object} the modified state (it may not be necessary as the ref is modified)
             */
            execute : function(rules){
                if(rules){
                    if(!_.isArray(rules)){
                        rules = [rules];
                    }

                    _.forEach(rules, function processRule(rule){

                        var currentRule,
                            currentProcessor,
                            processResult;
                        var trail = [rule];

                        //TODO remove the limit and add a timeout
                        while(trail.length > 0){

                            currentRule = trail.pop();

                            //process response rule
                            currentProcessor = processorFactory(currentRule, state);
                            processResult = currentProcessor.process();

                            //a processor can exit the all processing by returning false
                            if(processResult === false){
                                break;
                            }

                            //if it returns response rules, then we add them to the trail
                            if(_.isArray(processResult)){
                                trail = trail.concat(_.filter(processResult, ruleEngineFactory.isRuleSupported).reverse());
                            }
                        }
                    });
                }
                return state;
            }
        };
    };

    return ruleEngineFactory;
});

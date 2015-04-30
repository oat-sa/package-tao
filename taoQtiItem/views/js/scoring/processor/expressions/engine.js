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
 * The engine that process QTI expressions.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/processor',
    'taoQtiItem/scoring/processor/expressions/expressions',
    'taoQtiItem/scoring/processor/expressions/operators/operators'
], function(_, processorFactory, expressionProcessors, operatorProcessors){
    'use strict';

    //get the list of available operators
    var operators = _.keys(operatorProcessors);

    //regsiter all processors
    _.forEach(expressionProcessors, function(expressionProcessor, name){
        processorFactory.register(name, processorFactory.types.EXPRESSION, expressionProcessor);
    });
    _.forEach(operatorProcessors, function(operatorProcessor, name){
        processorFactory.register(name, processorFactory.types.OPERATOR, operatorProcessor);
    });


    /**
     * Creates an engine that can look over the expressions and execute them accordingy.
     *
     * @exports taoQtiItem/scoring/processor/expressions/engine
     * @param {Object} state - the item session state (response and outcome variables)
     * @returns {Object} the expression engine
     */
    var expressionEngineFactory = function expressionEngineFactory(state){

        var trail = [];
        var marker = [];
        var operands = [];

        var isMarked = function isMarked(expression){
            return _.contains(marker, expression);
        };

        var mark = function mark(expression){
            marker.push(expression);
        };

        var isOperator = function isOperator(expression){
            return _.contains(operators, expression.qtiClass);
        };

        var pushSubExpressions = function pushSubExpressions(expression){
            _.forEach(expression.expressions, function(subExpression){
                trail.push(subExpression);
            });
        };

        var popOperands = function popOperands(expression){
            var r = _.reduce(expression.expressions, function(result){
                if(operands.length){
                    result.push(_.clone(operands.pop()));
                }
                return result;
            }, []);
            return r;
        };

        return {

            /**
             * Execute the engine on the given expression tree
             * @param {Object} expression - the expression to process
             * @return {?ProcessingValue} the result of the expression evaluation in the form of a variable
             */
            execute : function(expression){

                var currentExpression,
                    currentProcessor,
                    result;

                var baseExpression = expression.qtiClass;

                trail.push(expression);

                //TODO remove the limit and add a timeout
                while(trail.length > 0){

                    currentExpression = trail.pop();
                    currentProcessor = null;

                    if(!isMarked(currentExpression) && isOperator(currentExpression)){

                        mark(currentExpression);

                        trail.push(currentExpression);

                        //reverse push sub expressions
                        pushSubExpressions(currentExpression);

                    } else if (isMarked(currentExpression)){
                        // Operator, second pass. Process it.
                        currentProcessor = processorFactory(currentExpression, state, popOperands(currentExpression));
                        result = currentProcessor.process();

                        if (currentExpression.qtiClass !== baseExpression) {
                            operands.push(result);
                        }
                    } else {
                        // Simple expression, process it.
                        currentProcessor = processorFactory(currentExpression, state);
                        result = currentProcessor.process();

                        operands.push(result);
                    }
                }
                return result;
            }
        };
    };

    return expressionEngineFactory;
});

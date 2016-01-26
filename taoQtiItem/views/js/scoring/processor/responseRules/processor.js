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
 * Single entry point for the response rules processors.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, errorHandler){
    'use strict';


    //keeps the references of processors (this is something we may load dynamically)
    var processors = {};

    /**
     * Provides you a processor from an rule (definition) and against a state.
     * The Processor must have been registerd previously.
     * See it as a kind of factory.
     *
     * @param {Object} rule - the expression definition
     * @param {String} rule.qtiClass - the expression name that should mastch with the processor name
     * @param {Object} state - the state to give to the processor to lookup for variables like responses
     * @returns {RuleProcessorWrapper} a transparent object that delegates the calls to the processor
     * @throws {Error} by trying to use an unregistered processor
     */
    var responseRuleProcessor = function responseRuleProcessor(rule, state){

        var name      = rule.qtiClass;
        var processor = processors[name];

        if(!processor){
             return errorHandler.throw('scoring', new Error('No processor found for ' + name));
        }

        processor.rule = rule;
        processor.state = state;

        /**
         * Wrap and forward the processing to the enclosed processor
         * @typedef RuleProcessorWrapper
         * @property {Function} process - call's processor's process
         */
        return {
            process : function process(){
                //forward the call to the related expression processor
                return processor.process.apply(processor, [].slice.call(arguments, 1));
            }
        };
    };

    /**
     * Register a processor
     * @param {String} name - the processor name
     * @param {ResponseRuleProcessor} processor - the processor to register
     * @throws {TypeError} when a parameter isn't valid
     */
    responseRuleProcessor.register = function register(name, processor){

        if(_.isEmpty(name)){
             return errorHandler.throw('scoring', new TypeError('Please give a valid name to your processor'));
        }
        if(!_.isPlainObject(processor) || !_.isFunction(processor.process)){
             return errorHandler.throw('scoring', new TypeError('The processor must be an object that contains a process method.'));
        }

        processors[name] = processor;
    };

    /**
     * @exports taoQtiItem/scoring/processor/responseRule/processor
     */
    return responseRuleProcessor;
});

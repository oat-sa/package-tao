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
 * The default expression processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10575
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/scoring/processor/errorHandler'
], function(errorHandler){
    'use strict';

    /**
     * Default expression
     * @type {ExpressionProcesssor}
     * @exports taoQtiItem/scoring/processor/expressions/default
     */
    var defaultProcessor = {

        /**
         * Process the expression
         * @returns {ProcessingValue} the value from the expression
         */
        process : function(){

            var identifier = this.expression.attributes.identifier;
            var variable   = this.state[identifier];

            if(typeof variable === 'undefined'){
                 return errorHandler.throw('scoring', new Error('No variable found with identifier ' + identifier ));
            }

            if(variable === null || typeof variable.defaultValue === 'undefined'){
                return null;
            }

            //todo cast value
            return {
                cardinality : variable.cardinality,
                baseType    : variable.baseType,
                value       : variable.defaultValue
            };
        }
    };

    return defaultProcessor;
});

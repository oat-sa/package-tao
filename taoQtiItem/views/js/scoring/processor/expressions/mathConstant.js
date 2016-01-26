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
 * The mathConstant expression processor.
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element106461
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([], function(){
    'use strict';

    /**
     * BaseValue expression
     * @type {ExpressionProcesssor}
     * @exports taoQtiItem/scoring/processor/expressions/mathConstant
     */
    var mathConstantProcessor = {

        /**
         * Process the expression
         * @returns {ProcessingValue} the value from the expression
         */
        process : function(){
            var value;
            if(this.expression.attributes.name === 'e'){
                value = Math.E;
            }
            if(this.expression.attributes.name === 'pi'){
                value = Math.PI;
            }
            return {
                cardinality : 'single',
                baseType    : 'float',
                value       : value
            };
        }
    };

    return mathConstantProcessor;
});

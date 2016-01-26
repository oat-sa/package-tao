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
 * Helps to convert QTI types to native JS
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    /**
     * @type {Object.<string,function>}
     */
    var castingMap = {
        float                   : parseFloat,
        string                  : toString,
        integer                 : toInt,
        identifier              : toString,
        pair                    : toPair,
        directedPair            : toDirectedPair,
        duration                : parseFloat,
        boolean                 : toBoolean,
        integerOrVariableRef    : toIntegerOrVariableRef,
        floatOrVariableRef      : toFloatOrVariableRef,
        stringOrVariableRef     : toStringOrVariableRef,
        point                   : toPoint
    };

    /**
     * Return transformation function based on required type
     * @exports taoQtiItem/scoring/processor/expressions/typeCaster
     *
     * @param {string} type
     * @returns {function}
     */
    var typeCaster = function typeCast(type) {
        return castingMap[type] || _.constant;
    };


    /**
     * Wrap parseInt. It can't be used unwrapped as a map callback
     * due to the radix parameter (that receives the index).
     * @private
     * @param {String|Number} value - the value to cast to an int
     * @returns {Number} an integer
     */
    function toInt(value){
        return parseInt(value, 10);
    }

    /**
     * Force creating string object, required for numeric types
     * @private
     * @param {*} value - the value to cast to a string
     * @returns {String}
     */
    function toString(value){
        return value.toString();
    }

    /**
     * Map a representation of a directedPair, ie. "A B" or "[A,B]" to an array ['A', 'B'] and preserve the order
     * @private
     * @param {Array|String} value - the value to cast to a DirectedPair
     * @returns {Array.<String>} an array with 2 strings that represents the pair
     */
    function toDirectedPair(value){
        if(_.isString(value) && value.indexOf(' ') > -1){
            return _.first(value.split(' '), 2);
        }
        return _.first(_.toArray(value), 2);
    }

    /**
     * Same as the DirectedPair but sort the value in order to compare them
     * @private
     * @param {Array|String} value - the value to cast to a  Pair
     * @returns {Array.<String>} an array with 2 strings that represents the pair
     */
    function toPair(value){
        return toDirectedPair(value).sort();
    }


    /**
     * Cat to a boolean
     * @private
     * @param {Number|String|Boolean} value - the value to cast
     * @returns {Boolean}
     */
    function toBoolean(value){
        if(_.isString(value)){
            if(value === 'true' || value === '1'){
                return true;
            }
            if(value === 'false' || value === '0'){
                return false;
            }
        }
        return !!value;
    }

    /**
     * Cast the value by either get the integer of it doesn't refer to a variable that contains an integer
     * @private
     * @param {Number|String} value - the value to cast to an integer
     * @param {Object} [state] - to lookup if the value is a reference to the variable
     * @returns {Number} the integer
     */
    function toIntegerOrVariableRef(value, state){
        if (!_.isNumber(value) && state && _.isObject(state[value]) && typeof state[value].value !== 'undefined') {
            return toInt(state[value].value);
        }
        return toInt(value);
    }

    /**
     * Cast the value by either get the float of it doesn't refer to a variable that contains a float
     * @private
     * @param {Number|String} value - the value to cast to an float
     * @param {Object} [state] - to lookup if the value is a reference to the variable
     * @returns {Number} the float
     */
    function toFloatOrVariableRef(value, state){
        if (!_.isNumber(value) && state && _.isObject(state[value]) && typeof state[value].value !== 'undefined') {
            return parseFloat(state[value].value);
        }
        return parseFloat(value);
    }

    /**
     * Cast the value by either get the string of it doesn't refer to a variable that contains a string
     * @private
     * @param {String} value - the value to cast to an string
     * @returns {String} the string
     */
    function toStringOrVariableRef(value, state){
        if ( state && _.isObject(state[value]) && typeof state[value].value !== 'undefined') {
            return state[value].value;
        }
        return value;
     }

    /**
     * Cast to a point
     * @private
     * @param {String|Array} value - the value to cast to a point
     * @returns {Array<Number>} as [x,y]
     */
    function toPoint(value){
        if(_.isString(value) && value.indexOf(' ') > -1){
            value = _.first(value.split(' '), 2);
        }
        if(_.isArray(value)){
            return _.map(value, toInt);
        }
        return null;
    }

    return typeCaster;
});

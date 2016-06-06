/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */

/**
 * String to array encoder/decoder.
 * It supports the data-encode format of the databinder
 *
 * @author Betrand Chevrier <bertrand@taotesting.com>
 */
define(['lodash'], function(_){
    'use strict';

   return {

       /**
        * Encode the modelValue to a string, using the glue as separator
        * @param {String[]} modelValue - the value to encode
        * @param {String} [glue = ','] - the join glue
        * @returns {String} the encoded string
        */
       encode : function encode (modelValue, glue){
           glue = glue || ',';
           return  _.isArray(modelValue) ? modelValue.join(glue) : modelValue;
       },

       /**
        * Encode the nodeValue to an array, using the glue as separator
        * @param {String} nodeValue - the value to encode
        * @param {String} [glue = ','] - the split glue
        * @returns {String[]} the encoded array
        */
       decode : function decode(nodeValue, glue){
           glue = glue || ',';
           var input = _.isString(nodeValue) ? nodeValue.trim() : nodeValue;
           return _.isEmpty(input) ? [] : input.split(glue);
       }
   };
});

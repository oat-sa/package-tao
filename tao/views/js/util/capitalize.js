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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 *
 * @author dieter <dieter@taotesting.com>
 */
define([
    'lodash'
], function (_) {
    'use strict';


    /**
     * Capitalize a word or a group of words
     *
     * @param {String} input the word to capitalize, will be applied after every ' '
     * @param {Boolean} allWords capitalize all words, similar to PHP's ucWords()
     * @returns {*}
     */
    var capitalize = function capitalize(input, allWords) {
        var ucFirst = function ucFirst(str){
            return str.charAt(0).toUpperCase() + str.substr(1);
        };


        if(!_.isString(input)) {
            return input;
        }

        if(allWords !== false && input.indexOf(' ') > -1) {
            return _.map(input.split(' '), ucFirst).join(' ');
        }
        return ucFirst(input);
    };


    /**
     * @exports capitalize
     */
    return capitalize;
});

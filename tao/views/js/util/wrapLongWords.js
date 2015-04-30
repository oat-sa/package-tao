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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */

/**
 *
 * @author dieter <dieter@taotesting.com>
 */
define([
    'util/regexEscape'
], function (regexEscape) {
    'use strict';

    /**
     * Wrap very long strings after n characters
     *
     * @param str
     * @param threshold number of characters to break after
     * @returns {string}
     */
    function wrapLongWords(str, threshold) {
        // add whitespaces to provoke line breaks before HTML tags
        str = str.replace(/([\w])</g, '$1 <');

        var chunkExp = new RegExp('.{1,' + threshold + '}', 'g'),
            longWords = str.match(new RegExp('[\\S]{' + threshold + ',}', 'g')) || [],
            i = longWords.length,
            cut;

        while(i--) {
            cut = longWords[i].match(chunkExp).join(' ');
            str = str.replace(new RegExp(regexEscape(longWords[i]), 'g'), cut);
        }
        return str;
    }

    return wrapLongWords;
});

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

], function () {
    'use strict';

    var _reQuot = /"/g;
    var _reApos = /'/g;

    /**
     * Encodes an HTML string to be safely displayed without code interpretation
     *
     * @param {String} html
     * @returns {String}
     */
    var encodeHTML = function encodeHTML(html) {
        // @see http://tinyurl.com/ko75kph
        return document.createElement('a').appendChild(
            document.createTextNode(html)).parentNode.innerHTML;
    };

    /**
     * Encodes an HTML string to be safely use inside an attribute
     *
     * @param {String} html
     * @returns {String}
     */
    var encodeAttribute = function encodeAttribute(html) {
        // use replaces chain instead of unified replace with map for performances reasons
        // @see http://jsperf.com/htmlencoderegex/68
        return encodeHTML(html).replace(_reQuot, '&quot;').replace(_reApos, '&apos;');
    };

    return {
        html: encodeHTML,
        attribute: encodeAttribute
    };
});

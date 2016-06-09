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
 * Portable element helper
 */
define(['jquery'], function($){
    'use strict';

    var imgSrcPattern = /(<img[^>]*src=["'])([^"']+)(["'])/ig;

    /**
     * Replace all identified relative media urls by the absolute one.
     * For now only images are supported.
     *
     * @param {String} html - the html to parse
     * @param {Object} the renderer
     * @returns {String} the html without updated URLs
     */
    function fixMarkupMediaSources(html, renderer){
        html = html || '';

        return html.replace(imgSrcPattern, function(substr, $1, $2, $3){
            var resolved = renderer.resolveUrl($2) || $2;
            return $1 + resolved + $3;
        });
    }

    /**
     * @exports taoQtiItem/qtiCommonRenderer/helpers/PortableElement
     */
    return {
        fixMarkupMediaSources : fixMarkupMediaSources,
    };
});

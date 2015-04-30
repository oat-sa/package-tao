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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(function(){
    
    /**
     * Uri helper 
     * @author Bertrand Chevrier <bertrand@taotesting.com>
     * @exports Uri
     */
    var Uri = {
            
        /**
         * Encode an uri, using our proprietary format 
         * @param {string} uri - the uri to encode
         * @returns {string} the encoded uri
         */
        encode : function(uri){
            var encoded = uri;
            if (/^http/.test(uri)) {
                encoded = encoded
                            .replace(/:\/\//g, '_2_')
                            .replace(/#/g, '_3_')
                            .replace(/:/g,'_4_')
                            .replace(/\//g,'_1_')
                            .replace(/\./g,'_0_');
                } 
            return encoded;
        },
        
        /**
         * Decode an uri, from our proprietary format 
         * @param {string} uri- the uri to decode
         * @returns {string} the decoded uri
         */
        decode : function(uri){
            var encoded = uri;
            if (/^http/.test(uri)) {
                encoded = encoded
                            .replace(/_0_/g, '.')
                            .replace(/_1_/g, '/')
                            .replace(/_2_/g, '://')
                            .replace(/_3_/g, '#')
                            .replace(/_4_/g, ':');
                } 
            return encoded;
        }
    };
    return Uri;
});



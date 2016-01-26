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
 * Provides common strategies to resolved assets
 * that may be used by any type of items.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'util/url'
], function(urlUtil){
    'use strict';

    /**
     * Unrelated strategies accessible by there name.
     * Remember to not use the whole object, but each one in an array since the order matters.
     *
     * @exports taoItems/assets/strategies
     */
    var strategies = {

        //the baseUrl concats the baseUrl in data if the url is relative
        baseUrl : {
            name : 'baseUrl',
            handle : function handleBaseUrl(url, data){
                if(typeof data.baseUrl === 'string' && (urlUtil.isRelative(url)) ){

                    //is slashcat we manage slash concact
                    if(data.slashcat === true){
                        return data.baseUrl.replace(/\/$/, '') + '/' + url.directory.replace(/^\.\//, '').replace(/^\//, '')
                            + encodeURIComponent(url.file.replace(/^\.\//, '').replace(/^\//, ''));
                    }

                    return data.baseUrl + url.directory.replace(/^\.?\//, '') + encodeURIComponent(url.file.replace(/^\.?\//, ''));
                }
            }
        },

        //absolute URL are just left intact
        external : {
            name : 'external',
            handle : function handleExternal(url, data){
                if(urlUtil.isAbsolute(url)){
                    return url.toString();
                }
            }
        },

        //the base64 encoded resources are also left intact
        base64 : {
            name : 'base64',
            handle : function handleB64(url){
                if(urlUtil.isBase64(url)){
                    return url.toString();
                }
            }
        },

        //special tao media protocol
        taomedia : {
            name : 'taomedia',
            handle : function handleTaoMedia(url, data){
                //either a baseUrl is given or if empty, taomedia resources are managed as relative resources
                var baseUrl = data.baseUrl || './';
                if( (typeof url === 'object' && url.protocol === 'taomedia') ||
                    (/^taomedia:\/\//.test(url.toString())) ){
                    return baseUrl + url.toString();
                }
            }
        }
    };
    return strategies;
});

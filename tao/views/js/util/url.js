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
 * Utility library that helps you to manipulate URLs.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';


    var parsers = {
        absolute: /^(?:[a-z]+:)?\/\//i,
        base64:   /^data:[^\/]+\/[^;]+(;charset=[\w]+)?;base64,/,
        query:    /(?:^|&)([^&=]*)=?([^&]*)/g,
        url:      /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
    };

    /**
     * The Url util
     * @exports util/url
     */
    var urlUtil = {

        /*
         * The parse method is a adaptation of parseUri from
         * Steven Levithan <stevenlevithan.com> under the MIT License
         */

        /**
         * Parse the given URL and create an object with each URL chunks.
         *
         * BE CAREFUL! This util is different from UrlParser.
         * This one works only from the given string, when UrlParser work from window.location.
         * It means UrlParser will resolve the host of a relative URL using the host of the current window.
         *
         * @param {String} url - the URL to parse
         * @returns {Object} parsedUrl with the properties available in key below and query that contains query string key/values.
         */
        parse : function parse (url) {
            var matches;
            var	keys    = ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","queryString","hash"];
            var i       = keys.length;
            var parsed  = Object.create({
                toString : function(){
                    return this.source;
                }
            });

            parsed.base64 = parsers.base64.test(url);

            if(parsed.base64){
                parsed.source = url;
            } else {

                matches = parsers.url.exec(url);
                while (i--) {
                    parsed[keys[i]] = matches[i] || "";
                }
                parsed.query = {};
                parsed.queryString.replace(parsers.query, function ($0, $1, $2) {
                    if ($1) {
                        parsed.query[$1] = $2;
                    }
                });
            }
            return parsed;
        },

        /**
         * Check whether an URL is absolute
         * @param {String|Object} url - the url to check. It can be a parsed URL (result of {@link util/url#parse})
         * @returns {Boolean|undefined} true if the url is absolute, or undefined if the URL cannot be checked
         */
        isAbsolute : function isAbsolute(url){


            //url from parse
            if(typeof url === 'object' && url.hasOwnProperty('source')){
                return url.source !== url.relative;
            }
            if(typeof url === 'string'){
                return parsers.absolute.test(url);
            }
        },

        /**
         * Check whether an URL is relative
         * @param {String|Object} url - the url to check. It can be a parsed URL (result of {@link util/url#parse})
         * @returns {Boolean|undefined} true if the url is relative, or undefined if the URL cannot be checked
         */
        isRelative : function isRelative(url) {
            var absolute = this.isAbsolute(url);
            if(typeof absolute === 'boolean'){
                return !absolute;
            }
        },

        /**
         * Check whether an URL is encoded in base64
         * @param {String|Object} url - the url to check. It can be a parsed URL (result of {@link util/url#parse})
         * @returns {Boolean|undefined} true if the url is base64, or undefined if the URL cannot be checked
         */
        isBase64 : function isBase64(url){

            if(typeof url === 'object' && url.hasOwnProperty('source')){
                return url.base64;
            }
            if(typeof url === 'string'){
                return parsers.base64.test(url);
            }
        },

        /**
         * Determine whether encoding is required to match XML standards for attributes
         * @param {String} url
         * @returns {String}
         */
        encodeAsXmlAttr : function encodeAsXmlAttr(uri) {
            return (/[<>&']+/.test(uri)) ? encodeURIComponent(uri) : uri;
        },

        /**
         * Build a URL.
         * It does not take case about baseURL.
         *
         * @param {String|Array} path - the URL path. Clean concat if an array (no dupe slashes)
         * @param {Object} [params] - params to add to the URL
         * @returns {String} the URL
         */
        build : function build(path, params){

            var url;

            if(path){
                if(_.isString(path)){
                    url = path;
                }
                if(_.isArray(path)){
                    url = '';
                    _.forEach(path, function(chunk){
                        if(/\/$/.test(url) && /^\//.test(chunk)){
                            url += chunk.substr(1);
                        } else if (url !== '' && !/\/$/.test(url) && !/^\//.test(chunk)){
                            url += '/' +  chunk;
                        } else {
                            url += chunk;
                        }
                    });
                }
                if(_.isPlainObject(params)){
                    if(url.indexOf('?') === -1){
                        url += '?';
                    }
                    url = _.reduce(params, function(acc, value, key){
                        acc += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(value);
                        return acc;
                    }, url);
                }
            }

            return url;
        }
    };

    return urlUtil;
});

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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(['jquery', 'lodash'], function($, _){
    
    
    function hasAccess(windowElt){
        //if we are in the same domain, the parent must place the __knownParent__ variable 
        return !!(window.__knownParent__ && windowElt && windowElt !== window);
    }
    
    /**
     * Use to notify an event from an iframe to it's parent.
     * If you're not in an iframe, nothing will happen.
     * 
     * @author Bertrand Chevrier <bertrand@taotesting.com>
     * @exports iframeNotifier
     */
    var xDomMessaging = {
        
        
        /**
         * Notify the parent window's document
         * @param {String} eventName - the name of the 
         * @param {Array} [args] - event arguments
         */
        parent : function(eventName, args){
            _.defer(function(){     //in next tick for thread safety
                if (hasAccess(window.parent) && window.parent.$) {
                    var _$ = window.parent.$;   //parent window jQuery instance
                    _$(window.parent.document).trigger(eventName, args || []);
                }
            });
        },
        
        /**
         * Notify the top window's document
         * @param {String} eventName - the name of the 
         * @param {Array} [args] - event arguments
         */
        top : function(eventName, args){
            _.defer(function(){     //in next tick for thread safety
                if (hasAccess(window.top) && window.top.$) {
                    var _$ = window.top.$;   //parent window jQuery instance
                    _$(window.top.document).trigger(eventName, args || []);
                }
            });
        }
    };
    
    return xDomMessaging;
});
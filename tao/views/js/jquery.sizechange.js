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
/**
 * ORGINAL VERSION:
 * http://meetselva.github.io/attrchange/
 * Copyright (C) 2013 Selvakumar Arumugam
 * You may use attrchange plugin under the terms of the MIT Licese.
 * https://github.com/meetselva/attrchange/blob/master/MIT-License.txt
 *
 * MODIFIED VERSION:
 * @author Bertrand Chevrier <bertrand@taotesting.com> for OAT SA
 * - Code refactoring to fit AMD modules
 * - Specific implementation of the original attrchange plugin to detect
 */
define(['jquery', 'lodash'], function($, _){
    'use strict';

    /**
     * Check whether DOM3 Events / MutationObserver are supported
     * @todo use Modernizr.hasEvent once integrated
     * @returns {Boolean} true if supported
     */
    function isDOM3EventSupported(){
         return window.MutationObserver || window.WebKitMutationObserver || false;
    }

    /**
     * Check whether DOM2 Events (based on DOMAttrModified) are supported
     * @todo use Modernizr.hasEvent once integrated
     * @returns {Boolean} true if supported
     */
   function isDOM2EventSupported() {
        var p = document.createElement('p');
        var flag = false;

        if (p.addEventListener){
            p.addEventListener('DOMAttrModified', function() {
                flag = true;
            }, false);
        } else if (p.attachEvent) {
            p.attachEvent('onDOMAttrModified', function() {
                flag = true;
            });
        } else {
            return false;
        }

        p.setAttribute('id', '__dummy_domtest_target');

        return flag;
   }

   /**
    * Register a jquery plugin that helps you to execute the given callback when a resize MAY happen.
    *
    * !!! The callback MUST NOT modify in any way the element it is observing, or you'll fall into an infinite loop !!!
    *
    * @example $iframe.contents().find('body').sizeChange(function(){ $iframe.height($iframe.contents().height()); });
    * @param {Function} cb - a callback function when an event that MAY resize is triggered
    * @returns {jQueryElement} for chaining
    */
   $.fn.sizeChange = function(cb) {
        var $this = this;
        var running = false;

        cb = cb || $.noop();
        if($this.length === 0){
            return $this;
        }

        var execCb = _.throttle(function execCb(done){
            cb();
            _.delay(done, 1);
            done();
            //if new images are inserted, their load can update size without trigerring and mutation
            $this.find('img').one('load', function(){
                cb();
            });
        }, 10);


        if (isDOM3EventSupported()) { //DOM3,  Modern Browsers
            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
            var mutationOptions = {
                    childList : this[0].nodeName !== 'IFRAME',
                    subtree: true,
                    attributes: true,
                    attributeFilter : ['style', 'width', 'height']
            };

            var observer = new MutationObserver(function(mutations) {
                for(var i in mutations) {
                    if(mutations[i].addedNodes !== null || mutations[i].attributeName !== null){
                        stop();
                        execCb(start);
                        return;
                    }
                }
            });

            var start = function start(){
                $this.each(function() {
                    observer.observe(this, mutationOptions);
                });
            };
            var stop = function stop(){
                observer.disconnect();
            };

            start();

        } else  if (isDOM2EventSupported()) { //DOM2, Opera
            var runs = function runs(){
                running = false;
            };
            $this.on('DOMAttrModified', function(event) {
                if(event.attrName === 'style' && !running){
                    running = true;
                    execCb(runs);
                }
            });
            $this.on('DOMNodeRemoved DOMNodeInserted DOMNodeInsertedIntoDocument DOMNodeRemovedFromDocument', function(event){
                if(event.target.nodeType === 1 && !running){
                    running  = true;
                    execCb(runs);
                }
            });
        } else {
            throw new Error('Event listening not supported');
        }

        return this;
    };

});

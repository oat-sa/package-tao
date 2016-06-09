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
 */


/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
(function(window){
    'use strict';
    
    //the configuration is retrieved from the query parameters
    var parameters = extractParams();
    if(parameters.clientConfigUrl === undefined){
       throw new Error('The Client Configuration URL has not been set as query parameter!');
    }
    var clientConfigUrl = parameters.clientConfigUrl;
    
    //the context is set into the template and bound to the tao namespace in global scope 
    var runnerContext = window.tao.qtiRunnerContext;

    //once the page is loaded
    onLoad(function(){

        requirejs.config({
           waitSeconds : parameters.timeout || 30 
        });
        
        //we load the configuration
        require([clientConfigUrl], function(){
            
            //and start the QTI bootstrap
            require(['css!taoCss/tao-main-style', 'taoQtiItem/runtime/qtiBootstrap'], function(css, qtiBootstrap){
    
                qtiBootstrap(runnerContext);
            });
        });
    });
    
    
    //as no libs are loaded there, we have to define some cross browser helpers
    
    /**
     * Cross Browser onload event handler as jquery is not yet loaded
     * @param {type} cb
     * @returns {undefined}
     */
    function onLoad(cb) {
        if(document.readyState === 'complete'){
            return cb();
        } 
        
        if (window.addEventListener) {
            window.addEventListener('load', cb, false);
        }
        else if (window.attachEvent) {
            window.attachEvent('onload', cb);
        }
    }
    
   /**
    * Old style param extraction of the current location
    * @returns {Object} params as key/value pairs
    */
    function extractParams(){
       var params = {};
       var queryString = window.location.search;
       if(queryString.indexOf('?') > -1){
           var pairs = queryString.replace(/^( )?\?/, '').split('&');
           for(var i = 0; i < pairs.length; i++){
               var param = pairs[i].split('=');
               if(param.length === 2){
                   var key = decodeURIComponent(param[0]);
                   var value = decodeURIComponent(param[1]);
                   if(params[key] !== undefined){
                       if(params[key].length){
                           params[key].push(value);
                       } else {
                           params[key] = [params[key], value];
                       }
                   } else {
                       params[key] = value;
                   }
               }
           }
       }
       return params;
    }
    
}(this));

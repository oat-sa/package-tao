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
define(['jquery', 'urlParser'], function($, UrlParser){
    'use strict';

    /**
     * @constructor
     */
    function ServiceApi(baseUrl, parameters, serviceCallId, stateStorage, userService, paused){
        this.baseUrl = baseUrl;
        this.parameters = parameters;
        this.connected = false;

        this.serviceCallId = serviceCallId;
        this.state = stateStorage;
        this.userService = userService;
        this.hasBeenPaused = paused;
    }

    ServiceApi.SIG_SUCCESS = 0;
    ServiceApi.SIG_ERROR = 1;

    ServiceApi.prototype.loadInto = function(frame, connected){
        var self = this;
        var $frame = $(frame);
        var callUrl = this.getCallUrl();
        var isCORSAllowed = new UrlParser(callUrl).checkCORS();

        $frame.on('load', function(e){
             //if we are  in the same domain, we add a variable
             //to the frame window, so the frame knows it can communicate
             //with the parent
            $(document).on('serviceready', function(){
                self.connect(frame, function(){
                    $(document).off('serviceready');
                    if(typeof connected === 'function'){
                        connected();
                    }
                });
            });
             if(isCORSAllowed === true){
                 frame.contentWindow.__knownParent__ = true;
             }
         });

        $frame.attr('src', callUrl);
    };

    ServiceApi.prototype.connect = function(frame, connected){
        if(this.connected === false && frame.contentWindow){
            //frame.contentWindow.serviceApi = this;
            if (typeof(frame.contentWindow.onServiceApiReady) === "function") {
                frame.contentWindow.onServiceApiReady(this);
                this.connected = true;
                if(typeof connected === 'function'){
                    connected();
                }
            }
        }
    };

    /**
     * Get the service call URL
     * @returns {String} the URI
     */
    ServiceApi.prototype.getCallUrl = function(){
        var params = this.parameters || {};
        params.serviceCallId = this.serviceCallId;
        return this.baseUrl + '?' + $.param(params);
    };

    ServiceApi.prototype.getUserPropertyValues = function(property, callback){
    	this.userService.get(property, callback);
    };

    //Context
    ServiceApi.prototype.getServiceCallId = function(){
        return this.serviceCallId;
    };

    //Context
    ServiceApi.prototype.getState = function(){
        return this.state.get();
    };

    ServiceApi.prototype.setState = function(state, callback){
        return this.state.set(state, callback);
    };

    //Context
    ServiceApi.prototype.getHasBeenPaused = function(){
        return this.hasBeenPaused;
    };

    ServiceApi.prototype.setHasBeenPaused = function(paused){
        this.hasBeenPaused = paused;
    };

    // Variables
    ServiceApi.prototype.getParameter = function(identifier){
        if (typeof(this.parameters[identifier]) !== "undefined") {
            return this.parameters[identifier];
        } else {
            return null;
        }
    };

    /**
     * Sets a callback on the finish action
     * @param {Function} callback
     * @returns {ServiceApi}
     */
    ServiceApi.prototype.onFinish = function(callback) {
        this.onFinishCallback = callback;
        return this;
    };

    /**
     * Sets a callback on the kill action
     * @param {Function} callback
     * @returns {ServiceApi}
     */
    ServiceApi.prototype.onKill = function(callback) {
        this.onKillCallback = callback;
        return this;
    };

    /**
     * Sets a callback on the exit action
     * @param {Function} callback
     * @returns {ServiceApi}
     */
    ServiceApi.prototype.onExit = function(callback) {
        this.onExitCallback = callback;
        return this;
    };

    /**
     * @param {Function} callback - Function called if no dedicated callback is found
     */
    ServiceApi.prototype.kill = function(callback) {
    	if (typeof this.onKillCallback === 'function') {
    		this.onKillCallback(callback);
    	} else {
    		callback(0);
    	}
    };

    /**
     * Exits the flow
     * @param {Function} callback - Function called if no dedicated callback is found
     */
    ServiceApi.prototype.exit = function(callback) {
    	if (typeof this.onExitCallback === 'function') {
    		this.onExitCallback(callback);
    	} else {
    		callback(0);
    	}
    };

    /**
     * Finishes the flow
     * @param valueArray - return parameters of the service.
     */
    ServiceApi.prototype.finish = function(valueArray) {
        //return execution to service caller
        if (typeof this.onFinishCallback === 'function') {
                this.onFinishCallback(valueArray);
        }
    };

    return ServiceApi;

});

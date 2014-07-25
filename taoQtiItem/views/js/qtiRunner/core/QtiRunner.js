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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
/**
 * A class to regroup QTI functionalities
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery {@link http://www.jquery.com}
 */
define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Loader', 
    'taoQtiItem/qtiItem/helper/pci', 
    'taoQtiItem/qtiItem/core/feedbacks/ModalFeedback'
], function($, _, ItemLoader, pci, ModalFeedback){

    var QtiRunner = function(){
        this.item = null;
        this.rpEngine = null;
        this.renderer = null;
        this.loader = null;
        this.itemApi = undefined;
    };
    
    QtiRunner.prototype.updateItemApi = function() {
        var responses = this.getResponses();
        
        // Transform responses into state variables.
        for (var key in responses) {
        	
        	var value = responses[key];
        	
        	// This is where we set variables that will be collected and stored
        	// as the Item State. We do not want to store large files into
        	// the state, and force the client to download these files
        	// all over again. We then transform them as a place holder, that will
        	// simply indicate that a file composes the state.
        	
        	if (typeof(value.base) != 'undefined') {
                
                for (var property in value.base) {

                    if (property === 'file') {
                        
                        var file = value.base.file;
                        // QTI File found! Replace it with an appropriate placeholder.
                        // The data is base64('qti_file_datatype_placeholder_data')
                        value = {"base" : {"file" : {"name" : file.name, "mime" : 'qti+application/octet-stream', "data" : "cXRpX2ZpbGVfZGF0YXR5cGVfcGxhY2Vob2xkZXJfZGF0YQ=="}}};
                    }
                }
            }
        	
            this.itemApi.setVariable(key, value);
        }
        
        // Save the responses that will be used for response processing.
        this.itemApi.saveResponses(responses);
        this.itemApi.resultApi.setQtiRunner(this);
    }

    QtiRunner.prototype.setItemApi = function(itemApi){
        this.itemApi = itemApi;
        var that = this;
        var oldStateVariables = JSON.stringify(itemApi.stateVariables);
        
        itemApi.onKill(function(killCallback) {
            // If the responses did not change,
            // just close gracefully.
            
            // Collect new responses and update item API.
            that.updateItemApi();
            var newStateVariables = JSON.stringify(itemApi.stateVariables);
            
            // Store the results.
            if (oldStateVariables != newStateVariables) {
                itemApi.submit(function() {
                    // Send successful signal.
                    killCallback(0);
                });
            }
            else {
                killCallback(0);
            }
        });
    };

    QtiRunner.prototype.setRenderer = function(renderer){
        if(renderer.isRenderer){
            this.renderer = renderer;
        }else{
            throw 'invalid renderer';
        }
    };

    QtiRunner.prototype.getLoader = function(){
        if(!this.loader){
            this.loader = new ItemLoader();
        }
        return this.loader;
    };

    QtiRunner.prototype.loadItemData = function(data, callback){
        var _this = this;
        this.getLoader().loadItemData(data, function(item){
            _this.item = item;
            callback(_this.item);
        });
    };

    QtiRunner.prototype.loadElements = function(elements, callback){
        if(this.getLoader().item){
            this.getLoader().loadElements(elements, callback);
        }else{
            throw 'QtiRunner : cannot load elements in empty item';
        }
    };

    QtiRunner.prototype.renderItem = function(data, callback){

        var _this = this;
        var render = function(){
            if(!_this.item){
                throw 'cannot render item: empty item';
            }
            if(_this.renderer){

                _this.renderer.load(function(){

                    _this.item.setRenderer(_this.renderer);
                    _this.item.render({}, $('#qti_item'));
                    _this.item.postRender();
                    _this.initInteractionsResponse();

                    if (typeof callback != 'undefined') {
                        callback();
                    }
                    
                }, _this.getLoader().getLoadedClasses());

            }else{
                throw 'cannot render item: no rendered set';
            }
        };

        if(typeof data === 'object'){
            this.loadItemData(data, render);
        }else{
            render();
        }
    };

    QtiRunner.prototype.initInteractionsResponse = function(){
        if(this.item){
            var interactions = this.item.getInteractions();
            for(var i in interactions){
                var interaction = interactions[i];
                var responseId = interaction.attr('responseIdentifier');
                this.itemApi.getVariable(responseId, function(values){
                    if(values){
                        interaction.setResponse(values);
                    }
                });
            }
        }
    };

    QtiRunner.prototype.validate = function(){
        this.updateItemApi();
        this.itemApi.finish();
    };

    QtiRunner.prototype.getResponses = function() {

        var responses = {};
        var interactions = this.item.getInteractions();
        
        for (var serial in interactions) {
            
            var interaction = interactions[serial];
            var response = interaction.getResponse();
            
            responses[interaction.attr('responseIdentifier')] = response;
        }
        
        return responses;
    };

    QtiRunner.prototype.setResponseProcessing = function(callback){
        this.rpEngine = callback;
    };
    
    QtiRunner.prototype.showFeedbacks = function(itemSession, callback, onShowCallback){

        //currently only modal feedbacks are available
        var _this = this,
            feedbacksToBeDisplayed = [];
        
        //find which modal feedbacks should be displayed according to the current item session:
        _.each(this.item.modalFeedbacks, function(feedback){
            var outcomeIdentifier = feedback.attr('outcomeIdentifier');
            if(itemSession[outcomeIdentifier]){
                var feedbackIds = pci.getRawValues(itemSession[outcomeIdentifier]);
                if(_.indexOf(feedbackIds, feedback.id()) >= 0){
                    feedbacksToBeDisplayed.push(feedback);
                }
            }
        });
        
        //record the number of feedbacks to be displayed:
        var count = feedbacksToBeDisplayed.length;
        
        if (count > 0 && typeof onShowCallback != 'undefined') {
            onShowCallback();
        }
        
        //show in reverse order
        var lastFeedback = feedbacksToBeDisplayed.shift();//the last feedback to be shown is the first defined in the item
            _.eachRight(feedbacksToBeDisplayed, function(feedback){
            _this.showModalFeedback(feedback);
        });
        
        //add callback to the last shown modal feedback
        this.showModalFeedback(lastFeedback, callback);
        
        return count;
    };

    QtiRunner.prototype.showModalFeedback = function(modalFeedback, callback){

        if(modalFeedback instanceof ModalFeedback){
            $('#modalFeedbacks').append(modalFeedback.render());
            modalFeedback.postRender({
                callback : callback
            });
        }
    };

    return QtiRunner;
});

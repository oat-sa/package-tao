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
    'context',
    'module',
    'core/promise',
    'iframeNotifier',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiRunner/modalFeedback/inlineRenderer',
    'taoQtiItem/qtiRunner/modalFeedback/modalRenderer'
], function($, _, context, module, Promise, iframeNotifier, ItemLoader, modalFeedbackInline, modalFeedbackModal){
    'use strict';

    var timeout = (context.timeout > 0 ? context.timeout + 1 : 30) * 1000;

    var QtiRunner = function(){
        this.item = null;
        this.rpEngine = null;
        this.renderer = null;
        this.loader = null;
        this.itemApi = undefined;
    };

    QtiRunner.prototype.updateItemApi = function() {
        var responses = this.getResponses();
        var states = this.getStates();
        var variables = [];
        // Transform responses into state variables.
        for (var key in states) {

        	var value = states[key];
        	// This is where we set variables that will be collected and stored
        	// as the Item State. We do not want to store large files into
        	// the state, and force the client to download these files
        	// all over again. We then transform them as a place holder, that will
        	// simply indicate that a file composes the state.

        	if (value.response && typeof(value.response.base) !== 'undefined') {

                for (var property in value.response.base) {

                    if (property === 'file') {

                        var file = value.response.base.file;
                        // QTI File found! Replace it with an appropriate placeholder.
                        // The data is base64('qti_file_datatype_placeholder_data')
                        value.response = {"base" : {"file" : {"name" : file.name, "mime" : 'qti+application/octet-stream', "data" : "cXRpX2ZpbGVfZGF0YXR5cGVfcGxhY2Vob2xkZXJfZGF0YQ=="}}};
                    }
                }
            }
            
            variables[key] = value;
        }
        
        //set all variables at once
        this.itemApi.setVariables(variables);

        // Save the responses that will be used for response processing.
        this.itemApi.saveResponses(responses);
        this.itemApi.resultApi.setQtiRunner(this);
    };

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
            if (oldStateVariables !== newStateVariables || itemApi.serviceApi.getHasBeenPaused()) {
                itemApi.submit(function() {
                    // Send successful signal.
                    itemApi.serviceApi.setHasBeenPaused(false);
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
        var self = this;
        this.getLoader().loadItemData(data, function(item){
            self.item = item;
            callback(self.item);
        });
    };

    QtiRunner.prototype.loadElements = function(elements, callback){
        if(this.getLoader().item){
            this.getLoader().loadElements(elements, callback);
        }else{
            throw 'QtiRunner : cannot load elements in empty item';
        }
    };

    QtiRunner.prototype.renderItem = function(data, done){

        var self = this;

        done = _.isFunction(done) ? done : _.noop;

        var render = function(){
            if(!self.item){
                throw 'cannot render item: empty item';
            }
            if(self.renderer){

                self.renderer.load(function(){

                    self.item.setRenderer(self.renderer);
                    self.item.render({}, $('#qti_item'));

                    // Race between postRendering and timeout
                    // postRendering waits for everything to be resolved or one reject
                    Promise.race([
                        Promise.all(self.item.postRender()),
                        new Promise(function(resolve, reject){
                            _.delay(reject, timeout, new Error('Post rendering ran out of time.'));
                        })
                    ])
                    .then(function(){
                        self.item.getContainer().on('responseChange', function(e, data){
                            if(data.interaction && data.interaction.attr('responseIdentifier') && data.response){
                                iframeNotifier.parent('responsechange', [data.interaction.attr('responseIdentifier'), data.response]);
                            }
                        });

                        self.initInteractionsResponse();
                        self.listenForThemeChange();
                        done();

                    })
                    .catch(function(err){

                        //in case of postRendering issue, we are also done
                        done();

                        throw new Error('Error in post rendering : ' + err);
                    });

                }, self.getLoader().getLoadedClasses());

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
        var self = this;
        if(self.item){
            var interactions = self.item.getInteractions();
            for(var i in interactions){
                var interaction = interactions[i];
                var responseId = interaction.attr('responseIdentifier');
                self.itemApi.getVariable(responseId, function(values){
                    if(values){
                        interaction.setState(values);
                        iframeNotifier.parent('stateready', [responseId, values]);
                    }
                    else{
                        var states = self.getStates();
                        if(_.indexOf(states, responseId)){
                            self.itemApi.setVariable(responseId, states[responseId]);
                            interaction.setState(states[responseId]);
                            iframeNotifier.parent('stateready', [responseId, states[responseId]]);
                        }
                    }
                });
            }
        }
    };

    /**
     * If an event 'themechange' bubbles to "#qti_item" node
     * then we tell the renderer to switch the theme.
     */
    QtiRunner.prototype.listenForThemeChange = function listenForThemeChange(){
        var self = this;
        var $container = this.renderer.getContainer(this.item);
        if(!$container.length){
            $container = $('.qti-item');
        }
        $container
            .off('themechange')
            .on('themechange', function(e, themeName){
                var themeLoader = self.renderer.getThemeLoader();
                themeName = themeName || e.originalEvent.detail;
                if(themeLoader){
                    themeLoader.change(themeName);
                }
            });
    };

    QtiRunner.prototype.validate = function(){
        this.updateItemApi();
        this.itemApi.finish();
    };

    QtiRunner.prototype.getResponses = function() {

        var responses = {};
        var interactions = this.item.getInteractions();

        _.forEach(interactions, function(interaction){
            var response = {};
            try {
                response = interaction.getResponse();
            } catch(e){
                console.error(e);
            }
            responses[interaction.attr('responseIdentifier')] = response;
        });

        return responses;
    };

    QtiRunner.prototype.getStates = function() {

        var states = {};
        var interactions = this.item.getInteractions();

        _.forEach(interactions, function(interaction){
            var state = {};
            try {
                state = interaction.getState();
            } catch(e){
                console.error(e);
            }
            states[interaction.attr('responseIdentifier')] = state;
        });

        return states;
    };

    QtiRunner.prototype.setResponseProcessing = function(callback){
        this.rpEngine = callback;
    };

    QtiRunner.prototype.showFeedbacks = function(itemSession, callback, onShowCallback){
        
        var inlineDisplay = !!module.config().inlineModalFeedback;
        
        //currently only modal feedbacks are available
        if(inlineDisplay){
            return modalFeedbackInline.showFeedbacks(this.item, this.getLoader(), this.renderer, itemSession, callback, onShowCallback);
        }else{
            return modalFeedbackModal.showFeedbacks(this.item, this.getLoader(), this.renderer, itemSession, callback, onShowCallback);
        }
    };
    
    return QtiRunner;
});

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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'context',
    'core/promise',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer',
    'taoQtiItem/runner/provider/manager/picManager',
    'taoItems/assets/manager'
], function($, _, context, Promise, QtiLoader, Element, QtiRenderer, picManager, assetManagerFactory){
    'use strict';

    var timeout = (context.timeout > 0 ? context.timeout + 1 : 30) * 1000;

    /**
     * @exports taoQtiItem/runner/provider/qti
     */
    var qtiItemRuntimeProvider = {

        init : function(itemData, done){
            var self = this;

            var rendererOptions = {
                assetManager : this.assetManager
            };
            if(this.options.themes){
                rendererOptions.themes = this.options.themes;
            }

            this._renderer = new QtiRenderer(rendererOptions);
            this._loader   = new QtiLoader();

            this._loader.loadItemData(itemData, function(item){
                if(!item){
                    return self.trigger('error', 'Unable to load item from the given data.');
                }

                self._item = item;
                self._renderer.load(function(){
                    self._item.setRenderer(this);

                    done();
                }, this.getLoadedClasses());
            });

            /**
             * This "special" event has to be called on the IR to show a modal feedback
             * @event itemRunner#feedback
             * @param {Objtec} feedbackData - the feedback item data, loaded only on demand (hidden to the user)
             * @param {Object} itemSession - the itemSession to decide which feedback to display
             * @param {Function} done - what to do once the modal feedback is closed
             */
            this.on('feedback', function(feedbackData, itemSession, done){
                //modal feedback data is
                if(feedbackData && itemSession){
                    self._loader.loadElements(feedbackData, function(item){
                        self._renderer.load(function(){
                            var queue = [];

                            _.forEach(item.modalFeedbacks, function(feedback){

                                var outcomeIdentifier = feedback.attr('outcomeIdentifier');
                                if(itemSession[outcomeIdentifier].base.identifier === feedback.id()){
                                    queue.push(new Promise(function(resolve){
                                        var $feedbackContent = $(feedback.render());

                                        //FIXME the IR should not be responsible of the modal rendering, i
                                        //the container selection should be part of the renderer
                                        $('#modalFeedbacks').append($feedbackContent);
                                        feedback.postRender({
                                            callback : function(){
                                                $feedbackContent.remove();
                                                resolve();
                                            }
                                        });
                                    }));
                                }
                            });

                            //execute the done callback once all modals are closed
                            Promise.all(queue).then(done);

                        }, this.getLoadedClasses());
                    });
                } else {
                    done();
                }
            });
        },

        render : function(elt, done){
            var self = this;
            var current = 0;

            if(this._item){

                try {
                    //render item html
                    elt.innerHTML = this._item.render({});
                } catch(e){
                    self.trigger('error', 'Error in template rendering : ' +  e.message);
                }
                try {
                    // Race between postRendering and timeout
                    // postRendering waits for everything to be resolved or one reject
                    Promise.race([
                        Promise.all(this._item.postRender()),
                        new Promise(function(resolve, reject){
                            _.delay(reject, timeout, new Error('Post rendering ran out of time.'));
                        })
                    ])
                    .then(function(){
                        $(elt)
                            .off('responseChange')
                            .on('responseChange', function(){
                                self.trigger('statechange', self.getState());
                                self.trigger('responsechange', self.getResponses());
                            })
                            .off('endattempt')
                            .on('endattempt', function(e, responseIdentifier){
                                self.trigger('endattempt', responseIdentifier || e.originalEvent.detail);
                            })
                            .off('themechange')
                            .on('themechange', function(e, themeName){
                                var themeLoader = self._renderer.getThemeLoader();
                                themeName = themeName || e.originalEvent.detail;
                                if(themeLoader){
                                    themeLoader.change(themeName);
                                }
                            });

                        /**
                         * Lists the PIC provided by this item.
                         * @event qti#listpic
                         */
                        self.trigger('listpic', picManager.collection(self._item));

                        done();

                    }).catch(function(err){
                        self.trigger('error', 'Error in post rendering : ' +  err.message);
                    });
                } catch(err){
                    self.trigger('error', 'Error in post rendering : ' + err.message);
                }
            }
        },

        /**
         * Clean up stuffs
         */
        clear : function(elt, done){
            if(this._item){

                _.invoke(this._item.getInteractions(), 'clear');
                this._item.clear();

                $(elt).off('responseChange')
                      .off('endattempt')
                      .off('themechange')
                      .off('feedback')
                      .empty();

                if(this._renderer){
                    this._renderer.unload();
                }
            }
            done();
        },

        /**
         * Get state implementation.
         * @returns {Object} that represents the state
         */
        getState : function getState(){
            var state = {};
            if(this._item){

                //get the state from interactions
                _.forEach(this._item.getInteractions(), function(interaction){
                    state[interaction.attr('responseIdentifier')] = interaction.getState();
                });

                //get the state from infoControls
                _.forEach(this._item.getElements(), function(element) {
                    if (Element.isA(element, 'infoControl') && element.attr('id')) {
                        state.pic = state.pic || {};
                        state.pic[element.attr('id')] = element.getState();
                    }
                });
            }
            return state;
        },

        /**
         * Set state implementation.
         * @param {Object} state - the state
         */
        setState : function setState(state){
            if(this._item && state){

                //set interaction state
                _.forEach(this._item.getInteractions(), function(interaction){
                    var id = interaction.attr('responseIdentifier');
                    if(id && state[id]){
                        interaction.setState(state[id]);
                    }
                });

                //set info control state
                if(state.pic){
                    _.forEach(this._item.getElements(), function(element) {
                        if (Element.isA(element, 'infoControl') && state.pic[element.attr('id')]) {
                            element.setState(state.pic[element.attr('id')]);
                        }
                    });
                }
            }
        },

        getResponses : function(){
            var responses = {};
            if(this._item){
                _.reduce(this._item.getInteractions(), function(res, interaction){
                    responses[interaction.attr('responseIdentifier')] = interaction.getResponse();
                    return responses;
                }, responses);
            }
            return responses;
        }
    };

    return qtiItemRuntimeProvider;
});

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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * Test Runner provider for QTI Tests.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'core/promise',
    'taoTests/runner/areaBroker',
    'taoTests/runner/proxy',
    'taoTests/runner/probeOverseer',
    'taoQtiTest/runner/helpers/map',
    'taoQtiItem/runner/qtiItemRunner',
    'taoItems/assets/manager',
    'taoItems/assets/strategies',
    'tpl!taoQtiTest/runner/provider/layout'
], function($, _, __, Promise, areaBroker, proxyFactory, probeOverseer, mapHelper, qtiItemRunner, assetManagerFactory, assetStrategies, layoutTpl) {
    'use strict';

    //the asset strategies
    var assetManager = assetManagerFactory([
        assetStrategies.external,
        assetStrategies.base64,
        assetStrategies.baseUrl
    ], { baseUrl: '' });

    /**
     * A Test runner provider to be registered againt the runner
     */
    var qtiProvider = {

        //provider name
        name : 'qti',

        /**
         * Initialize and load the area broker with a correct mapping
         * @returns {areaBroker}
         */
        loadAreaBroker : function loadAreaBroker(){
            var $layout = $(layoutTpl());
            return areaBroker($layout, {
                content:    $('#qti-content', $layout),
                toolbox:    $('.tools-box-list', $layout),
                navigation: $('.navi-box-list', $layout),
                control:    $('.top-action-bar .control-box', $layout),
                panel:      $('.test-sidebar-left', $layout),
                header:     $('.title-box', $layout)
            });
        },

        /**
         * Initialize and load the test runner proxy
         * @returns {proxy}
         */
        loadProxy : function loadProxy(){

            var config = this.getConfig();

            var proxyConfig = _.pick(config, [
                'testDefinition',
                'testCompilation',
                'serviceCallId',
                'serviceController',
                'serviceExtension'
            ]);
            return proxyFactory('qtiServiceProxy', proxyConfig);
        },

        /**
         * Initialize and load the probe overseer
         * @returns {probeOverseer}
         */
        loadProbeOverseer : function loadProbeOverseer(){
            var config = this.getConfig();

            //the test run needs to be identified uniquely
            var identifier = config.serviceCallId || 'test-' + Date.now();
            return probeOverseer(identifier, this);
        },

        /**
         * Initialization of the provider, called during test runner init phase.
         *
         * We install behaviors during this phase (ie. even handlers)
         * and we call proxy.init.
         *
         * @this {runner} the runner context, not the provider
         * @returns {Promise} to chain proxy.init
         */
        init : function init(){
            var self = this;

            /**
             * Compute the next item for the given action
             * @param {String} action - item action like move/next, skip, etc.
             * @param {Object} [params] - the item action additionnal params
             */
            var computeNext = function computeNext(action, params){

                var context = self.getTestContext();

                //to be sure load start after unload...
                //we add an intermediate ns event on unload
                self.on('unloaditem.' + action, function(){
                    self.off('.'+action);

                    self.getProxy()
                        .callItemAction(context.itemUri, action, params)
                        .then(function(results){

                            self.setTestContext(results.testContext);

                            if (results.testMap) {
                                self.setTestMap(results.testMap);
                            } else {
                                updateStats();
                            }

                            load();
                        })
                        .catch(function(err){
                            self.trigger('error', err);
                        });
                })
                .unloadItem(context.itemUri);
            };

            /**
             * Load the next action: load the current item or call finish based the test state
             */
            var load = function load(){

                var context = self.getTestContext();
                var states = self.getTestData().states;
                if(context.state <= states.interacting){
                    self.loadItem(context.itemUri);
                } else if (context.state === states.closed){
                    self.finish();
                }
            };

            /**
             * Store the item state and responses, if needed
             * @returns {Promise} - resolve with a boolean at true if the response is submitd
             */
            var submit = function submit(){

                var context = self.getTestContext();
                var states = self.getTestData().itemStates;
                var itemRunner = self.itemRunner;
                var itemAttemptId = context.itemIdentifier + '#' + context.attempt;
                var params = {};

                var performSubmit = function performSubmit(){
                    //we submit the responses
                    return self.getProxy().submitItem(context.itemUri, itemRunner.getState(), itemRunner.getResponses(), params)
                        .then(function(result){
                            return new Promise(function(resolve){
                                //if the submit results contains modal feedback we ask (gently) the IR to display them
                                if(result.success) {
                                    if (result.itemSession) {
                                        context.itemAnswered = result.itemSession.itemAnswered;
                                    }

                                    if(result.displayFeedbacks === true && itemRunner){
                                        return itemRunner.trigger('feedback', result.feedbacks, result.itemSession, resolve);
                                    }
                                }
                                return resolve();
                            });
                        });
                };

                if(context.itemSessionState >= states.closed) {
                    return Promise.resolve(false);
                }

                //if the duration plugin is installed,
                //we load the duration using an event
                if(self.getPlugin('duration')){
                    return new Promise(function(resolve, reject){
                        //trigger the get event to retrieve the duration for that attempt
                        self.getPlugin('duration').trigger('get', itemAttemptId, function receiver(p){
                            p.then(function(duration){
                                params.itemDuration = 0;
                                if(_.isNumber(duration) && duration > 0){
                                    params.itemDuration = duration;
                                }
                                return resolve(performSubmit());
                            }).catch(reject);
                        });
                    });
                }

                return performSubmit();
            };

            /**
             * Update the stats on the TestMap
             */
            var updateStats = function updateStats(){

                var context = self.getTestContext();
                var testMap = self.getTestMap();
                var states = self.getTestData().states;
                var item = mapHelper.getItemAt(testMap, context.itemPosition);

                if(context.state !== states.interacting){
                    return;
                }

                //flag as viewed, always
                item.viewed = true;

                //flag as answered only if a response has been set
                if (undefined !== context.itemAnswered) {
                    item.answered = context.itemAnswered;
                }

                //update the map stats, then reassign the map
                self.setTestMap(mapHelper.updateItemStats(testMap, context.itemPosition));
            };


            /*
             * Install behavior on events
             */
            this.on('ready', function(){
                //load the 1st item
                load();
            })
            .on('move', function(direction, scope, position){

                //ask to move:
                // 1. try to submit state and responses
                // 2. update stats on the map
                // 3. compute the next item to load

                var computeNextMove = _.partial(computeNext, 'move', {
                        direction : direction,
                        scope     : scope || 'item',
                        ref       : position
                    });


                this.trigger('disablenav disabletools');

                submit()
                 .then(updateStats)
                 .then(computeNextMove)
                 .catch(function(err){
                    self.trigger('error', err);
                 });
            })
            .on('skip', function(scope){

                this.trigger('disablenav disabletools');

                computeNext('skip', {
                    scope     : scope || 'item'
                });

            })
            .on('exit', function(why){
                var context = self.getTestContext();
                self.disableItem(context.itemUri);

                submit()
                    .then(function() {
                        return self.getProxy()
                            .callTestAction('exitTest', {reason: why})
                            .then(function(){
                                return self.finish();
                            });
                    })
                    .catch(function(err){
                        self.trigger('error', err);
                    });
            })
            .on('timeout', function(scope, ref){

                var context = self.getTestContext();

                context.isTimeout = true;

                self.disableItem(context.itemUri);

                submit()
                    .then(updateStats)
                    .then(function() {
                        self.trigger('alert', __('Time limit reached, this part of the test has ended.'), function() {
                            computeNext('timeout', {
                                scope: scope,
                                ref: ref
                            });
                        });
                    })
                    .catch(function(err){
                        self.trigger('error', err);
                    });
            })
            .on('renderitem', function(itemRef){

                var context = this.getTestContext();
                var states = this.getTestData().itemStates;
                var warning = false;

                this.trigger('enablenav enabletools');


                //The item is rendered but in a state that prevents us from interacting
                if (context.isTimeout) {
                    warning = __('Time limit reached for item "%s".', context.itemIdentifier);

                } else if (context.itemSessionState > states.interacting) {

                    if (context.remainingAttempts === 0) {
                        warning = __('No more attempts allowed for item "%s".', context.itemIdentifier);
                    } else {
                        warning = __('Item "%s" is completed.', context.itemIdentifier);
                    }
                }

                //we disable the item and warn the user
                if (warning) {
                    self.disableItem(context.itemUri);
                    self.trigger('warning', warning);
                }
            })
            .on('disableitem', function(){
                this.trigger('disabletools');
            })
            .on('enableitem', function(){
                this.trigger('enabletools');
            })
            .on('error', function(){
                this.trigger('disabletools enablenav');
            });

            //starts the event collection
            if(this.getProbeOverseer()){
                this.getProbeOverseer().start();
            }

            //load data and current context in parrallel at initialization
            return this.getProxy().init()
                       .then(function(results){
                            self.setTestData(results.testData);
                            self.setTestContext(results.testContext);
                            self.setTestMap(results.testMap);
                       });
        },

        /**
         * Rendering phase of the test runner
         *
         * Attach the test runner to the DOM
         *
         * @this {runner} the runner context, not the provider
         */
        render : function render(){

            var config = this.getConfig();
            var broker = this.getAreaBroker();

            config.renderTo.append(broker.getContainer());
        },

        /**
         * LoadItem phase of the test runner
         *
         * We call the proxy in order to get the item data
         *
         * @this {runner} the runner context, not the provider
         * @returns {Promise} that calls in parallel the state and the item data
         */
        loadItem : function loadItem(itemRef){
            var self = this;

            return self.getProxy().getItem(itemRef)
                .then(function(data){
                    //aggregate the results
                    return {
                        content : data.itemData,
                        baseUrl : data.baseUrl,
                        state : data.itemState,
                        rubrics : data.rubrics
                    };
                });
        },

        /**
         * RenderItem phase of the test runner
         *
         * Here we iniitialize the item runner and wrap it's call to the test runner
         *
         * @this {runner} the runner context, not the provider
         * @returns {Promise} resolves when the item is ready
         */
        renderItem : function renderItem(itemRef, itemData){
            var self = this;

            var changeState = function changeState(){
                self.setItemState(itemRef, 'changed', true);
            };

            if (itemData.rubrics) {
                this.trigger('loadrubricblock', itemData.rubrics);
            }

            return new Promise(function(resolve, reject){
                assetManager.setData('baseUrl', itemData.baseUrl);

                self.itemRunner = qtiItemRunner(itemData.content.type, itemData.content.data, {
                    assetManager: assetManager
                })
                .on('error', function(err){
                    self.trigger('enablenav');
                    reject(err);
                })
                .on('init', function(){
                    if(itemData.state){
                        this.setState(itemData.state);
                    }
                    this.render(self.getAreaBroker().getContentArea());
                })
                .on('render', function(){

                    this.on('responsechange', changeState);
                    this.on('statechange', changeState);

                    resolve();
                })
                .init();
            });
        },

        /**
         * UnloadItem phase of the test runner
         *
         * Item clean up
         *
         * @this {runner} the runner context, not the provider
         * @returns {Promise} resolves when the item is cleared
         */
        unloadItem : function unloadItem(itemRef){
            var self = this;

            self.trigger('disablenav disabletools');

            return new Promise(function(resolve, reject){
                if(self.itemRunner){
                    self.itemRunner
                        .on('clear', resolve)
                        .clear();
                    return;
                }
                resolve();
            });
        },

        /**
         * Finish phase of the test runner
         *
         * Calls proxy.finish to close the testj
         *
         * @this {runner} the runner context, not the provider
         * @returns {Promise} proxy.finish
         */
        finish : function finish(){

            this.trigger('disablenav disabletools')
                .trigger('endsession', 'finish');

            return this.getProxy().callTestAction('finish');
        },

        /**
         * Destroy phase of the test runner
         *
         * Clean up
         *
         * @this {runner} the runner context, not the provider
         */
        destroy : function destroy(){

            var self = this;

            var probeOverseer = this.getProbeOverseer();

            //if there is trace data collected by the probes
            if(probeOverseer){
                probeOverseer.flush()
                    .then(function(data){

                        //we reformat the time set into a trace variables
                        if(data && data.length){
                            var traceData = {};
                            _.forEach(data, function(entry){
                                var id = entry.type + '-' + entry.id;

                                if(entry.marker){
                                    id = entry.marker + '-' + id;
                                }
                                traceData[id] = entry;
                            });
                            //and send them
                            return self.getProxy().callTestAction('storeTraceData', { traceData : JSON.stringify(traceData) });
                        }
                    }).then(function(){
                        probeOverseer.stop();
                    });
            }

            this.itemRunner = null;
        }
    };

    return qtiProvider;
});

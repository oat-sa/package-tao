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
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'core/promise',
    'taoTests/runner/proxy',
    'taoQtiTest/test/runner/mocks/areaBrokerMock'
], function ($, _, Promise, proxyFactory, areaBroker) {
    'use strict';

    var defaultName = 'mock';

    /**
     * Build a runner mock
     * @param {Object} [plugins]
     * @param {Object} [config]
     * @param {String} [config.name]
     * @param {Object} [config.areaBroker]
     * @param {Array} [config.areas]
     * @param {Object} [config.proxy]
     * @param {String} [config.proxyName]
     * @returns {*}
     */
    function provider(config) {
        config = config || {};

        var providerApi = {
            //provider name
            name : config.name || defaultName,

            /**
             * Initialize and load the area broker with a correct mapping
             * @returns {areaBroker}
             */
            loadAreaBroker : function loadAreaBroker(){
                return config.areaBroker || areaBroker(config.areas);
            },

            /**
             * Initialize and load the test runner proxy
             * @returns {proxy}
             */
            loadProxy : function loadProxy(){
                return config.proxy || proxyFactory(config.proxyName || defaultName, config);
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
                var config = this.getConfig();

                //install event based behavior
                this.on('ready', function(){
                        this.loadItem('item-0');
                    })
                    .on('move', function(type){

                        var test = this.getTestContext();


                        if(type === 'next'){
                            if(test.items[test.current + 1]){
                                self.unloadItem('item-' +test.current);
                                self.loadItem('item-' + (test.current + 1));
                            } else {
                                self.finish();
                            }
                        }
                        else if(type === 'previous'){

                            if(test.items[test.current - 1]){
                                self.unloadItem('item-' +test.current);
                                self.loadItem('item-' + (test.current - 1));
                            } else {
                                self.loadItem('item-0');
                            }
                        }
                    });



                //load test data
                return new Promise(function(resolve, reject){

                    $.getJSON(config.url).success(function(test){
                        self.setTestContext(_.defaults(test || {}, {
                            items : {},
                            current: 0
                        }));

                        resolve();
                    });
                });
            },

            /**
             * Rendering phase of the test runner
             *
             * Attach the test runner to the DOM
             *
             * @this {runner} the runner context, not the provider
             */
            render : function(){

                var config = this.getConfig();
                var context = this.getTestContext();
                var broker = this.getAreaBroker();

                broker.getContainer().find('.title').html('Running Test ' + context.id);

                var $renderTo = config.renderTo || $('body');

                $renderTo.append(broker.getContainer());
            },

            /**
             * LoadItem phase of the test runner
             *
             * We call the proxy in order to get the item data
             *
             * @this {runner} the runner context, not the provider
             * @returns {Promise} that calls in parallel the state and the item data
             */
            loadItem : function loadItem(itemIndex){
                var self = this;

                var test = this.getTestContext();
                var broker = this.getAreaBroker();
                var $content = broker.getContentArea();

                $content.html('loading');

                return new Promise(function(resolve, reject){

                    setTimeout(function(){

                        test.current = parseInt(itemIndex.replace('item-',''), 10);
                        self.setTestContext(test);

                        resolve(test.items[test.current]);
                    }, 500);
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
            renderItem : function renderItem(itemIndex, item){
                var broker = this.getAreaBroker();
                var $content = broker.getContentArea();
                $content.html(
                    '<h1>' + item.id + '</h1>' +
                    '<div>' + item.content + '</div>'
                );
            },

            /**
             * Finish phase of the test runner
             *
             * Calls proxy.finish to close the testj
             *
             * @this {runner} the runner context, not the provider
             * @returns {Promise} proxy.finish
             */
            finish : function(){
                var broker = this.getAreaBroker();
                var $content = broker.getContentArea();
                $content.html('<h1>Done</h1>');
            }
        };

        return providerApi;
    }

    return provider;
});

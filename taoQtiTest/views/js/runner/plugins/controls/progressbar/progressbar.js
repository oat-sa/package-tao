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
 * Test Runner Control Plugin : Progress Bar
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoTests/runner/plugin',
    'tpl!taoQtiTest/runner/plugins/controls/progressbar/progressbar',
    'ui/progressbar'
], function ($, _, __, pluginFactory, progressTpl){
    'use strict';

    /**
     * Calculate progression based on the current context
     *
     * @param {Object} testContext - The current test context
     * @param {Object} testMap - The items organization map
     * @param {String} progressIndicator - to select the progression type
     * @param {String} [progressScope] - the progression scope
     * @returns {Object} the progression with a label and a ratio
     */
    var progressUpdater = function progressUpdater(testContext, testMap, progressIndicator, progressScope){

        /**
         * Provide progression calculation based on the type of indicator
         */
        var updater = {

            /**
            * Updates the progress bar displaying the percentage
            * @param {Object} testContext The progression context
            * @returns {{ratio: number, label: string}}
            */
            percentage : function percentage() {
                var total = Math.max(1, testContext.numberItems);
                var ratio = Math.floor(testContext.numberCompleted / total * 100);
                return {
                    ratio : ratio,
                    label : ratio + '%'
                };
            },

            /**
            * Updates the progress bar displaying the position
            * @param {Object} testContext The progression context
            * @returns {{ratio: number, label: string}}
            */
            position : function position() {

                //get the current test part in the map
                var getTestPart = function getTestPart(){
                    if(testMap && testMap.parts){
                        return testMap.parts[testContext.testPartId];
                    }
                };

                //get the current test section in the map
                var getTestSection = function getTestSection(){
                    var testPart = getTestPart();
                    if(testPart && testPart.sections){
                        return testPart.sections[testContext.sectionId];
                    }
                };

                //provides you the methods to get total and position by scope
                var progressScopeCounter = {
                    test : {
                        total : function(){
                            return Math.max(1, testContext.numberItems);
                        },
                        position : function(){
                            return testContext.itemPosition + 1;
                        }
                    },
                    testPart : {
                        total : function(){
                            var testPart = getTestPart();
                            if(testPart){
                                return _.reduce(testMap.parts[testContext.testPartId].sections, function(acc, section){
                                    return acc + _.size(section.items);
                                }, 0);
                            }
                            return 0;
                        },
                        position : function(){
                            var testSection = getTestSection();
                            if(testSection){
                                return testSection.items[testContext.itemIdentifier].positionInPart + 1;
                            }
                            return 0;
                        }
                    },
                    testSection : {
                        total : function(){
                            var testSection = getTestSection();
                            if(testSection){
                                return _.size(testSection.items);
                            }
                            return 0;
                        },
                        position : function(){
                            var testSection = getTestSection();
                            if(testSection){
                                return testSection.items[testContext.itemIdentifier].positionInSection + 1;
                            }
                            return 0;
                        }
                    }
                };

                var counter = progressScopeCounter[progressScope] || progressScopeCounter.test;
                var total = counter.total();
                var currentPosition = counter.position();

                return {
                    ratio : total > 0 ? Math.floor(currentPosition / total * 100) : 0,
                    label : __('Item %d of %d', currentPosition, total)
                };
            }
        };
        return updater[progressIndicator]();
    };


    /**
     * Returns the configured plugin
     */
    return pluginFactory({

        name : 'progressBar',

        /**
         * Initialize the plugin (called during runner's init)
         */
        init : function init(){
            var $progressLabel,
                $progressControl;
            var testRunner = this.getTestRunner();
            var testData   = testRunner.getTestData();
            var config     = testData.config.progressIndicator || {};
            var progressIndicator = config.type || 'percentage';
            var progressScope = config.scope || 'test';

            /**
             * Updae the progress bar
             */
            var update = function update (){
                var progressData = progressUpdater(testRunner.getTestContext(), testRunner.getTestMap(), progressIndicator, progressScope);
                if(progressData && $progressLabel && $progressControl){
                    $progressLabel.text(progressData.label);
                    $progressControl.progressbar('value', progressData.ratio);
                }
            };

            //create the progressbar
            this.$element = $(progressTpl());

            //store the controls
            $progressLabel = $('[data-control="progress-label"]', this.$element);
            $progressControl = $('[data-control="progress-bar"]', this.$element);

            //and initialize the progress bar component
            $progressControl.progressbar();

            //let update the progression
            update();

            testRunner
                .on('ready', update)
                .on('loaditem', update);
        },

        /**
         * Called during the runner's render phase
         */
        render : function render(){
            var $container = this.getAreaBroker().getControlArea();
            $container.append(this.$element);
        }
    });
});

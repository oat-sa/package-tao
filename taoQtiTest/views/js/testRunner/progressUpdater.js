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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/progressbar'
], function ($, _, __) {
    'use strict';

    /**
     * Provides a versatile progress bar updater
     * @type {{init: Function, update: Function}}
     */
    var progressUpdaters = {
        /**
         * Initializes the progress updater
         *
         * @param {String|jQuery|HTMLElement} progressBar The element on which put the progress bar
         * @param {String|jQuery|HTMLElement} progressLabel The element on which put the progress label
         * @returns {progressUpdaters}
         */
        init: function(progressBar, progressLabel) {
            this.progressBar = $(progressBar).progressbar();
            this.progressLabel = $(progressLabel);
            return this;
        },

        /**
         * Writes the progress label and update the progress by ratio
         * @param {String} label
         * @param {Number} ratio
         * @returns {progressUpdaters}
         */
        write: function(label, ratio) {
            this.progressLabel.text(label);
            this.progressBar.progressbar('value', ratio);
            return this;
        },

        /**
         * Updates the progress bar
         * @param {Object} testContext The progression context
         * @returns {{ratio: number, label: string}}
         */
        update: function(testContext) {
            var progressIndicator = testContext.progressIndicator || 'percentage';
            var progressIndicatorMethod = progressIndicator + 'Progression';
            var getProgression = this[progressIndicatorMethod] || this.percentageProgression;
            var progression = getProgression && getProgression(testContext) || {};

            this.write(progression.label, progression.ratio);
            return progression;
        },

        /**
         * Updates the progress bar displaying the percentage
         * @param {Object} testContext The progression context
         * @returns {{ratio: number, label: string}}
         */
        percentageProgression: function(testContext) {
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
        positionProgression: function(testContext) {
            var progressScope = testContext.progressIndicatorScope;
            var progressScopeCounter = {
                test : {
                    total : 'numberItems',
                    position : 'itemPosition'
                },
                testPart : {
                    total : 'numberItemsPart',
                    position : 'itemPositionPart'
                },
                testSection : {
                    total : 'numberItemsSection',
                    position : 'itemPositionSection'
                }
            };
            var counter = progressScopeCounter[progressScope] || progressScopeCounter.test;
            var total = Math.max(1, testContext[counter.total]);
            var position = testContext[counter.position] + 1;
            return {
                ratio : Math.floor(position / total * 100),
                label : __('Item %d of %d', position, total)
            };
        }
    };

    /**
     * Builds an instance of progressUpdaters
     * @param {String|jQuery|HTMLElement} progressBar The element on which put the progress bar
     * @param {String|jQuery|HTMLElement} progressLabel The element on which put the progress label
     * @returns {progressUpdaters}
     */
    var progressUpdaterFactory = function(progressBar, progressLabel) {
        var updater = _.clone(progressUpdaters, true);
        return updater.init(progressBar, progressLabel);
    };

    return progressUpdaterFactory;
});

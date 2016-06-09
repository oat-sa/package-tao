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
 * Component that controls the display of a timer (countdown/stopwatch like) into a QTI Test.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'moment',
    'core/encoder/time',
    'tpl!taoQtiTest/runner/plugins/controls/timer/timer'
], function ($, _, __, moment, timeEncoder, timerTpl) {
    'use strict';

    var hasAnimationFrame = 'requestAnimationFrame' in window;

    /**
     * Duration of a second in the timer's base unit
     * @type {Number}
     */
    var precision = 1000;

    /**
     * In a Qti context timers could be associated to those elements
     */
    var timerTypes = {
        test:     'assessmentTest',
        testPart: 'testPart',
        section:  'assessmentSection',
        item:     'assessmentItemRef'
    };

    /**
     * Create a new timer component
     * @param {Object} config - the timer config
     * @param {String} config.id - used to identified the timer
     * @param {String} config.type - the associated type (from timerTypes above)
     * @param {String} [config.label] - the label to display next to the timer
     * @param {Number} [config.remaining = 0] - the remaining time
     * @param {Number|Boolean} [config.warning = false] - to warn the user their is only the specified amount of time left
     * @param {Boolean} [config.running = true] - timer state
     * @returns {timerComponent} the timer component
     * @throws {TypeError} if the config is not correct
     */
    var timerComponentFactory = function timerComponentFactory(config){
        var data;

        if(!_.isPlainObject(config)){
            throw new TypeError('A timer needs to be configured using a config object');
        }
        if(_.isEmpty(config.id)){
            throw new TypeError('A timer needs and identifier');
        }
        if(!_.contains(timerTypes, config.type)){
            throw new TypeError('Invalid type configured');
        }

        data = _.defaults(config, {
            label:     '',
            remaining: 0,
            warning:   false,
            running:   true
        });

        /**
         * @typedef {timerComponent} the component to manage the timer
         */
        return {

            /**
             * Initialize the component
             * @returns {timerComponent} chains
             */
            init   : function init(){
               this.$element = $(timerTpl(data));
               this.$display = $('.qti-timer_time', this.$element);
               return this;
            },

            /**
             * Attach the component to the DOM
             * @param {jQueryElement} $container - where to append the component
             * @returns {timerComponent} chains
             */
            render : function render($container){
                $container.append(this.$element);
                return this;
            },

            /**
             * Destroy the component
             * @returns {timerComponent} chains
             */
            destroy : function destroy(){
                this.$element.remove();
                return this;
            },

            /**
             * Refresh the display
             * @returns {timerComponent} chains
             */
            refresh : function refresh(){
                var self = this;
                var update = function update(){
                    self.$display.text( timeEncoder.encode( data.remaining / precision) );
                };
                if(hasAnimationFrame){
                   window.requestAnimationFrame(update);
                } else {
                    update();
                }

                return this;
            },

            /**
             * Warn about time remaining ?
             * @returns {Boolean|String} if not false, the warning message
             */
            warn : function warn() {
                var remaining;
                var message = false;

                if(_.isFinite(data.warning) && data.remaining <= data.warning){
                    remaining = moment.duration(data.remaining / precision, "seconds").humanize();

                    this.$element.addClass('qti-timer__warning');
                    switch (data.type) {
                        case 'assessmentItemRef':
                            message = __("Warning – You have %s remaining to complete this item.", remaining);
                            break;

                        case 'assessmentSection':
                            message = __("Warning – You have %s remaining to complete this section.", remaining);
                            break;

                        case 'testPart':
                            message = __("Warning – You have %s remaining to complete this test part.", remaining);
                            break;

                        case 'assessmentTest':
                            message = __("Warning – You have %s remaining to complete the test.", remaining);
                            break;
                    }

                    data.warning = 0;
                }

                return message;
            },


            /**
             * Get the timer id
             * @returns {String} the id
             */
            id  : function id(){
                return data.id;
            },

            /**
             * Get/set the current/remaining value
             * @param {Number} [value] - only as a setter
             * @returns {Number|timerComponent} the value as a getter, or chains as a setter
             */
            val : function val(value){
                if(typeof value !== 'undefined'){
                    data.remaining = value;
                    return this;
                }
                return data.remaining;
            },

            /**
             * Get/set the running state
             * @param {Boolean} [value] - only as a setter
             * @returns {Boolean|timerComponent} the value as a getter, or chains as a setter
             */
            running : function running(value){
                if(typeof value !== 'undefined'){
                    data.running = !!value;
                    return this;
                }
                return !!data.running;
            }
        };
    };

    return timerComponentFactory;
});

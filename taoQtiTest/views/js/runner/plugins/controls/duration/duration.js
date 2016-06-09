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
 * Test Runner Control Plugin : Duration (record exact spent time duration)
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/polling',
    'core/timer',
    'core/store',
    'taoTests/runner/plugin',
], function (_, pollingFactory, timerFactory, store, pluginFactory) {
    'use strict';

    /**
     * Time interval between duration capture in ms
     * @type {Number}
     */
    var refresh = 1000;


    /**
     * Creates the timer plugin
     */
    return pluginFactory({

        name: 'duration',

        /**
         * Initializes the plugin (called during runner's init)
         */
        init: function init() {

            var self = this;
            var testRunner   = this.getTestRunner();

            //where the duration of attempts are stored
            var durationStore = store('duration-' + testRunner.getConfig().serviceCallId);

            //one stopwatch to count the time
            this.stopwatch = timerFactory({
                autoStart : false
            });

            //update the duration on a regular basis
            this.polling = pollingFactory({

                action : function updateDuration() {

                    //how many time elapsed from the last tick ?
                    var elapsed = self.stopwatch.tick();
                    var context = testRunner.getTestContext();

                    //store by attempt
                    var itemAttemptId = context.itemIdentifier + '#' + context.attempt;

                    durationStore.getItem(itemAttemptId).then(function(duration){
                        duration = _.isNumber(duration) ? duration : 0;
                        elapsed  = _.isNumber(elapsed) && elapsed > 0 ? (elapsed / 1000) : 0;

                        //store the last duration
                        durationStore.setItem(itemAttemptId, duration + elapsed);
                    });
                },
                interval : refresh,
                autoStart : false
            });

            //change plugin state
            testRunner

                .after('renderitem enableitem', function(){
                    self.enable();
                })

                .before('move disableitem skip error', function(e){
                    if (self.getState('enabled')) {
                        self.disable();
                    }
                })

                /**
                 * @event duration.get
                 * @param {String} attemptId - the attempt id to get the duration for
                 * @param {getDuration} getDuration - a receiver callback
                 */
                .on('plugin-get.duration', function(e, attemptId, getDuration){
                    if(_.isFunction(getDuration)){
                        if(!/^(.*)+#+\d+$/.test(attemptId)){
                            return getDuration(Promise.reject(new Error('Is it really an attempt id, like "itemid#attempt"')));
                        }

                        /**
                        * @callback getDuration
                        * @param {Promise} p - that resolve with the duration value
                        */
                        getDuration(durationStore.getItem(attemptId));
                    }
                })

                .before('finish', function(e){
                    var done = e.done();

                    self.storage.clear()
                        .then(done)
                        .catch(done);
                });
        },

        /**
         * Called during the runner's destroy phase
         */
        destroy : function destroy (){
            this.polling.stop();
            this.stopwatch.stop();
        },

        /**
         * Enables the duration count
         */
        enable : function enable (){
            this.polling.start();
            this.stopwatch.resume();
        },

        /**
         * Disables the duration count
         */
        disable : function disable (){
            this.polling.stop();
            this.stopwatch.pause();
        }
    });
});

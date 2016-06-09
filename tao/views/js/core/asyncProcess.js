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
    'lodash',
    'core/promise',
    'core/eventifier'
], function (_, Promise, eventifier) {
    'use strict';

    /**
     * Defines a manager for async process with deferred steps.
     * It will start the process only if it is not already running.
     * The running process must register each deferred steps, and it must also notify its logical end
     * (i.e.: when its main stuff is finished, no matter if the deferred steps are also finished)
     * @returns {asyncProcess}
     * @trigger start - When a process start
     * @trigger step - When a step is added
     * @trigger resolve - When the process has finished without error
     * @trigger reject - When the process has finished on error
     */
    function asyncProcessFactory() {
        var running = false;
        var steps = [];

        return eventifier({
            /**
             * Tells if a process is running
             * @returns {Boolean}
             */
            isRunning: function isRunning() {
                return running;
            },

            /**
             * Start a new process if there is no running process.
             * @param {Function} [cb] - The process to start
             * @returns {boolean} - Returns true if the process can be started
             */
            start: function start(cb) {
                var started = false;
                if (!running) {
                    steps = [];
                    running = true;
                    started = true;
                    if (_.isFunction(cb)) {
                        cb();
                    }

                    /**
                     * @event asyncProcess#start
                     */
                    this.trigger('start');
                }
                return started;
            },

            /**
             * Add a process step by providing a promise.
             * The manager will wait for this promise to declare the process is finished.
             * @param {Promise} step
             * @returns {asyncProcess}
             */
            addStep: function addStep(step) {
                steps.push(step);

                /**
                 * @event asyncProcess#step
                 * @param {Promise} step - The added step
                 */
                this.trigger('step', step);

                return this;
            },

            /**
             * Notifies the logical end of the running process. The deferred steps may still be running at this time.
             * Note: All the deferred steps must be already registered. No later registration will be accepted.
             * @param {Function} [cb] - A nodeback like function which will be called when all the deferred steps have finished or an error occurs
             * @returns {Promise} - Returns the finish promise
             */
            done: function done(cb) {
                var self = this;
                var finish = Promise.all(steps);

                finish
                    .then(function(data) {
                        running = false;

                        if (_.isFunction(cb)) {
                            cb(null, data);
                        }

                        /**
                         * @event asyncProcess#resolve
                         * @param {Object} data - The resolved data
                         */
                        self.trigger('resolve', data);
                    })
                    .catch(function(error) {
                        running = false;

                        if (_.isFunction(cb)) {
                            cb(error || true);
                        }

                        /**
                         * @event asyncProcess#reject
                         * @param {Object} error - The reject reason
                         */
                        self.trigger('reject', error);
                    });

                return finish;
            }
        });
    }

    return asyncProcessFactory;
});

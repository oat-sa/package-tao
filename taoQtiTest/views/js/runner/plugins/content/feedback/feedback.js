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
 * Test Runner Content Plugin : Feedback
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'i18n',
    'taoTests/runner/plugin',
    'ui/feedback'
], function ($, __, pluginFactory, feedback){
    'use strict';

    /**
     * Returns the configured plugin
     */
    return pluginFactory({
        name : 'feedback',

        /**
         * Initialize the plugin (called during runner's init)
         */
        init : function init(){
            var self = this;

            //keep a ref of the feedbacks
            var currentFeedback;

            var testRunner = this.getTestRunner();

            /**
             * Close the current feedback
             */
            var closeCurrent = function closeCurrent(){
                if(currentFeedback){
                    currentFeedback.close();
                }
            };

            //change plugin state
            testRunner
                .on('error', function(err){
                    var message = err;
                    var type = 'error';

                    if ('object' === typeof err) {
                        message = err.message;
                        type = err.type;
                    }

                    if (!message) {
                        switch (type) {
                            case 'TestState':
                                message = __('The test has been closed/suspended!');
                                break;

                            case 'FileNotFound':
                                message = __('File not found!');
                                break;

                            default:
                                message = __('An error occurred!');
                        }
                    }

                    currentFeedback = feedback().error(message);
                })
                .on('warning', function(message){
                    currentFeedback = feedback().warning(message);
                })
                .on('info', function(message){
                    currentFeedback = feedback().info(message);
                })
                .on('alert confirm unloaditem', closeCurrent);
        }
    });
});

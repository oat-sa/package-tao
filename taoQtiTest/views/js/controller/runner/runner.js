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
 * Test runner controller entry
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'module',
    'core/promise',
    'layout/loading-bar',
    'ui/feedback',
    'taoTests/runner/runner',
    'taoQtiTest/runner/provider/qti',
    'taoTests/runner/proxy',
    'taoQtiTest/runner/proxy/qtiServiceProxy',
    'taoQtiTest/runner/plugins/loader',
    'css!taoQtiTestCss/new-test-runner'
], function ($, _, __, module, Promise, loadingBar, feedback,
             runner, qtiProvider, proxy, qtiServiceProxy, pluginLoader) {
    'use strict';


    /*
     *TODO plugins list, provider registration should be loaded dynamically
     */

    runner.registerProvider('qti', qtiProvider);
    proxy.registerProxy('qtiServiceProxy', qtiServiceProxy);

    /**
     * Catches errors
     * @param {Object} err
     */
    function onError(err) {
        loadingBar.stop();

        feedback().error(err.message);

        //TODO to be replaced by the logger
        window.console.error(err);
    }

    /**
     * Call the destroy action of the test runner
     * Must be applied on a test runner instance: destroyRunner.call(runner);
     */
    function destroyRunner() {
        var self = this;
        //FIXME this should be handled by the eventifier instead of doing a delay
        _.delay(function(){
            self.destroy();
        }, 300); //let deferred exec a chance to finish
    }


    /**
     * Initializes and launches the test runner
     * @param {Object} config
     */
    function initRunner(config) {
        var plugins = pluginLoader.getPlugins();

        /**
         *  At the end, we are redirected to the exit URL
         */
        var leave = function leave (){
            window.location = config.exitUrl;
        };

        config = _.defaults(config, {
            renderTo: $('.runner')
        });

        //instantiate the QtiTestRunner
        runner('qti', plugins, config)
            .before('error', function (e, err) {
                var self = this;

                onError(err);

                // test has been closed/suspended => redirect to the index page after message acknowledge
                if (err && err.type && err.type === 'TestState') {

                    if(!this.getState('ready')){
                        //if we open an inconstent test (should never happen) we let a few sec to
                        //read the error message then leave
                        _.delay(leave, 2000);
                    } else {
                        this.trigger('alert', err.message, function() {
                            self.trigger('endsession', 'teststate', err.code);
                            destroyRunner.call(self);
                        });
                    }
                    // prevent other messages/warnings
                    return false;
                }
            })
            .on('ready', function () {
                _.defer(function () {
                    $('.runner').removeClass('hidden');
                });
            })
            .on('unloaditem', function () {
                //TODO move the loading bar into a plugin
                loadingBar.start();
            })
            .on('renderitem', function () {
                //TODO move the loading bar into a plugin
                loadingBar.stop();
            })
            .after('finish', function () {
                destroyRunner.call(this);
            })
            .on('destroy', leave)
            .init();
    }

    /**
     * List of options required by the controller
     * @type {String[]}
     */
    var requiredOptions = [
        'testDefinition',
        'testCompilation',
        'serviceCallId',
        'exitUrl'
    ];

    /**
     * The runner controller
     */
    var runnerController = {

        /**
         * Controller entry point
         *
         * @param {Object} options - the testRunner options
         * @param {String} options.testDefinition
         * @param {String} options.testCompilation
         * @param {String} options.serviceCallId
         * @param {String} options.serviceController
         * @param {String} options.serviceExtension
         * @param {String} options.exitUrl - the full URL where to return at the final end of the test
         */
        start: function start(options) {
            var startOptions = options || {};
            var config = module.config();
            var missingOption = false;

            // verify required options
            _.forEach(requiredOptions, function(name) {
                if (!startOptions[name]) {
                    onError({
                        success: false,
                        code: 0,
                        type: 'error',
                        message: __('Missing required option %s', name)
                    });
                    missingOption = true;
                    return false;
                }
            });

            if (!missingOption) {
                loadingBar.start();

                if (config) {
                    _.forEach(config.plugins, function (plugin) {
                        pluginLoader.add(plugin.module, plugin.category, plugin.position);
                    });
                }

                pluginLoader.load()
                    .then(function () {
                        initRunner(_.omit(startOptions, 'plugins'));
                    })
                    .catch(function () {
                        onError({
                            success: false,
                            code: 0,
                            type: 'error',
                            message: __('Plugin dependency error!')
                        });
                    });
            }
        }
    };

    return runnerController;
});

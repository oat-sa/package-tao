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
    'helpers',
    'taoTests/runner/runner',
    'taoQtiTest/test/runner/mocks/providerMock',
    'taoQtiTest/runner/plugins/controls/timer/timer'
], function(_, helpers, runnerFactory, providerMock, timerFactory) {
    'use strict';

    var providerName = 'mock';
    runnerFactory.registerProvider(providerName, providerMock());

    QUnit.module('timerFactory');


    QUnit.test('module', 3, function(assert) {
        var runner = runnerFactory(providerName);

        assert.equal(typeof timerFactory, 'function', "The timerFactory module exposes a function");
        assert.equal(typeof timerFactory(runner), 'object', "The timerFactory factory produces an instance");
        assert.notStrictEqual(timerFactory(runner), timerFactory(runner), "The timerFactory factory provides a different instance on each call");
    });


    var pluginApi = [
        { name : 'init', title : 'init' },
        { name : 'render', title : 'render' },
        { name : 'finish', title : 'finish' },
        { name : 'destroy', title : 'destroy' },
        { name : 'trigger', title : 'trigger' },
        { name : 'getTestRunner', title : 'getTestRunner' },
        { name : 'getAreaBroker', title : 'getAreaBroker' },
        { name : 'getConfig', title : 'getConfig' },
        { name : 'setConfig', title : 'setConfig' },
        { name : 'getState', title : 'getState' },
        { name : 'setState', title : 'setState' },
        { name : 'show', title : 'show' },
        { name : 'hide', title : 'hide' },
        { name : 'enable', title : 'enable' },
        { name : 'disable', title : 'disable' }
    ];

    QUnit
        .cases(pluginApi)
        .test('plugin API ', 1, function(data, assert) {
            var runner = runnerFactory(providerName);
            var timer = timerFactory(runner);
            assert.equal(typeof timer[data.name], 'function', 'The timerFactory instances expose a "' + data.name + '" function');
        });


    QUnit.asyncTest('timer.init', function(assert) {
        var runner = runnerFactory(providerName);
        var timer = timerFactory(runner, runner.getAreaBroker());

        timer.init()
            .then(function() {
                assert.equal(timer.polling.is('stopped'), true, 'The timer must not start the polling before an item is loaded');
                assert.equal(timer.stopwatch.is('started'), false, 'The timer must not start the countdown before an item is loaded');
                assert.equal(typeof timer.$element, 'object', 'The timer has pre-rendered the element');
                assert.equal(timer.$element.length, 1, 'The timer has pre-rendered the container');
                assert.equal(timer.getState('init'), true, 'The timer is initialised');

                QUnit.start();
            })
            .catch(function(err) {
                console.log(err);
                assert.ok(false, 'The init method must not fail');
                QUnit.start();
            });
    });


    QUnit.asyncTest('timer.render', function(assert) {
        var runner = runnerFactory(providerName);
        var timer = timerFactory(runner, runner.getAreaBroker());

        timer.init()
            .then(function() {
                assert.equal(typeof timer.$element, 'object', 'The timer has pre-rendered the element');
                assert.equal(timer.$element.length, 1, 'The timer has pre-rendered the container');

                timer.render()
                    .then(function() {
                        var $container = runner.getAreaBroker().getControlArea();

                        assert.equal(timer.getState('ready'), true, 'The timer is ready');
                        assert.equal($container.find(timer.$element).length, 1, 'The timer has inserted its content into the layout');

                        QUnit.start();
                    })
                    .catch(function(err) {
                        console.log(err);
                        assert.ok(false, 'The render method must not fail');
                        QUnit.start();
                    });
            })
            .catch(function(err) {
                console.log(err);
                assert.ok(false, 'The init method must not fail');
                QUnit.start();
            });
    });


    QUnit.asyncTest('timer.destroy', function(assert) {
        var runner = runnerFactory(providerName);
        var timer = timerFactory(runner, runner.getAreaBroker());

        timer.init()
            .then(function() {
                assert.equal(timer.getState('init'), true, 'The timer is initialised');
                assert.equal(typeof timer.$element, 'object', 'The timer has pre-rendered the element');
                assert.equal(timer.$element.length, 1, 'The timer has pre-rendered the container');

                timer.render()
                    .then(function() {
                        var $container = runner.getAreaBroker().getControlArea();

                        assert.equal(timer.getState('ready'), true, 'The timer is ready');
                        assert.equal($container.find(timer.$element).length, 1, 'The timer has inserted its content into the layout');

                        timer.enable()
                            .then(function() {
                                assert.equal(timer.getState('enabled'), true, 'The timer is enabled');
                                assert.equal(timer.polling.is('stopped'), false, 'The timer has started the polling');
                                assert.equal(timer.stopwatch.is('started'), true, 'The timer has started the countdown');
                                assert.equal(timer.stopwatch.is('running'), true, 'The timer is running the countdown');

                                timer.destroy()
                                    .then(function() {
                                        var $container = runner.getAreaBroker().getControlArea();

                                        assert.equal(timer.getState('init'), false, 'The timer is destroyed');
                                        assert.equal($container.find(timer.$element).length, 0, 'The timer has removed its content from the layout');

                                        assert.equal(timer.polling.is('stopped'), true, 'The timer has stopped the polling');
                                        assert.equal(timer.stopwatch.is('started'), false, 'The timer has stopped the countdown');

                                        QUnit.start();
                                    })
                                    .catch(function(err) {
                                        console.log(err);
                                        assert.ok(false, 'The destroy method must not fail');
                                        QUnit.start();
                                    });
                            })
                            .catch(function(err) {
                                console.log(err);
                                assert.ok(false, 'The enable method must not fail');
                                QUnit.start();
                            });
                    })
                    .catch(function(err) {
                        console.log(err);
                        assert.ok(false, 'The render method must not fail');
                        QUnit.start();
                    });
            })
            .catch(function(err) {
                console.log(err);
                assert.ok(false, 'The init method must not fail');
                QUnit.start();
            });
    });


    QUnit.asyncTest('timer.enable', function(assert) {
        var runner = runnerFactory(providerName);
        var timer = timerFactory(runner, runner.getAreaBroker());

        timer.init()
            .then(function() {
                assert.equal(timer.getState('init'), true, 'The timer is initialised');
                assert.equal(timer.getState('enabled'), false, 'The timer is disabled');

                timer.enable()
                    .then(function() {
                        assert.equal(timer.getState('enabled'), true, 'The timer is enabled');
                        assert.equal(timer.polling.is('stopped'), false, 'The timer has started the polling');
                        assert.equal(timer.stopwatch.is('started'), true, 'The timer has started the countdown');
                        assert.equal(timer.stopwatch.is('running'), true, 'The timer is running the countdown');

                        timer.destroy()
                            .then(function() {
                                assert.equal(timer.getState('init'), false, 'The timer is destroyed');
                                assert.equal(timer.polling.is('stopped'), true, 'The timer has stopped the polling');
                                assert.equal(timer.stopwatch.is('started'), false, 'The timer has stopped the countdown');

                                QUnit.start();
                            })
                            .catch(function(err) {
                                console.log(err);
                                assert.ok(false, 'The destroy method must not fail');
                                QUnit.start();
                            });
                    })
                    .catch(function(err) {
                        console.log(err);
                        assert.ok(false, 'The enable method must not fail');
                        QUnit.start();
                    });
            })
            .catch(function(err) {
                console.log(err);
                assert.ok(false, 'The init method must not fail');
                QUnit.start();
            });
    });


    QUnit.asyncTest('timer.disable', function(assert) {
        var runner = runnerFactory(providerName);
        var timer = timerFactory(runner, runner.getAreaBroker());

        timer.init()
            .then(function() {
                assert.equal(timer.getState('init'), true, 'The timer is initialised');
                assert.equal(timer.getState('enabled'), false, 'The timer is disabled');

                timer.enable()
                    .then(function() {
                        assert.equal(timer.getState('enabled'), true, 'The timer is enabled');
                        assert.equal(timer.polling.is('stopped'), false, 'The timer has started the polling');
                        assert.equal(timer.stopwatch.is('started'), true, 'The timer has started the countdown');
                        assert.equal(timer.stopwatch.is('running'), true, 'The timer is running the countdown');

                        timer.disable()
                            .then(function() {
                                assert.equal(timer.getState('enabled'), false, 'The timer is disabled');
                                assert.equal(timer.polling.is('stopped'), true, 'The timer has stopped the polling');
                                assert.equal(timer.stopwatch.is('started'), true, 'The timer has keeped the countdown');
                                assert.equal(timer.stopwatch.is('running'), false, 'The timer is not running the countdown');

                                QUnit.start();
                            })
                            .catch(function(err) {
                                console.log(err);
                                assert.ok(false, 'The disable method must not fail');
                                QUnit.start();
                            });
                    })
                    .catch(function(err) {
                        console.log(err);
                        assert.ok(false, 'The enable method must not fail');
                        QUnit.start();
                    });
            })
            .catch(function(err) {
                console.log(err);
                assert.ok(false, 'The init method must not fail');
                QUnit.start();
            });
    });


    QUnit.asyncTest('timer.show/timer.hide', function(assert) {
        var runner = runnerFactory(providerName);
        var timer = timerFactory(runner, runner.getAreaBroker());

        timer.init()
            .then(function() {
                assert.equal(typeof timer.$element, 'object', 'The timer has pre-rendered the element');
                assert.equal(timer.$element.length, 1, 'The timer has pre-rendered the container');

                timer.render()
                    .then(function() {
                        var $container = runner.getAreaBroker().getControlArea();
                        timer.setState('visible', true);

                        assert.equal(timer.getState('ready'), true, 'The timer is ready');
                        assert.equal(timer.getState('visible'), true, 'The timer is visible');
                        assert.equal($container.find(timer.$element).length, 1, 'The timer has inserted its content into the layout');

                        timer.hide()
                            .then(function() {
                                assert.equal(timer.getState('visible'), false, 'The timer is not visible');
                                assert.equal(timer.$element.css('display'), 'none', 'The timer element is hidden');

                                timer.show()
                                    .then(function() {
                                        assert.equal(timer.getState('visible'), true, 'The timer is visible');
                                        assert.notEqual(timer.$element.css('display'), 'none', 'The timer element is visible');

                                        QUnit.start();
                                    })
                                    .catch(function(err) {
                                        console.log(err);
                                        assert.ok(false, 'The show method must not fail');
                                        QUnit.start();
                                    });
                            })
                            .catch(function(err) {
                                console.log(err);
                                assert.ok(false, 'The hide method must not fail');
                                QUnit.start();
                            });
                    })
                    .catch(function(err) {
                        console.log(err);
                        assert.ok(false, 'The render method must not fail');
                        QUnit.start();
                    });
            })
            .catch(function(err) {
                console.log(err);
                assert.ok(false, 'The init method must not fail');
                QUnit.start();
            });
    });
});

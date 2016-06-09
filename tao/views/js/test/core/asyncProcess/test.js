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
    'core/asyncProcess'
], function (_, Promise, asyncProcessFactory) {
    'use strict';

    QUnit.module('asyncProcess');


    QUnit.test('factory', function (assert) {
        QUnit.expect(3);
        assert.ok(typeof asyncProcessFactory === 'function', 'the module exposes a function');
        assert.ok(typeof asyncProcessFactory() === 'object', 'the factory produces an object');
        assert.ok(asyncProcessFactory() !== asyncProcessFactory(), 'the factory produces a different object at each call');
    });


    var asyncProcessApi = [
        {name: 'isRunning', title: 'isRunning'},
        {name: 'start', title: 'start'},
        {name: 'addStep', title: 'addStep'},
        {name: 'done', title: 'done'}
    ];

    QUnit
        .cases(asyncProcessApi)
        .test('asyncProcess API ', 1, function (data, assert) {
            QUnit.expect(1);

            var asyncProcess = asyncProcessFactory();

            assert.equal(typeof asyncProcess[data.name], 'function', 'The asyncProcess expose a "' + data.name + '" function');
        });


    QUnit.asyncTest('simple process', function (assert) {
        QUnit.expect(11);

        var asyncProcess = asyncProcessFactory();

        asyncProcess
            .on('start', function() {
                assert.ok(true, 'The process is started');
                QUnit.start();
            })
            .on('resolve', function() {
                assert.ok(true, 'The process is finished and the resolve event has been triggered');
                QUnit.start();
            })
            .on('reject', function() {
                assert.ok(false, 'The process is finished and the reject event has been triggered');
                QUnit.start();
            });

        assert.equal(asyncProcess.isRunning(), false, 'There is no running process at this time');

        assert.equal(asyncProcess.start(), true, 'The process has started');

        assert.equal(asyncProcess.start(), false, 'No other process can start until the current one is finished');

        assert.equal(asyncProcess.isRunning(), true, 'There is a running process at this time');

        var p = asyncProcess.done(function (err) {
            assert.ok(!err, 'No error is reported by the handler');
            assert.equal(asyncProcess.isRunning(), false, 'The process is now finished');
            QUnit.start();
        });

        QUnit.stop(3);

        assert.ok('object' === typeof p, 'The done method returns an object');
        assert.ok('function' === typeof p.then && 'function' === typeof p.catch, 'The done method returns a promise');

        p.then(function () {
            assert.ok(true, 'The promise is resolved');
            QUnit.start();
        }).catch(function () {
            assert.ok(false, 'The promise must not be rejected');
            QUnit.start();
        })
    });


    QUnit.asyncTest('deferred process', function (assert) {
        QUnit.expect(13);
        QUnit.stop(3);

        var asyncProcess = asyncProcessFactory();

        asyncProcess
            .on('start', function() {
                assert.ok(true, 'The process is started');
                QUnit.start();
            })
            .on('resolve', function() {
                assert.ok(true, 'The process is finished and the resolve event has been triggered');
                QUnit.start();
            })
            .on('reject', function() {
                assert.ok(false, 'The process is finished and the reject event has been triggered');
                QUnit.start();
            });

        function process() {
            assert.ok(true, 'The process main has been called');
            assert.ok(asyncProcess.isRunning(), 'The process is running');

            setTimeout(function() {
                var p = asyncProcess.done(function (err) {
                    assert.ok(!err, 'No error is reported by the handler');
                    assert.equal(asyncProcess.isRunning(), false, 'The process is now finished');
                    QUnit.start();
                });

                assert.ok('object' === typeof p, 'The done method returns an object');
                assert.ok('function' === typeof p.then && 'function' === typeof p.catch, 'The done method returns a promise');

                p.then(function () {
                    assert.ok(true, 'The promise is resolved');
                    QUnit.start();
                }).catch(function () {
                    assert.ok(false, 'The promise must not be rejected');
                    QUnit.start();
                })
            }, 250);
        }

        assert.equal(asyncProcess.isRunning(), false, 'There is no running process at this time');

        assert.equal(asyncProcess.start(process), true, 'The process has started');

        assert.equal(asyncProcess.start(process), false, 'No other process can start until the current one is finished');

        assert.equal(asyncProcess.isRunning(), true, 'There is a running process at this time');
    });


    QUnit.asyncTest('process with steps', function (assert) {
        QUnit.expect(24);
        QUnit.stop(5);

        var asyncProcess = asyncProcessFactory();

        asyncProcess
            .on('start', function() {
                assert.ok(true, 'The process is started');
                QUnit.start();
            })
            .on('step', function() {
                assert.ok(true, 'A step has been added');
                QUnit.start();
            })
            .on('resolve', function(data) {
                assert.ok(true, 'The process is finished and the resolve event has been triggered');

                assert.ok(_.isArray(data), 'The resolved data has been provided');
                assert.ok(_.indexOf(data, 1) !== -1, 'The data contains the first resolved step');
                assert.ok(_.indexOf(data, 2) !== -1, 'The data contains the second resolved step');
                QUnit.start();
            })
            .on('reject', function() {
                assert.ok(false, 'The process is finished and the reject event has been triggered');
                QUnit.start();
            });

        function process() {
            assert.ok(true, 'The process main has been called');
            assert.ok(asyncProcess.isRunning(), 'The process is running');

            asyncProcess.addStep(new Promise(function(resolve) {
                setTimeout(function() {
                    resolve(1);
                }, 300);
            }));

            asyncProcess.addStep(new Promise(function(resolve) {
                setTimeout(function() {
                    resolve(2);
                }, 400);
            }));

            setTimeout(function() {
                var p = asyncProcess.done(function (err, data) {
                    assert.ok(!err, 'No error is reported by the handler');
                    assert.equal(asyncProcess.isRunning(), false, 'The process is now finished');

                    assert.ok(_.isArray(data), 'The resolved data has been provided');
                    assert.ok(_.indexOf(data, 1) !== -1, 'The data contains the first resolved step');
                    assert.ok(_.indexOf(data, 2) !== -1, 'The data contains the second resolved step');

                    QUnit.start();
                });

                assert.ok('object' === typeof p, 'The done method returns an object');
                assert.ok('function' === typeof p.then && 'function' === typeof p.catch, 'The done method returns a promise');

                p.then(function (data) {
                    assert.ok(true, 'The promise is resolved');

                    assert.ok(_.isArray(data), 'The resolved data has been provided');
                    assert.ok(_.indexOf(data, 1) !== -1, 'The data contains the first resolved step');
                    assert.ok(_.indexOf(data, 2) !== -1, 'The data contains the second resolved step');

                    QUnit.start();
                }).catch(function () {
                    assert.ok(false, 'The promise must not be rejected');
                    QUnit.start();
                })
            }, 250);
        }

        assert.equal(asyncProcess.isRunning(), false, 'There is no running process at this time');

        assert.equal(asyncProcess.start(process), true, 'The process has started');

        assert.equal(asyncProcess.start(process), false, 'No other process can start until the current one is finished');

        assert.equal(asyncProcess.isRunning(), true, 'There is a running process at this time');
    });


    QUnit.asyncTest('process with errors', function (assert) {
        QUnit.expect(14);
        QUnit.stop(3);

        var asyncProcess = asyncProcessFactory();

        asyncProcess
            .on('start', function() {
                assert.ok(true, 'The process is started');
                QUnit.start();
            })
            .on('resolve', function() {
                assert.ok(false, 'The process is finished and the resolve event has been triggered');
                QUnit.start();
            })
            .on('reject', function(err) {
                assert.ok(true, 'The process is finished and the reject event has been triggered');
                assert.equal(err, 'oups', 'An error is reported by the handler');
                QUnit.start();
            });

        function process() {
            assert.ok(true, 'The process main has been called');
            assert.ok(asyncProcess.isRunning(), 'The process is running');

            asyncProcess.addStep(new Promise(function(resolve, reject) {
                setTimeout(function() {
                    reject('oups');
                }, 300);
            }));

            asyncProcess.addStep(new Promise(function(resolve) {
                setTimeout(function() {
                    resolve(2);
                }, 400);
            }));

            setTimeout(function() {
                var p = asyncProcess.done(function (err) {
                    assert.equal(err, 'oups', 'An error is reported by the handler');
                    assert.equal(asyncProcess.isRunning(), false, 'The process is now finished');

                    QUnit.start();
                });

                assert.ok('object' === typeof p, 'The done method returns an object');
                assert.ok('function' === typeof p.then && 'function' === typeof p.catch, 'The done method returns a promise');

                p.then(function () {
                    assert.ok(false, 'The promise must be rejected!');
                    QUnit.start();
                }).catch(function (err) {
                    assert.equal(err, 'oups', 'An error is reported by the handler');
                    QUnit.start();
                })
            }, 250);
        }

        assert.equal(asyncProcess.isRunning(), false, 'There is no running process at this time');

        assert.equal(asyncProcess.start(process), true, 'The process has started');

        assert.equal(asyncProcess.start(process), false, 'No other process can start until the current one is finished');

        assert.equal(asyncProcess.isRunning(), true, 'There is a running process at this time');
    });

});

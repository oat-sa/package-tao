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
 * @author Sam <sam@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'taoTests/runner/runner',
    'taoTests/runner/probeOverseer',
    'core/store'
], function($, _, runnerFactory, probeOverseer) {
    'use strict';

    var testId = 'test-123';
    var mockRunner = {
        init: _.noop,
        on: _.noop
    };
    var mockProvider = {
        init: _.noop,
        loadAreaBroker: _.noop
    };


    QUnit.module('API');

    QUnit.test('module factory', function(assert) {
        QUnit.expect(6);
        assert.equal(typeof probeOverseer, 'function', "The module exposes a function");

        assert.throws(function() {
            probeOverseer();
        }, TypeError, "The factory needs a test id");

        assert.throws(function() {
            probeOverseer(testId);
        }, TypeError, "The factory needs a runner");

        assert.throws(function() {
            probeOverseer(testId, {});
        }, TypeError, "The factory needs a valid runner");

        assert.equal(typeof probeOverseer(testId, mockRunner), 'object', "The module is a factory");
        assert.notEqual(probeOverseer(testId, mockRunner), probeOverseer(testId, mockRunner), "The factory creates different instances");
    });


    QUnit.test('own api', function(assert) {
        QUnit.expect(7);
        var probes = probeOverseer(testId, mockRunner);
        assert.equal(typeof probes.add, 'function', "The module as the add method");
        assert.equal(typeof probes.getProbes, 'function', "The module as the getProbes method");
        assert.equal(typeof probes.getQueue, 'function', "The module as the getQueue method");
        assert.equal(typeof probes.push, 'function', "The module as the push method");
        assert.equal(typeof probes.flush, 'function', "The module as the flush method");
        assert.equal(typeof probes.start, 'function', "The module as the start method");
        assert.equal(typeof probes.stop, 'function', "The module as the stop method");
    });

    QUnit.module('probes');

    QUnit.test('normal validation', function(assert) {
        QUnit.expect(5);

        var probes = probeOverseer(testId, mockRunner);

        assert.throws(function() {
            probes.add();
        }, TypeError, "A probe is an object");

        assert.throws(function() {
            probes.add({});
        }, TypeError, "A probe is an object with a predefined format");

        assert.throws(function() {
            probes.add({
                name: true
            });
        }, TypeError, "A probe is an object with a valid name");

        assert.throws(function() {
            probes.add({
                name: 'foo'
            });
        }, TypeError, "A probe is an object with events");

        probes.add({
            name: 'foo',
            events: ['bar']
        });

        assert.throws(function() {
            probes.add({
                name: 'foo',
                events: ['bar']
            });
        }, TypeError, "A probe cannot be added twice");

    });

    QUnit.test('latency validation', function(assert) {
        QUnit.expect(4);

        var probes = probeOverseer(testId, mockRunner);

        assert.throws(function() {
            probes.add({});
        }, TypeError, "A probe is an object with a predefined format");

        assert.throws(function() {
            probes.add({
                name: 'foo',
                latency: true
            });
        }, TypeError, "A latency probe must have events");

        assert.throws(function() {
            probes.add({
                name: 'foo',
                latency: true,
                events: ['bar']
            });
        }, TypeError, "A latency probe must have start and stop events");

        assert.throws(function() {
            probes.add({
                name: 'foo',
                latency: true,
                startEvents: [],
                stopEvents: []
            });
        }, TypeError, "A latency probe must have start and stop events defined");

        probes.add({
            name: 'foo',
            latency: true,
            startEvents: ['init'],
            stopEvents: ['finish']
        });
    });

    QUnit.test('add and get', function(assert) {
        QUnit.expect(3);

        var probes = probeOverseer(testId, mockRunner);
        var p1 = {
            name: 'foo',
            latency: true,
            startEvents: ['init'],
            stopEvents: ['finish']
        };
        var p2 = {
            name: 'bar',
            events: ['ready']
        };

        assert.deepEqual(probes.add(p1), probes, 'The add method chains');
        assert.deepEqual(probes.add(p2), probes, 'The add method chains');
        assert.deepEqual(probes.getProbes(), [p1, p2], 'The probes are added correclty');
    });

    QUnit.test('reformat events', function(assert) {
        QUnit.expect(3);

        var probes = probeOverseer(testId, mockRunner);
        var p1 = {
            name: 'foo',
            latency: true,
            startEvents: 'init',
            stopEvents: 'finish'
        };
        var p2 = {
            name: 'bar',
            events: 'ready'
        };

        probes.add(p1)
            .add(p2);

        assert.deepEqual(probes.getProbes()[0].startEvents, ['init'], 'Events are wrapped in an array');
        assert.deepEqual(probes.getProbes()[0].stopEvents, ['finish'], 'Events are wrapped in an array');
        assert.deepEqual(probes.getProbes()[1].events, ['ready'], 'Events are wrapped in an array');
    });

    QUnit.module('collection', {
        setup: function() {
            runnerFactory.clearProviders();
        }
    });

    QUnit.asyncTest('simple', function(assert) {
        QUnit.expect(12);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: _.noop
        });

        var runner = runnerFactory('foo');

        var probes = probeOverseer(testId, runner);

        probes.add({
            name: 'test-ready',
            events: 'ready',
            capture: function(testRunner) {
                assert.equal(typeof testRunner, 'object', 'The runner is given in parameter');
                assert.deepEqual(testRunner, runner, 'The runner instance is given in parameter');
                return {
                    'foo': 'bar'
                };
            }
        });
        probes.start().then(function(){

            var creation = Date.now() / 1000;
            var init;
            runner
                .on('init', function() {
                    init = Date.now() / 1000;
                })
                .after('ready', function() {
                    setTimeout(function() {
                        probes.getQueue().then(function(queue) {

                            assert.equal(queue.length, 1, 'The queue contains an entry');
                            assert.equal(typeof queue[0], 'object', 'The queue entry is an object');
                            assert.equal(typeof queue[0].id, 'string', 'The queue entry contains an id');
                            assert.equal(typeof queue[0].timestamp, 'number', 'The queue entry contains a timestamp');
                            assert.ok(queue[0].timestamp >= creation && creation > 0, 'The timestamp is superior to the test creation');
                            assert.ok(queue[0].timestamp >= init && init > 0, 'The timestamp is superior or equal to the test init');
                            assert.equal(typeof queue[0].timezone, 'string', 'The queue entry contains a timezone');
                            assert.ok( new RegExp('^[\+\-]{1}[0-9]{2}:[0-9]{2}$').test(queue[0].timezone) , 'The timezone is formatted correclty');
                            assert.equal(queue[0].type, 'test-ready', 'The entry type is correct');
                            assert.deepEqual(queue[0].context, {
                                foo: 'bar'
                            }, 'The entry context is correct');

                            probes.stop();

                            QUnit.start();

                        }).catch(function(err) {
                            assert.ok(false, err);
                            QUnit.start();
                        });
                    }, 200); //time to write in the db
                })
                .init();
        });
    });

    QUnit.asyncTest('latency', function(assert) {
        QUnit.expect(16);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: _.noop
        });

        var runner = runnerFactory('foo');

        var probes = probeOverseer(testId, runner);

        probes.add({
            name: 'test-latency',
            latency: true,
            startEvents: ['ready'],
            stopEvents: ['finish'],
            capture: function(testRunner) {
                assert.equal(typeof testRunner, 'object', 'The runner is given in parameter');
                assert.deepEqual(testRunner, runner, 'The runner instance is given in parameter');
                return {
                    'foo': 'bar'
                };
            }
        });
        probes.start().then(function(){

            var creation = Date.now() / 1000;
            var init;
            runner
                .on('init', function() {
                    init = Date.now() / 1000;
                })
                .after('ready', function() {
                    setTimeout(function() {
                        runner.finish();
                    }, 50);
                })
                .after('finish', function() {

                    setTimeout(function() {
                        probes.getQueue().then(function(queue) {

                            assert.equal(queue.length, 2, 'The queue contains the two entries');
                            var startEntry = queue[0];
                            var stopEntry = queue[1];

                            assert.equal(typeof startEntry, 'object', 'The start entry is an object');
                            assert.equal(typeof startEntry.id, 'string', 'The start entry contains an id');
                            assert.equal(typeof startEntry.timestamp, 'number', 'The start entry contains a timestamp');
                            assert.equal(startEntry.type, 'test-latency', 'The entry type is correct');
                            assert.deepEqual(startEntry.context, {
                                foo: 'bar'
                            }, 'The entry context is correct');
                            assert.ok(startEntry.timestamp >= creation && creation > 0, 'The timestamp is superior to the test creation');
                            assert.ok(startEntry.timestamp >= init && init > 0, 'The timestamp is superior or equal to the test init');

                            assert.equal(typeof queue[0].timezone, 'string', 'The queue entry contains a timezone');
                            assert.ok(/^[\+\-]{1}[0-9]{2}:[0-9]{2}$/.test(queue[0].timezone), 'The timezone is formatted correclty');

                            assert.equal(typeof stopEntry, 'object', 'The stop entry is an object');
                            assert.equal(stopEntry.id, startEntry.id, 'string', 'The stop entry id is the same than the start entry');

                            probes.stop();

                            QUnit.start();

                        }).catch(function(err) {
                            assert.ok(false, err);

                            QUnit.start();
                        });
                    }, 200); //time to write in the db
                })
                .init();
        });
    });

    QUnit.asyncTest('flush', function(assert) {
        QUnit.expect(3);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: _.noop
        });

        var runner = runnerFactory('foo');

        var probes = probeOverseer(testId, runner);

        probes.add({
            name: 'foo',
            events: 'foo'
        });
        probes.start().then(function(){

            runner
                .on('ready', function() {
                    runner.trigger('foo')
                        .trigger('foo')
                        .trigger('foo');
                })
                .after('ready', function() {


                    setTimeout(function() {
                        probes.getQueue()
                            .then(function(queue) {
                                assert.equal(queue.length, 3, 'The queue contains 3 entries');
                            })
                            .then(function() {
                                return probes.flush().then(function(flushed) {
                                    assert.equal(flushed.length, 3, 'The queue contains 3 entries');
                                });
                            })
                            .then(function() {
                                return probes.getQueue().then(function(queue) {
                                    assert.equal(queue.length, 0, 'The queue is empty now');
                                });
                            })
                            .then(function() {
                                probes.stop();
                                QUnit.start();
                            }).catch(function(err) {
                                assert.ok(false, err);

                                QUnit.start();
                            });
                    }, 200); //time to write in the db
                })
                .init();
        });
    });

    QUnit.asyncTest('stop', function(assert) {
        QUnit.expect(2);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: _.noop
        });

        var runner = runnerFactory('foo');

        var probes = probeOverseer(testId, runner);

        probes.add({
            name: 'foo',
            events: 'foo'
        });
        probes.start().then(function(){

            runner
                .on('ready', function() {
                    runner.trigger('foo')
                        .trigger('foo')
                        .trigger('foo');
                })
                .after('ready', function() {
                    setTimeout(function() {

                        probes.getQueue()
                            .then(function(queue) {
                                assert.equal(queue.length, 3, 'The queue contains 3 entries');
                            })
                            .then(function() {
                                return probes.stop().then(function(){
                                    runner.trigger('foo');
                                });
                            })
                            .then(function() {
                                setTimeout(function() {
                                    probes.getQueue().then(function(queue) {
                                        assert.equal(queue, null, 'The queue is not there');
                                        QUnit.start();
                                    });
                                }, 150);
                            });
                    }, 150);
                })
                .init();
        });
    });
});

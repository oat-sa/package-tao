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
    'core/polling'
], function($, _, polling) {
    'use strict';

    QUnit.module('polling');


    QUnit.test('module', 3, function(assert) {
        assert.equal(typeof polling, 'function', "The polling module exposes a function");
        assert.equal(typeof polling(), 'object', "The polling factory produces an object");
        assert.notStrictEqual(polling(), polling(), "The polling factory provides a different object on each call");
    });


    var testReviewApi = [
        { name : 'async', title : 'async' },
        { name : 'next', title : 'next' },
        { name : 'start', title : 'start' },
        { name : 'stop', title : 'stop' },
        { name : 'setInterval', title : 'setInterval' },
        { name : 'getInterval', title : 'getInterval' },
        { name : 'setAction', title : 'setAction' },
        { name : 'getAction', title : 'getAction' },
        { name : 'setContext', title : 'setContext' },
        { name : 'getContext', title : 'getContext' },
        { name : 'getIteration', title : 'getIteration' },
        { name : 'setMax', title : 'setMax' },
        { name : 'getMax', title : 'getMax' },
        { name : 'is', title : 'is' }
    ];

    QUnit
        .cases(testReviewApi)
        .test('instance API ', function(data, assert) {
            var instance = polling();
            assert.equal(typeof instance[data.name], 'function', 'The polling instance exposes a "' + data.title + '" function');
        });


    QUnit.test('API', function(assert) {
        var instance = polling();
        var action = function() {};
        var interval = 250;
        var context = {};

        assert.equal(instance.getInterval(), 60000, 'The polling instance has set a default value for the interval');
        assert.equal(instance.getContext(), instance, 'The polling instance has set a default value for the call context');
        assert.equal(instance.getAction(), null, 'The polling instance has no action callback for now');

        assert.equal(instance.setInterval(interval), instance, 'The method setInterval returns the instance');
        assert.equal(instance.setContext(context), instance, 'The method setContext returns the instance');
        assert.equal(instance.setAction(action), instance, 'The method setAction returns the instance');

        assert.equal(instance.getInterval(), interval, 'The polling instance has set the right value for the interval');
        assert.equal(instance.getContext(), context, 'The polling instance has set the right value for the call context');
        assert.equal(instance.getAction(), action, 'The polling instance has set the right action callback');
    });


    QUnit.asyncTest('events', function(assert) {
        var instance = polling();

        var interval = 50;
        var context = {
            step : 0
        };

        var action = function() {
            var async;

            assert.equal(instance.is('processing'), true, 'The instance must be in state processing');

            switch (this.step ++) {
                case 0:
                    async = instance.async();
                    setTimeout(function() {
                        async.resolve();
                    }, interval);
                    break;

                case 1:
                    async = instance.async();
                    setTimeout(function() {
                        async.reject();
                    }, interval);
                    break;

                case 2:
                    instance.stop();
                    break;

                case 3:
                    async = instance.async();
                    setTimeout(function() {
                        async.reject();
                    }, interval);
                    break;
            }
        };

        instance.on('custom', function() {
            assert.ok(true, 'The polling instance can handle custom events');
            QUnit.start();
        });

        instance.on('call', function() {
            assert.ok(true, 'The polling instance triggers event when the action is called [step ' + context.step + ']');
            QUnit.start();
        });

        instance.on('resolved', function() {
            assert.equal(instance.is('processing'), false, 'The instance must not be in state processing');
            assert.equal(instance.is('pending'), true, 'The instance must be in state pending');
            assert.ok(true, 'The polling instance triggers event when the action is validated in async mode [step ' + context.step + ']');
            QUnit.start();
        });

        instance.on('rejected', function() {
            if (4 !== context.step) {
                assert.equal(instance.is('processing'), false, 'The instance must not be in state processing');
                assert.equal(instance.is('stopped'), true, 'The instance must be in state stopped');
            }
            assert.ok(true, 'The polling instance triggers event when the action is canceled in async mode [step ' + context.step + ']');
            QUnit.start();
        });

        instance.on('async', function(cb) {
            assert.ok(true, 'The polling instance triggers event when the action is set to async mode [step ' + context.step + ']');
            assert.equal(typeof cb, 'object', 'The first parameter of the async event is the resolve object');
            assert.ok(cb.resolve, 'The first parameter of the async event has a resolve method');
            assert.ok(cb.reject, 'The first parameter of the async event has a reject method');
            QUnit.start();
        });

        instance.on('next', function() {
            assert.equal(instance.is('stopped'), false, 'The instance must not be in state stopped');
            assert.ok(true, 'The polling instance triggers event when the action is triggered immediately [step ' + context.step + ']');
            QUnit.start();
        });

        instance.on('start', function() {
            assert.equal(instance.is('pending'), true, 'The instance must be in state pending');
            assert.equal(instance.is('stopped'), false, 'The instance must not be in state stopped');
            assert.ok(true, 'The polling instance triggers event when the polling is started [step ' + context.step + ']');
            QUnit.start();
        });

        instance.on('stop', function() {
            assert.equal(instance.is('pending'), false, 'The instance must not be in state pending');
            assert.equal(instance.is('stopped'), true, 'The instance must be in state stopped');
            assert.ok(true, 'The polling instance triggers event when the polling is stopped [step ' + context.step + ']');
            QUnit.start();

            if (2 === context.step || 3 === context.step) {
                instance.next();
            }
        });

        instance.on('setinterval', function(val) {
            assert.ok(true, 'The polling instance triggers event when the interval is changed');
            assert.equal(val, interval, 'The first parameter of the setinterval event is the changed value');
            QUnit.start();
        });

        instance.on('setaction', function(val) {
            assert.ok(true, 'The polling instance triggers event when the action to call is changed');
            assert.equal(val, action, 'The first parameter of the setaction event is the changed value');
            QUnit.start();
        });

        instance.on('setcontext', function(val) {
            assert.ok(true, 'The polling instance triggers event when the call context is changed');
            assert.equal(val, context, 'The first parameter of the setcontext event is the changed value');
            QUnit.start();
        });

        QUnit.stop(19);

        instance.trigger('custom');

        instance.setInterval(interval);
        instance.setContext(context);
        instance.setAction(action);
        instance.start();
    });


    QUnit.asyncTest('limit', function(assert) {
        var instance = polling();
        var max = 3;
        var interval = 50;
        var context = {
            step : 0
        };

        var action = function() {
            this.step ++;
            assert.equal(instance.getIteration(), this.step, 'The iteration is counted #' + this.step);
            assert.ok(instance.getIteration() <= max, 'The number of iterations is under the max #' + this.step);
            QUnit.start();
        };

        instance.on('stop', function() {
            assert.ok(true, 'The polling instance is stopped');
            QUnit.start();
        });

        QUnit.stop(3);

        instance.setInterval(interval);
        instance.setContext(context);
        instance.setAction(action);
        instance.setMax(max);
        instance.start();
    });
});

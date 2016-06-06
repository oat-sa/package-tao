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
    'jquery',
    'lodash',
    'taoQtiTest/runner/plugins/controls/timer/timerComponent'
], function($, _, timerComponentFactory) {
    'use strict';

    QUnit.module('API');

    QUnit.test('factory', function(assert) {
        assert.equal(typeof timerComponentFactory, 'function', "The module exposes a function");

        assert.throws(function(){
            timerComponentFactory();
        }, TypeError, 'The component needs to be configured');

        assert.throws(function(){
            timerComponentFactory('ffo');
        }, TypeError, 'The component needs a config object');

        assert.throws(function(){
            timerComponentFactory({});
        }, TypeError, 'The component needs an id');

        assert.throws(function(){
            timerComponentFactory({
                id : ''
            });
        }, TypeError, 'The component needs a valid id');

        assert.throws(function(){
            timerComponentFactory({
                id : 'foo'
            });
        }, TypeError, 'The component needs a type');

        assert.throws(function(){
            timerComponentFactory({
                id : 'foo',
                type : 'bar'
            });
        }, TypeError, 'The component needs a valid type');

        var config = {
            id : 'foo',
            type : 'assessmentTest'
        };
        var timerComponent = timerComponentFactory(config);

        assert.equal(typeof timerComponent, 'object', 'The factory creates an object');
        assert.notDeepEqual(timerComponent, timerComponentFactory(config), 'The factory creates new objects');
    });

    var pluginApi = [
        { name : 'init', title : 'init' },
        { name : 'render', title : 'render' },
        { name : 'destroy', title : 'destroy' },
        { name : 'refresh', title : 'refresh' },
        { name : 'warn', title : 'warn' },
        { name : 'id', title : 'id' },
        { name : 'val', title : 'val' },
        { name : 'running', title : 'running' }
    ];

    QUnit
        .cases(pluginApi)
        .test('component method ', function(data, assert) {
            QUnit.expect(1);

            var config = {
                id : 'foo',
                type : 'assessmentTest'
            };
            var timer = timerComponentFactory(config);
            assert.equal(typeof timer[data.name], 'function', 'The component exposes a "' + data.name + '" function');
        });


    QUnit.module('lifecycle');

    QUnit.test('init', function(assert) {
        QUnit.expect(12);

        var config = {
            id : 'foo',
            type : 'assessmentTest',
            label : 'Foo bAr'
        };

        var timer = timerComponentFactory(config);
        assert.equal(typeof timer, 'object', 'The factory creates an object');
        assert.ok(typeof timer.$element === 'undefined', 'The timer has no internal element');

        var result = timer.init();
        assert.deepEqual(result, timer, 'The init method is chainable');

        //check DOM
        assert.ok(timer.$element instanceof $, 'The timer has an element');
        assert.equal(timer.$element.length, 1, 'The timer element is not empty');
        assert.equal(timer.$element.parent().length, 0, 'The element is detached from the DOM');

        assert.ok(timer.$element.hasClass('qti-timer'), 'The timer element has the correct css class');
        assert.ok(timer.$element.hasClass('qti-timer__type-'+config.type), 'The timer element has the correct typed css class');

        assert.equal($('.qti-timer_label', timer.$element).length, 1, 'The element contains a label element');
        assert.equal($('.qti-timer_label', timer.$element).text(), config.label, 'The element contains the configured label');
        assert.equal($('.qti-timer_time', timer.$element).length, 1, 'The element contains a time value element');
        assert.equal($('.qti-timer_time', timer.$element).text(), '', 'The element contains not time value');
    });

    QUnit.test('render', function(assert) {
        QUnit.expect(7);

        var $container = $('#qunit-fixture .timer-box');
        var config = {
            id : 'moo',
            type : 'assessmentSection',
            label : 'Moo nAr'
        };

        assert.equal($container.length, 1, 'The container exists');

        var timer = timerComponentFactory(config);
        timer.init();

        assert.equal(timer.$element.length, 1, 'The timer element is not empty');
        assert.equal(timer.$element.parent().length, 0, 'The element is detached from the DOM');

        var result = timer.render($container);
        assert.deepEqual(result, timer, 'The render method is chainable');

        assert.equal(timer.$element.parent().length, 1, 'The element is now attached to the DOM');
        assert.equal($('.qti-timer_label', $container).length, 1, 'The element contains a label element');
        assert.equal($('.qti-timer_label', $container).text(), config.label, 'The element contains the configured label');
    });

    QUnit.test('destroy', function(assert) {
        QUnit.expect(8);

        var $container = $('#qunit-fixture .timer-box');
        var config = {
            id : 'doo',
            type : 'assessmentSection',
            label : 'Doo nMAr'
        };

        assert.equal($container.length, 1, 'The container exists');

        var timer = timerComponentFactory(config);
        timer
            .init()
            .render($container);

        //check DOM
        assert.equal(timer.$element.length, 1, 'The timer element is not empty');
        assert.equal(timer.$element.parent().length, 1, 'The element is now attached to the DOM');
        assert.equal($('.qti-timer', $container).length, 1, 'The container has the element');
        assert.equal($('.qti-timer_label', $container).length, 1, 'The element contains a label element');
        assert.equal($('.qti-timer_label', $container).text(), config.label, 'The element contains the configured label');

        var result = timer.destroy();
        assert.deepEqual(result, timer, 'The destroy method is chainable');

        assert.equal($container.children().length, 0, 'There is no attached element anymore');
    });

    QUnit.module('behavior');

    QUnit.test('state', function(assert) {
        QUnit.expect(4);

        var config = {
            id : 'boo',
            type : 'assessmentSection'
        };

        var timer = timerComponentFactory(config);

        assert.equal(timer.id(), config.id, 'The identifier method returns the right identifier');

        assert.equal(timer.running(), true, 'The running state is true by default');
        assert.deepEqual(timer.running(false), timer, 'The running method chains in setter mode');
        assert.equal(timer.running(), false, 'The running state is modified correctly');
    });

    QUnit.asyncTest('update', function(assert) {
        QUnit.expect(8);

        var $container = $('#qunit-fixture .timer-box');
        var config = {
            id : 'doo',
            type : 'assessmentSection',
            label : 'Doo nMAr',
            remaining : 30 * 1000
        };

        assert.equal($container.length, 1, 'The container exists');

        var timer = timerComponentFactory(config);
        timer
            .init()
            .render($container);

        assert.equal($('.qti-timer_time', timer.$element).length, 1, 'The element contains a time value element');
        assert.equal($('.qti-timer_time', timer.$element).text(), '', 'The element contains not time value');

        assert.equal(timer.val(), config.remaining, 'The given value match the remaining time');

        var result = timer.refresh();
        assert.deepEqual(result, timer, 'The refresh method is chainable');

        _.delay(function(){
            assert.equal($('.qti-timer_time', timer.$element).text(), '00:00:30', 'The element contains now the time value');

            var result = timer.val(29 * 1000);
            assert.deepEqual(result, timer, 'The val method is chainable in setter mode');

            timer.refresh();
            _.delay(function(){
                assert.equal($('.qti-timer_time', timer.$element).text(), '00:00:29', 'The element contains the updated time value');
                QUnit.start();
            }, 50);
        }, 10);
    });

    QUnit.test('warn', function(assert) {
        QUnit.expect(5);

        var $container = $('#qunit-fixture .timer-box');
        var config = {
            id : 'woo',
            type : 'assessmentItemRef',
            remaining: 1 * 60 * 1000,
            warning: 5 * 60 * 1000
        };

        assert.equal($container.length, 1, 'The container exists');

        var timer = timerComponentFactory(config);
        timer.init()
             .render($container)
             .refresh();

        assert.ok( ! $('.qti-timer', $container).hasClass('qti-timer__warning'), 'The element does not display in warning');

        var result = timer.warn();
        assert.equal(typeof result, 'string', 'The warn result is a string');
        assert.equal(result, "Warning – You have a minute remaining to complete this item.", 'The warn result is a correct');
        assert.ok($('.qti-timer', $container).hasClass('qti-timer__warning'), 'The element display in warning');
    });

});

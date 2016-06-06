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
 * Test the areaBroker
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'taoTests/runner/areaBroker',
], function ($, areaBroker){
    'use strict';

    var fixture = '#qunit-fixture';


    QUnit.module('API');

    QUnit.test('module', function (assert){
        QUnit.expect(1);

        assert.equal(typeof areaBroker, 'function', "The module exposes a function");
    });

    QUnit.test('factory', function (assert){
        QUnit.expect(7);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);
        var $content    = $('.content', $container);
        var $toolbox    = $('.toolbox', $container);
        var $navigation = $('.navigation', $container);
        var $control    = $('.control', $container);
        var $panel      = $('.panel', $container);
        var $header     = $('.header', $container);
        var mapping    = {
            'content'    : $content,
            'toolbox'    : $toolbox,
            'navigation' : $navigation,
            'control'    : $control,
            'header'     : $header,
            'panel'      : $panel
        };

        assert.ok($container.length,  "The container exists");

        assert.throws(function(){
            areaBroker();
        }, TypeError, 'A broker must be created with a container');

        assert.throws(function(){
            areaBroker('foo');
        }, TypeError, 'A broker must be created with an existing container');

        assert.throws(function(){
            areaBroker($container);
        }, TypeError, 'A broker must be created with an area mapping');

        assert.throws(function(){
            areaBroker($container, {
                content : $content
            });
        }, TypeError, 'A broker must be created with an full area mapping');


        assert.equal(typeof areaBroker($container, mapping), 'object', "The factory creates an object");
        assert.notEqual(areaBroker($container, mapping), areaBroker($container, mapping), "The factory creates new instances");
    });

    QUnit.test('broker api', function (assert){
        QUnit.expect(4);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);
        var $content    = $('.content', $container);
        var $toolbox    = $('.toolbox', $container);
        var $navigation = $('.navigation', $container);
        var $control    = $('.control', $container);
        var $panel      = $('.panel', $container);
        var $header     = $('.header', $container);
        var mapping    = {
            'content'    : $content,
            'toolbox'    : $toolbox,
            'navigation' : $navigation,
            'control'    : $control,
            'header'     : $header,
            'panel'      : $panel
        };

        assert.ok($container.length,  "The container exists");

        var broker = areaBroker($container, mapping);
        assert.equal(typeof broker.defineAreas, 'function', 'The broker has a defineAreas function');
        assert.equal(typeof broker.getContainer, 'function', 'The broker has a getContainer function');
        assert.equal(typeof broker.getArea, 'function', 'The broker has a getArea function');
    });

    QUnit.module('Area mapping');

    QUnit.test('define mapping', function (assert){
        QUnit.expect(9);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        var $content    = $('.content', $container);
        var $toolbox    = $('.toolbox', $container);
        var $navigation = $('.navigation', $container);
        var $control    = $('.control', $container);
        var $panel      = $('.panel', $container);
        var $header     = $('.header', $container);
        var mapping    = {
            'content'    : $content,
            'toolbox'    : $toolbox,
            'navigation' : $navigation,
            'control'    : $control,
            'header'     : $header,
            'panel'      : $panel
        };

        var broker = areaBroker($container, mapping);

        assert.throws(function(){
            broker.defineAreas();
        }, TypeError, 'requires a mapping object');

        assert.throws(function(){
            broker.defineAreas({});
        }, TypeError, 'required mapping missing');

        assert.throws(function(){
            broker.defineAreas({
                'content': $content,
                'navigation' : $navigation
            });
        }, TypeError, 'required mapping incomplete');

        broker.defineAreas(mapping);

        assert.deepEqual(broker.getArea('content'), $content, 'The area match');
        assert.deepEqual(broker.getArea('toolbox'), $toolbox, 'The area match');
        assert.deepEqual(broker.getArea('navigation'), $navigation, 'The area match');
        assert.deepEqual(broker.getArea('control'), $control, 'The area match');
        assert.deepEqual(broker.getArea('panel'), $panel, 'The area match');
    });

    QUnit.test('aliases', function (assert){
        QUnit.expect(6);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        var $content    = $('.content', $container);
        var $toolbox    = $('.toolbox', $container);
        var $navigation = $('.navigation', $container);
        var $control    = $('.control', $container);
        var $panel      = $('.panel', $container);
        var $header     = $('.header', $container);
        var mapping    = {
            'content'    : $content,
            'toolbox'    : $toolbox,
            'navigation' : $navigation,
            'control'    : $control,
            'header'     : $header,
            'panel'      : $panel
        };
        var broker = areaBroker($container, mapping);

        assert.deepEqual(broker.getContentArea(), $content, 'The area match');
        assert.deepEqual(broker.getToolboxArea(), $toolbox, 'The area match');
        assert.deepEqual(broker.getNavigationArea(), $navigation, 'The area match');
        assert.deepEqual(broker.getControlArea(), $control, 'The area match');
        assert.deepEqual(broker.getPanelArea(), $panel, 'The area match');
    });


    QUnit.module('container');

    QUnit.test('retrieve', function (assert){
        QUnit.expect(2);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);
        var $content    = $('.content', $container);
        var $toolbox    = $('.toolbox', $container);
        var $navigation = $('.navigation', $container);
        var $control    = $('.control', $container);
        var $panel      = $('.panel', $container);
        var $header     = $('.header', $container);
        var mapping    = {
            'content'    : $content,
            'toolbox'    : $toolbox,
            'navigation' : $navigation,
            'control'    : $control,
            'header'     : $header,
            'panel'      : $panel
        };

        assert.ok($container.length,  "The container exists");

        var broker = areaBroker($container, mapping);

        assert.deepEqual(broker.getContainer(), $container, 'The container match');
    });
});

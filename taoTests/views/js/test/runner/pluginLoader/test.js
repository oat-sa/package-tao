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
 * Test the test plugin
 * @author Sam <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoTests/runner/pluginLoader',
    'core/promise',
], function (_, pluginLoader, Promise){
    'use strict';


    QUnit.module('API');

    QUnit.test('module', function (assert){
        QUnit.expect(3);

        assert.equal(typeof pluginLoader, 'function', "The plugin loader exposes a function");
        assert.equal(typeof pluginLoader(), 'object', "The plugin loader produces an object");
        assert.notStrictEqual(pluginLoader(), pluginLoader(), "The plugin loader provides a different object on each call");
    });

    QUnit.test('loader methods', function (assert){
        QUnit.expect(6);
        var loader = pluginLoader();

        assert.equal(typeof loader, 'object', "The loader is an object");
        assert.equal(typeof loader.add, 'function', "The loader exposes the add method");
        assert.equal(typeof loader.append, 'function', "The loader exposes the append method");
        assert.equal(typeof loader.prepend, 'function', "The loader exposes the prepend method");
        assert.equal(typeof loader.load, 'function', "The loader exposes the load method");
        assert.equal(typeof loader.getPlugins, 'function', "The loader exposes the getPlugins method");

    });


    QUnit.module('required');

    QUnit.test('required plugin format', function (assert){
        QUnit.expect(4);

        assert.throws(function(){
            pluginLoader({ 12 : _.noop });
        }, TypeError, 'Wrong category format');

        assert.throws(function(){
            pluginLoader({ 'foo' : true });
        }, TypeError, 'The plugin list must be an array');

        assert.throws(function(){
            pluginLoader({ 'foo' : [true] });
        }, TypeError, 'The plugin list must be an array of function');

        assert.throws(function(){
            pluginLoader({ 'foo' : ['true', _.noop] });
        }, TypeError, 'The plugin list must be an array with only functions');

        var loader = pluginLoader({
            foo : [_.noop],
            bar : [_.noop, _.noop]
        });
    });

    QUnit.test('required plugin loading', function (assert){
        QUnit.expect(5);

        var a = function a (){ return 'a'; };
        var b = function b (){ return 'b'; };
        var c = function c (){ return 'c'; };
        var plugins = {
            foo : [a],
            bar : [b, c]
        };

        var loader = pluginLoader(plugins);

        assert.equal(typeof loader, 'object', "The loader is an object");
        assert.deepEqual(loader.getCategories(), ['foo', 'bar'], "The plugins categories are correct");
        assert.deepEqual(loader.getPlugins(), [a, b, c], "The plugins have been registered");
        assert.deepEqual(loader.getPlugins('foo'), plugins.foo, "The plugins are registered under the right category");
        assert.deepEqual(loader.getPlugins('bar'), plugins.bar, "The plugins are registered under the right category");
    });


    QUnit.module('dynamic');

    QUnit.test('add plugin module format', function (assert){
        QUnit.expect(3);

        var loader = pluginLoader();

        assert.equal(typeof loader, 'object', "The loader is an object");

        assert.throws(function(){
            loader.add(12);
        }, TypeError, 'A module is a string');

        assert.throws(function(){
            loader.add('12', true);
        }, TypeError, 'A category is a string');

        loader.add('foo', 'foo');
    });

    QUnit.asyncTest('load a plugin', function (assert){
        QUnit.expect(5);

        var loader = pluginLoader();

        assert.equal(typeof loader, 'object', "The loader is an object");

        assert.deepEqual(loader.append('taoTests/test/runner/pluginLoader/mockPlugin', 'mock'), loader, 'The loader chains');

        var p = loader.load();

        assert.ok(p instanceof Promise, "The load method returns a Promise");
        assert.deepEqual(loader.getPlugins('mock'), [], 'The loader mock category is empty');

        p.then(function(){
            assert.equal(loader.getPlugins('mock').length, 1, 'The mock category contains now a plugin');
            QUnit.start();
        }).catch(function(e){
            assert.ok(false, e);
            QUnit.start();
        });
    });
});

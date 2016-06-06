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
    'taoTests/runner/plugin',
    'core/eventifier'
], function (_, pluginFactory, eventifier){
    'use strict';

    var mockProvider = {
        name : 'foo',
        init : _.noop
    };

    var samplePluginDefaults = {
        a : false,
        b : 10
    };

    var mockRunner = {
        trigger : _.noop
    };

    QUnit.module('plugin');

    QUnit.test('module', function (assert){
        QUnit.expect(3);

        assert.equal(typeof pluginFactory, 'function', "The plugin module exposes a function");
        assert.equal(typeof pluginFactory(mockProvider), 'function', "The plugin factory produces a function");
        assert.notStrictEqual(pluginFactory(mockProvider), pluginFactory(mockProvider), "The plugin factory provides a different object on each call");
    });

    QUnit.test('provider format', function (assert){
        QUnit.expect(4);

        assert.throws(function(){
            pluginFactory();
        }, TypeError, 'A provider should be an object');

        assert.throws(function(){
            pluginFactory({});
        }, TypeError, 'A plugin provider should have a name');

        assert.throws(function(){
            pluginFactory({ name : ''});
        }, TypeError, 'A plugin provider should have a valid name');

        assert.throws(function(){
            pluginFactory({ name : 'foo'});
        }, TypeError, 'A plugin provider should have a init function');


        pluginFactory({
            name : 'foo',
            init : _.noop
        });
    });

    QUnit.test('create a plugin', function (assert){
        QUnit.expect(20);

        var myPlugin = pluginFactory(mockProvider, samplePluginDefaults);

        assert.equal(typeof myPlugin(), 'object', "My plugin factory produce a plugin instance object");
        assert.notStrictEqual(myPlugin(), myPlugin(), "My plugin factory provides different object on each call");

        var _config2 = {
            a : true,
            b : 999
        };

        var instance1 = myPlugin();
        assert.equal(typeof instance1.init, 'function', 'The plugin instance has also the default function init');
        assert.equal(typeof instance1.getAreaBroker, 'function', 'The plugin instance has also the default function getAreaBroker');
        assert.equal(typeof instance1.finish, 'function', 'The plugin instance has also the default function finish');
        assert.equal(typeof instance1.render, 'function', 'The plugin instance has also the default function render');
        assert.equal(typeof instance1.destroy, 'function', 'The plugin instance has also the default function destroy');
        assert.equal(typeof instance1.show, 'function', 'The plugin instance has also the default function show');
        assert.equal(typeof instance1.hide, 'function', 'The plugin instance has also the default function hide');
        assert.equal(typeof instance1.enable, 'function', 'The plugin instance has also the default function enable');
        assert.equal(typeof instance1.disable, 'function', 'The plugin instance has also the default function disable');
        assert.equal(typeof instance1.setState, 'function', 'The plugin instance has also the default function setState');
        assert.equal(typeof instance1.getState, 'function', 'The plugin instance has also the default function getState');
        assert.equal(typeof instance1.getConfig, 'function', 'The plugin instance has also the default function getConfig');
        assert.equal(typeof instance1.setConfig, 'function', 'The plugin instance has also the default function setConfig');
        assert.equal(typeof instance1.getName, 'function', 'The plugin instance has also the default function getName');

        // check default config
        var config1 = instance1.getConfig();
        assert.equal(config1.a, samplePluginDefaults.a, 'instance1 inherits the default config');
        assert.equal(config1.b, samplePluginDefaults.b, 'instance1 inherit the default config');

        // check overwritten config
        var instance2 = myPlugin({}, {}, _config2);
        var config2 = instance2.getConfig();
        assert.equal(config2.a, _config2.a, 'instance2 has new config value');
        assert.equal(config2.b, _config2.b, 'instance2 has new config value');

    });

    QUnit.test('call plugin methods', function (assert){
        QUnit.expect(11);

        var samplePluginImpl = {
            name : 'samplePluginImpl',
            init : function (){
                var config = this.getConfig();
                assert.ok(true, 'called init');

                assert.equal(config.a, samplePluginDefaults.a, 'instance1 inherits the default config');
                assert.equal(config.b, samplePluginDefaults.b, 'instance1 inherit the default config');
            },
            render : function (){
                assert.ok(true, 'called render');
            },
            finish : function (){
                assert.ok(true, 'called finish');
            },
            destroy : function (){
                assert.ok(true, 'called destory');
            },
            show : function (){
                assert.ok(true, 'called show');
            },
            hide : function (){
                assert.ok(true, 'called hide');
            },
            enable : function (){
                assert.ok(true, 'called enable');
            },
            disable : function (){
                assert.ok(true, 'called disable');
            }
        };

        var myPlugin = pluginFactory(samplePluginImpl, samplePluginDefaults);

        assert.equal(typeof myPlugin(), 'object', "My plugin factory produce a plugin instance object");

        var instance1 = myPlugin(mockRunner);
        instance1.init();
        instance1.render();
        instance1.hide();
        instance1.show();
        instance1.disable();
        instance1.enable();
        instance1.finish();
        instance1.destroy();
    });

    QUnit.asyncTest('state', function (assert){
        QUnit.expect(14);

        var myPlugin = pluginFactory(mockProvider);

        assert.equal(typeof myPlugin(), 'object', "My plugin factory produce a plugin instance object");

        var instance1 = myPlugin(mockRunner);

        assert.throws(function(){
            instance1.setState({}, false);
        }, TypeError, 'The state must have a valid name');

        //custom state : active
        assert.strictEqual(instance1.getState('active'), false, 'no state set by default');
        instance1.setState('active', true);
        assert.strictEqual(instance1.getState('active'), true, 'active state set');
        instance1.setState('active', false);
        assert.strictEqual(instance1.getState('active'), false, 'no state set by default');

        //built-in state init:
        assert.strictEqual(instance1.getState('init'), false, 'init state = false by default');
        instance1.init().then(function(){

            assert.strictEqual(instance1.getState('init'), true, 'init state set');

            //built-in visible state
            assert.strictEqual(instance1.getState('visible'), false, 'visible state = false by default');
            instance1.show().then(function(){
                assert.strictEqual(instance1.getState('visible'), true, 'visible state set');
                instance1.hide().then(function(){
                    assert.strictEqual(instance1.getState('visible'), false, 'visible turns to false');
                });
            });

            //built-in enabled state
            assert.strictEqual(instance1.getState('enabled'), false, 'enabled state = false by default');
            instance1.enable().then(function(){
                assert.strictEqual(instance1.getState('enabled'), true, 'enabled state set');
                instance1.disable().then(function(){
                    assert.strictEqual(instance1.getState('enabled'), false, 'enabled turns to false');
                });
            });

            //built-in init state
            setTimeout(function(){
                instance1.destroy().then(function(){
                    assert.strictEqual(instance1.getState('init'), false, 'destoyed state set');
                    QUnit.start();
                });
            }, 10);
        });
    });

    QUnit.test('test runner binding', function (assert){
        QUnit.expect(4);
        var value1 = 'xxx';
        var testRunner = {
            prop1 : 123,
            method1 : function (){
                return value1;
            },
            trigger: _.noop
        };

        var samplePluginImpl = {
            name : 'testRunnerPlugin',
            init : function (){
                assert.ok(true, 'called init');

                assert.deepEqual(this.getTestRunner(), testRunner, 'The plugin has access to the test runner');
                assert.strictEqual(this.getTestRunner().method1(), value1, 'called root component method');
            }
        };

        var myPlugin = pluginFactory(samplePluginImpl);

        var instance1 = myPlugin(testRunner);
        instance1.init();
        assert.strictEqual(instance1.getTestRunner(), testRunner, 'root component is set');
    });


    QUnit.asyncTest('root component event', function (assert){
        QUnit.expect(6);

        var eventParams = ['ABC', true, 12345];
        var myPlugin = pluginFactory({
            name : 'pluginA',
            init : function(){
                this.trigger('someEvent', eventParams[0], eventParams[1], eventParams[2]);
            }
        });

        var testRunner = eventifier()
            .on('plugin-init.pluginA', function (plugin){
                assert.ok(true, 'root component knows knows that pluginA has been initialized');
                assert.deepEqual(plugin, instance1, 'The given plugin instance is correct');
                QUnit.start();
            })
            .on('plugin-someEvent.pluginA', function (plugin, arg1, arg2, arg3){
                assert.ok(true, 'someEvent triggered and forwarded to root component');
                assert.strictEqual(eventParams[0], arg1, 'event param ok');
                assert.strictEqual(eventParams[1], arg2, 'event param ok');
                assert.strictEqual(eventParams[2], arg3, 'event param ok');
            });

        var instance1 = myPlugin(testRunner);
        instance1.init();
    });

    QUnit.asyncTest('get plugin name', function(assert){
        QUnit.expect(3);

        var name = 'foo-plugin';
        var testRunner = {
            trigger: _.noop
        };

        var samplePluginImpl = {
            name : name,
            init : function (){
                assert.ok(true, 'called init');
                assert.equal(this.getName(), name, 'The name matches');
                QUnit.start();
            }
        };

        var myPlugin = pluginFactory(samplePluginImpl);

        var instance1 = myPlugin(testRunner);
        assert.equal(instance1.getName(), name, 'The name matches');
        instance1.init();
    });
});

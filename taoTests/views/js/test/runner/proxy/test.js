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
define(['lodash', 'taoTests/runner/proxy'], function(_, proxyFactory) {
    'use strict';

    QUnit.module('proxyFactory');

    var defaultProxy = {
        init : function() {},
        destroy : function() {},
        getTestData : function() {},
        getTestContext : function() {},
        getTestMap : function() {},
        callTestAction : function() {},
        getItem : function() {},
        submitItem : function() {},
        callItemAction : function() {},
        telemetry : function() {}
    };


    QUnit.test('module', function(assert) {
        QUnit.expect(5);

        assert.equal(typeof proxyFactory, 'function', "The proxyFactory module exposes a function");
        assert.equal(typeof proxyFactory.registerProxy, 'function', "The proxyFactory module exposes a registerProxy method");
        assert.equal(typeof proxyFactory.getProxy, 'function', "The proxyFactory module exposes a getProxy method");

        proxyFactory.registerProxy('default', defaultProxy);

        assert.equal(typeof proxyFactory(), 'object', "The proxyFactory factory produces an object");
        assert.notStrictEqual(proxyFactory(), proxyFactory(), "The proxyFactory factory provides a different object on each call");
    });

    var proxyApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'getTokenHandler', title : 'getTokenHandler' },
        { name : 'addCallActionParams', title : 'addCallActionParams' },
        { name : 'getTestData', title : 'getTestData' },
        { name : 'getTestContext', title : 'getTestContext' },
        { name : 'getTestMap', title : 'getTestMap' },
        { name : 'callTestAction', title : 'callTestAction' },
        { name : 'getItem', title : 'getItem' },
        { name : 'submitItem', title : 'submitItem' },
        { name : 'callItemAction', title : 'callItemAction' }
    ];

    QUnit
        .cases(proxyApi)
        .test('instance API ', function(data, assert) {
            var instance = proxyFactory();
            QUnit.expect(1);
            assert.equal(typeof instance[data.name], 'function', 'The proxyFactory instance exposes a "' + data.title + '" function');
        });


    QUnit.asyncTest('proxyFactory.init', function(assert) {
        var initConfig = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(6);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            init : function(config) {
                assert.ok(true, 'The proxyFactory has delegated the call to init');
                assert.equal(config, initConfig, 'The proxyFactory has provided the config object to the init method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default', initConfig).on('init', function(p, config) {
            assert.ok(true, 'The proxyFactory has fired the "init" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "init" event');
            assert.equal(config, initConfig, 'The proxyFactory has provided the config object through the "init" event');
            QUnit.start();
        }).init();

        assert.equal(result, promise, 'The proxyFactory.init method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.destroy', function(assert) {
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(4);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            destroy : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to destroy');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('destroy', function(p) {
            assert.ok(true, 'The proxyFactory has fired the "destroy" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "destroy" event');
            QUnit.start();
        }).destroy();

        assert.equal(result, promise, 'The proxyFactory.destroy method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getTestData', function(assert) {
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(4);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            getTestData : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestData');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getTestData', function(p) {
            assert.ok(true, 'The proxyFactory has fired the "getTestData" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getTestData" event');
            QUnit.start();
        }).getTestData();

        assert.equal(result, promise, 'The proxyFactory.getTestData method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getTestContext', function(assert) {
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(4);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            getTestContext : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestContext');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getTestContext', function(p) {
            assert.ok(true, 'The proxyFactory has fired the "getTestContext" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getTestContext" event');
            QUnit.start();
        }).getTestContext();

        assert.equal(result, promise, 'The proxyFactory.getTestContext method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getTestMap', function(assert) {
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(4);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            getTestMap : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestMap');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getTestMap', function(p) {
            assert.ok(true, 'The proxyFactory has fired the "getTestMap" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getTestMap" event');
            QUnit.start();
        }).getTestMap();

        assert.equal(result, promise, 'The proxyFactory.getTestMap method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.callTestAction', function(assert) {
        var expectedAction = 'test';
        var expectedParams = {
            foo : 'bar'
        };
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(8);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            callTestAction : function(action, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to callTestAction');
                assert.equal(action, expectedAction, 'The proxyFactory has provided the action to the callTestAction method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params to the callTestAction method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('callTestAction', function(p, action, params) {
            assert.ok(true, 'The proxyFactory has fired the "callTestAction" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "callTestAction" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callTestAction" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "callTestAction" event');
            QUnit.start();
        }).callTestAction(expectedAction, expectedParams);

        assert.equal(result, promise, 'The proxyFactory.callTestAction method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getItem', function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(6);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            getItem : function(uri) {
                assert.ok(true, 'The proxyFactory has delegated the call to getItem');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the getItem method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getItem', function(p, uri) {
            assert.ok(true, 'The proxyFactory has fired the "getItem" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getItem" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "getItem" event');
            QUnit.start();
        }).getItem(expectedUri);

        assert.equal(result, promise, 'The proxyFactory.getItem method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.submitItem', function(assert) {
        var expectedUri      = 'http://tao.dev#item123';
        var expectedState    = { state: true };
        var expectedResponse = { response: true };
        var expectedParams   = { duration : 12.12324 };

        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(12);

        proxyFactory.registerProxy('default', _.defaults({
            submitItem : function(uri, state, response, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to submitItem');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the submitItem method');
                assert.equal(state, expectedState, 'The proxyFactory has provided the state to the submitItem method');
                assert.equal(response, expectedResponse, 'The proxyFactory has provided the response to the submitItem method');
                assert.equal(params, expectedParams, 'The proxyFactory has provided the params to the submitItem method');

                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('submitItem', function(p, uri, state, response, params) {
            assert.ok(true, 'The proxyFactory has fired the "submitItem" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "submitItem" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "submitItem" event');
            assert.equal(state, expectedState, 'The proxyFactory has provided the state through the "submitItem" event');
            assert.equal(response, expectedResponse, 'The proxyFactory has provided the response through the "submitItem" event');
            assert.equal(params, expectedParams, 'The proxyFactory has provided the params through the "submitItem" event');

            QUnit.start();
        }).submitItem(expectedUri, expectedState, expectedResponse, expectedParams);

        assert.equal(result, promise, 'The proxyFactory.submitItem method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.callItemAction', function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var expectedAction = 'test';
        var expectedParams = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(10);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            callItemAction : function(uri, action, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to callItemAction');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the callItemAction method');
                assert.equal(action, expectedAction, 'The proxyFactory has provided the action to the callItemAction method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params to the callItemAction method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('callItemAction', function(p, uri, action, params) {
            assert.ok(true, 'The proxyFactory has fired the "callItemAction" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "callItemAction" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "callItemAction" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callItemAction" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "callItemAction" event');
            QUnit.start();
        }).callItemAction(expectedUri, expectedAction, expectedParams);

        assert.equal(result, promise, 'The proxyFactory.callItemAction method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.telemetry', function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var expectedSignal = 'test';
        var expectedParams = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.expect(10);
        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            telemetry : function(uri, signal, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to telemetry');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the telemetry method');
                assert.equal(signal, expectedSignal, 'The proxyFactory has provided the signal to the telemetry method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params to the telemetry method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('telemetry', function(p, uri, signal, params) {
            assert.ok(true, 'The proxyFactory has fired the "telemetry" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "telemetry" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "telemetry" event');
            assert.equal(signal, expectedSignal, 'The proxyFactory has provided the signal through the "telemetry" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "telemetry" event');
            QUnit.start();
        }).telemetry(expectedUri, expectedSignal, expectedParams);

        assert.equal(result, promise, 'The proxyFactory.telemetry method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.addCallActionParams', function(assert) {
        QUnit.expect(5);

        var expectedItemUri = 'http://tao.dev#item123';
        var expectedAction = 'test';
        var expectedParams = {
            foo : true,
            bar : ['a', 'b']
        };
        var extraParams = {
            noz : 'moo'
        };

        proxyFactory.registerProxy('default', defaultProxy);

        var proxy = proxyFactory('default');

        proxy.addCallActionParams(extraParams);

        proxy
        .on('callItemAction', function(p, uri, action, params) {

            assert.equal(uri, expectedItemUri, 'The proxyFactory has provided the URI through the "callItemAction" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callItemAction" event');
            assert.deepEqual(params, _.merge({}, expectedParams, extraParams), 'The proxyFactory has provided the params through the "callItemAction" event with extra parameters');

        })
        .on('callTestAction', function(p, action, params) {

            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callTestAction" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "callTestAction" event without extra parameters');

            QUnit.start();
        });

        proxy.callItemAction(expectedItemUri, expectedAction, expectedParams);
        proxy.callTestAction(expectedAction, expectedParams);
    });


    QUnit.test('proxyFactory.getTokenHandler', function(assert) {

        proxyFactory.registerProxy('default', defaultProxy);

        var proxy = proxyFactory('default');

        var securityToken = proxy.getTokenHandler();

        QUnit.expect(3);

        assert.equal(typeof securityToken, 'object', 'The proxy has built a securityToken handler');
        assert.equal(typeof securityToken.getToken, 'function', 'The securityToken handler has a getToken method');
        assert.equal(typeof securityToken.setToken, 'function', 'The securityToken handler has a setToken method');

    });
});

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
define(['taoTests/runner/proxyRegistry'], function(proxyRegistry) {
    'use strict';

    QUnit.module('proxyRegistry');


    QUnit.test('module', function(assert) {
        QUnit.expect(3);
        assert.equal(typeof proxyRegistry, 'function', "The proxyRegistry module exposes a function");
        assert.equal(typeof proxyRegistry(), 'object', "The proxyRegistry factory produces an object");
        assert.notStrictEqual(proxyRegistry(), proxyRegistry(), "The proxyRegistry factory provides a different object on each call");
    });


    var proxyRegistryApi = [
        { name : 'registerProxy', title : 'registerProxy' },
        { name : 'getProxy', title : 'getProxy' }
    ];

    QUnit
        .cases(proxyRegistryApi)
        .test('instance API ', function(data, assert) {
            var instance = proxyRegistry();
            QUnit.expect(1);
            assert.equal(typeof instance[data.name], 'function', 'The proxyRegistry instance exposes a "' + data.title + '" function');
        });


    var invalidProxy = {
        init : function() {}
    };
    var validProxy = {
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

    var proxies = [{
        title: 'invalid proxy',
        name: 'invalid',
        proxy: invalidProxy,
        valid: false
    }, {
        title: 'empty name',
        name: null,
        proxy: validProxy,
        valid: false
    }, {
        title: 'valid proxy',
        name: 'myProxy1',
        proxy: validProxy,
        valid: true
    }, {
        title: 'valid proxy with validator',
        name: 'myProxy2',
        proxy: validProxy,
        valid: true,
        validator: function() {
            return true;
        }
    }, {
        title: 'invalid proxy by validator',
        name: 'myProxy3',
        proxy: validProxy,
        valid: false,
        validator: function() {
            return false;
        }
    }];

    QUnit
        .cases(proxies)
        .test('registerProxy', function(data, assert) {
            var registry = proxyRegistry({}, data.validator);

            QUnit.expect(1);

            if (!data.valid) {
                assert.throws(function() {
                    registry.registerProxy(data.name, data.proxy);
                }, 'proxyRegistry.registerProxy() must throws error when the name is empty or the proxy is invalid');
            } else {
                registry.registerProxy(data.name, data.proxy);
                assert.ok(!!registry.getProxy(data.name), 'the proxy must be registered');
            }
    });


    QUnit.test('getProxy', function(assert) {
        var registry = proxyRegistry();
        var proxy;

        var name1 = 'myProxy1';
        var proxy1 = {
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

        var name2 = 'myProxy2';
        var proxy2 = {
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

        QUnit.expect(7);

        assert.throws(function() {
            registry.getProxy();
        }, 'proxyRegistry.getProxy() must throws error when the name is empty');

        assert.throws(function() {
            registry.getProxy(proxy1);
        }, 'proxyRegistry.getProxy() must throws error when no proxy is registered');

        registry.registerProxy(name1, proxy1);

        assert.throws(function() {
            registry.getProxy('unknown');
        }, 'proxyRegistry.getProxy() must throws error when the proxy is unknown');

        proxy = registry.getProxy();
        assert.equal(proxy, proxy1, 'proxyRegistry.getProxy() must return the unique registered proxy when no name is provided and only one proxy is registered');

        proxy = registry.getProxy(name1);
        assert.equal(proxy, proxy1, 'proxyRegistry.getProxy() must return the right proxy when a name is provided #one proxy');

        registry.registerProxy(name2, proxy2);

        proxy = registry.getProxy(name1);
        assert.equal(proxy, proxy1, 'proxyRegistry.getProxy() must return the right proxy when a name is provided #proxy1');

        proxy = registry.getProxy(name2);
        assert.equal(proxy, proxy2, 'proxyRegistry.getProxy() must return the right proxy when a name is provided #proxy2');
    });
});

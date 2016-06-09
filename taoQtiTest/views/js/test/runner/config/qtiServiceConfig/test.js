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
    'taoQtiTest/runner/config/qtiServiceConfig'
], function(_, helpers, qtiServiceConfig) {
    'use strict';

    QUnit.module('qtiServiceConfig');


    QUnit.test('module', 3, function(assert) {
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123'
        };
        assert.equal(typeof qtiServiceConfig, 'function', "The qtiServiceConfig module exposes a function");
        assert.equal(typeof qtiServiceConfig(config), 'object', "The qtiServiceConfig factory produces an instance");
        assert.notStrictEqual(qtiServiceConfig(config), qtiServiceConfig(config), "The qtiServiceConfig factory provides a different instance on each call");
    });


    // small coverage check to facilitate dev of unit tests
    var coverage = {};
    QUnit.moduleDone(function() {
        _.forEach(proxyApi, function(api) {
            if (!coverage[api.name]) {
                console.log('Missing unit test for method qtiServiceConfig.' + api.name);
            }
        });
    });


    var proxyApi = [
        { name : 'getTestDefinition', title : 'getTestDefinition' },
        { name : 'getTestCompilation', title : 'getTestCompilation' },
        { name : 'getServiceCallId', title : 'getServiceCallId' },
        { name : 'getServiceController', title : 'getServiceController' },
        { name : 'getServiceExtension', title : 'getServiceExtension' },
        { name : 'getTestActionUrl', title : 'getTestActionUrl' },
        { name : 'getItemActionUrl', title : 'getItemActionUrl' }
    ];

    QUnit
        .cases(proxyApi)
        .test('proxy API ', 1, function(data, assert) {
            var config = {
                testDefinition: 'http://tao.dev/mockTestDefinition#123',
                testCompilation: 'http://tao.dev/mockTestCompilation#123',
                serviceCallId: 'http://tao.dev/mockServiceCallId#123'
            };
            var instance = qtiServiceConfig(config);
            assert.equal(typeof instance[data.name], 'function', 'The qtiServiceConfig instances expose a "' + data.title + '" function');
        });


    QUnit.test('qtiServiceConfig factory', 4, function(assert) {
        qtiServiceConfig({
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123'
        });
        assert.ok(true, 'The qtiServiceConfig() factory must not throw an exception when all the required config entries are provided');

        assert.throws(function() {
            qtiServiceConfig({
                testCompilation: 'http://tao.dev/mockTestCompilation#123',
                serviceCallId: 'http://tao.dev/mockServiceCallId#123'
            });
        }, 'The qtiServiceConfig() factory must throw an exception is the required config entry testDefinition is missing');

        assert.throws(function() {
            qtiServiceConfig({
                testDefinition: 'http://tao.dev/mockTestDefinition#123',
                serviceCallId: 'http://tao.dev/mockServiceCallId#123'
            });
        }, 'The qtiServiceConfig() factory must throw an exception is the required config entry testCompilation is missing');

        assert.throws(function() {
            qtiServiceConfig({
                testDefinition: 'http://tao.dev/mockTestDefinition#123',
                testCompilation: 'http://tao.dev/mockTestCompilation#123'
            });
        }, 'The qtiServiceConfig() factory must throw an exception is the required config entry serviceCallId is missing');
    });


    QUnit.test('qtiServiceConfig.getTestDefinition', 1, function(assert) {
        var expectedTestDefinition = 'http://tao.dev/mockTestDefinition#123';
        var config = {
            testDefinition: expectedTestDefinition,
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123'
        };
        var instance = qtiServiceConfig(config);
        coverage.getTestDefinition = true;

        assert.equal(instance.getTestDefinition(), expectedTestDefinition, 'The qtiServiceConfig.getTestDefinition() method has returned the expected value');
    });


    QUnit.test('qtiServiceConfig.getTestCompilation', 1, function(assert) {
        var expectedTestCompilation = 'http://tao.dev/mockTestCompilation#123';
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: expectedTestCompilation,
            serviceCallId: 'http://tao.dev/mockServiceCallId#123'
        };
        var instance = qtiServiceConfig(config);
        coverage.getTestCompilation = true;

        assert.equal(instance.getTestCompilation(), expectedTestCompilation, 'The qtiServiceConfig.getTestCompilation() method has returned the expected value');
    });


    QUnit.test('qtiServiceConfig.getServiceCallId', 1, function(assert) {
        var expectedServiceCallId = 'http://tao.dev/mockServiceCallId#123';
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: expectedServiceCallId
        };
        var instance = qtiServiceConfig(config);
        coverage.getServiceCallId = true;

        assert.equal(instance.getServiceCallId(), expectedServiceCallId, 'The qtiServiceConfig.getServiceCallId() method has returned the expected value');
    });


    QUnit.test('qtiServiceConfig.getServiceController', 3, function(assert) {
        var expectedServiceController = 'MockRunner';
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123'
        };
        var instance = qtiServiceConfig(config);
        coverage.getServiceController = true;

        assert.notEqual(instance.getServiceController(), expectedServiceController, 'The qtiServiceConfig.getServiceController() method must return the default value');
        assert.ok(!!instance.getServiceController(), 'The qtiServiceConfig.getServiceController() method must not return a null value');

        config.serviceController = expectedServiceController;
        instance = qtiServiceConfig(config);
        assert.equal(instance.getServiceController(), expectedServiceController, 'The qtiServiceConfig.getServiceController() method has returned the expected value');
    });


    QUnit.test('qtiServiceConfig.getServiceExtension', 3, function(assert) {
        var expectedServiceExtension = 'MockExtension';
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123'
        };
        var instance = qtiServiceConfig(config);
        coverage.getServiceExtension = true;

        assert.notEqual(instance.getServiceExtension(), expectedServiceExtension, 'The qtiServiceConfig.getServiceExtension() method must return the default value');
        assert.ok(!!instance.getServiceExtension(), 'The qtiServiceConfig.getServiceExtension() method must not return a null value');

        config.serviceExtension = expectedServiceExtension;
        instance = qtiServiceConfig(config);
        assert.equal(instance.getServiceExtension(), expectedServiceExtension, 'The qtiServiceConfig.getServiceExtension() method has returned the expected value');
    });


    QUnit.test('qtiServiceConfig.getTestActionUrl', 1, function(assert) {
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123',
            serviceController: 'MockRunner',
            serviceExtension: 'MockExtension'
        };
        var actionName = 'MockAction';
        var expectedUrl = helpers._url(actionName, config.serviceController, config.serviceExtension, {
            testDefinition : config.testDefinition,
            testCompilation : config.testCompilation,
            serviceCallId : config.serviceCallId
        });
        var instance = qtiServiceConfig(config);
        coverage.getTestActionUrl = true;

        assert.equal(instance.getTestActionUrl(actionName), expectedUrl, 'The qtiServiceConfig.getTestActionUrl() method has returned the expected value');
    });


    QUnit.test('qtiServiceConfig.getItemActionUrl', 1, function(assert) {
        var itemUri = 'http://tao.dev/mockItem#123';
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123',
            serviceController: 'MockRunner',
            serviceExtension: 'MockExtension'
        };
        var actionName = 'MockAction';
        var expectedUrl = helpers._url(actionName, config.serviceController, config.serviceExtension, {
            testDefinition : config.testDefinition,
            testCompilation : config.testCompilation,
            testServiceCallId : config.serviceCallId,
            itemDefinition : itemUri
        });
        var instance = qtiServiceConfig(config);
        coverage.getItemActionUrl = true;

        assert.equal(instance.getItemActionUrl(itemUri, actionName), expectedUrl, 'The qtiServiceConfig.getItemActionUrl() method has returned the expected value');
    });
    
    
    QUnit.test('qtiServiceConfig.getTelemetryUrl', 1, function(assert) {
        var itemUri = 'http://tao.dev/mockItem#123';
        var config = {
            testDefinition: 'http://tao.dev/mockTestDefinition#123',
            testCompilation: 'http://tao.dev/mockTestCompilation#123',
            serviceCallId: 'http://tao.dev/mockServiceCallId#123',
            serviceController: 'MockRunner',
            serviceExtension: 'MockExtension'
        };
        var signalName = 'MockSignal';
        var expectedUrl = helpers._url(signalName, config.serviceController, config.serviceExtension, {
            testDefinition : config.testDefinition,
            testCompilation : config.testCompilation,
            testServiceCallId : config.serviceCallId,
            itemDefinition : itemUri
        });
        var instance = qtiServiceConfig(config);
        coverage.getTelemetryUrl = true;

        assert.equal(instance.getTelemetryUrl(itemUri, signalName), expectedUrl, 'The qtiServiceConfig.getTelemetryUrl() method has returned the expected value');
    });
});

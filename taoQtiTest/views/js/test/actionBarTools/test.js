define([
    'jquery',
    'lodash',
    'taoQtiTest/testRunner/actionBarTools'
], function($, _, actionBarTools){
    'use strict';

    var containerSelector = '#tools-container';

    var qtiTools = {
        tool1 : {
            'label' : 'tool 1',
            'hook' : 'taoQtiTest/test/samples/hooks/validHook'
        },
        tool2 : {
            'label' : 'tool 2',
            'hook' : 'taoQtiTest/test/samples/hooks/validHookHidden'
        },
        tool3 : {
            'label' : 'tool 3',
            'hook' : 'taoQtiTest/test/samples/hooks/validHook'
        },
        tool4 : {
            'label' : 'tool 4',
            'hook' : 'taoQtiTest/test/samples/hooks/invalidHookMissingMethod'
        }
    };

    QUnit.module('actionBarTools');


    QUnit.test('module', 1, function(assert) {
        assert.equal(typeof actionBarTools, 'object', "The actionBarTools module exposes an object");
    });


    var actionBarToolsApi = [
        { name : 'register', title : 'register' },
        { name : 'getRegisteredTools', title : 'getRegisteredTools' },
        { name : 'getRegistered', title : 'getRegistered' },
        { name : 'isRegistered', title : 'isRegistered' },
        { name : 'get', title : 'get' },
        { name : 'list', title : 'list' },
        { name : 'render', title : 'render' }
    ];

    QUnit
        .cases(actionBarToolsApi)
        .test('module API ', 1, function(data, assert) {
            assert.equal(typeof actionBarTools[data.name], 'function', 'The actionBarTools module exposes a "' + data.title + '" function');
        });


    QUnit.test('register', 8, function(assert) {
        actionBarTools.register(null);
        assert.equal(typeof actionBarTools.getRegisteredTools(), 'object', 'The actionBarTools.getRegisteredTools() method  must provide a list, even if no list is provided');
        assert.equal(_.values(actionBarTools.getRegisteredTools()).length, 0, 'The actionBarTools.getRegisteredTools() method must provide an empty list of registered configs when not list is provided');

        actionBarTools.register(qtiTools);
        assert.strictEqual(actionBarTools.getRegisteredTools(), qtiTools, 'The actionBarTools.getRegisteredTools() method must provide the registered list');

        assert.strictEqual(actionBarTools.getRegistered('tool1'), qtiTools.tool1, 'The actionBarTools.getRegistered() method must return the right tool config');
        assert.ok(!actionBarTools.getRegistered('notExist'), 'The actionBarTools.getRegistered() method cannot return an unknown tool');

        assert.ok(_.isArray(actionBarTools.list()), 'The actionBarTools.list() method must return a list, even if no tool is rendered');
        assert.equal(actionBarTools.list().length, 0, 'The actionBarTools.list() method must return an empty list when no tool is rendered');
        assert.ok(!actionBarTools.get('tool1'), 'No tool is rendered before the actionBarTools.render() method is called');
    });


    QUnit.asyncTest('render', 17, function(assert) {
        var $container = $(containerSelector);
        var mockTestContext = {};
        var mockTestRunner = {};

        actionBarTools.register(qtiTools);
        actionBarTools.render($container, mockTestContext, mockTestRunner, function(tools, $ctnr, testContext, testRunner, obj) {
            assert.ok(_.isArray(tools), 'The render method must provide the list of tools');
            assert.equal(tools.length, 3, 'The render method must only provide the valid tools');
            assert.ok(_.find(tools, function(tool){return tool.getId() === 'tool2'}), 'The render method must provide the tool2 instance');
            assert.ok(_.find(tools, function(tool){return tool.getId() === 'tool3'}), 'The render method must provide the tool3 instance');

            assert.strictEqual(this, actionBarTools, 'The render method fires the callback inside the actionBarTools context');
            assert.equal($ctnr && $ctnr.length, 1, 'The render method must provide a jQuery element');
            assert.ok($ctnr.is(containerSelector), 'The render method must provide the container element');
            assert.strictEqual(testContext, mockTestContext, 'The render method must provide the testContext object');
            assert.strictEqual(testRunner, mockTestRunner, 'The render method must provide the testRunner instance');
            assert.strictEqual(obj, actionBarTools, 'The render method must provide the actionBarTools instance');

            assert.equal($container.find('.action').length, 2, 'The render method must renders the buttons');
            assert.equal($container.find('[data-control="tool1"]').length, 1, 'The render method must renders the tool1 button');
            assert.equal($container.find('[data-control="tool3"]').length, 1, 'The render method must renders the tool3 button');

            assert.ok(_.isArray(actionBarTools.list()), 'The actionBarTools.list() method must return a list');
            assert.equal(actionBarTools.list().length, 2, 'The actionBarTools.list() method must return a filled list when tools are rendered');
            assert.ok(actionBarTools.get('tool2'), 'The tool tool2 must exist');
            assert.ok(actionBarTools.get('tool3'), 'The tool tool3 must exist');

            QUnit.start();
        });
    });


    QUnit.asyncTest('events', 21, function(assert) {
        var $container = $(containerSelector);
        var mockTestContext = {};
        var mockTestRunner = {};

        actionBarTools.on('beforeregister', function(tools, obj) {
            assert.strictEqual(tools, qtiTools, 'The beforeregister event must provide the list of tools to register');
            assert.strictEqual(obj, actionBarTools, 'The beforeregister event must provide the actionBarTools instance');
            QUnit.start();
        });
        actionBarTools.on('afterregister', function(tools, obj) {
            assert.strictEqual(tools, qtiTools, 'The afterregister event must provide the list of registered tools');
            assert.strictEqual(obj, actionBarTools, 'The afterregister event must provide the actionBarTools instance');
            QUnit.start();
        });

        actionBarTools.on('beforerender', function($ctnr, testContext, testRunner, obj) {
            assert.equal($ctnr && $ctnr.length, 1, 'The beforerender event must provide a jQuery element');
            assert.ok($ctnr.is(containerSelector), 'The beforerender event must provide the container element');
            assert.strictEqual(testContext, mockTestContext, 'The beforerender event must provide the testContext object');
            assert.strictEqual(testRunner, mockTestRunner, 'The beforerender event must provide the testRunner instance');
            assert.strictEqual(obj, actionBarTools, 'The beforerender event must provide the actionBarTools instance');
            QUnit.start();
        });
        actionBarTools.on('afterrender', function(tools, $ctnr, testContext, testRunner, obj) {
            assert.ok(_.isArray(tools), 'The afterrender event must provide the list of tools');
            assert.equal(tools.length, 3, 'The afterrender event must only provide the valid tools');
            assert.ok(_.find(tools, function(tool){return tool.getId() === 'tool2'}), 'The afterrender event must provide the tool2 instance');
            assert.ok(_.find(tools, function(tool){return tool.getId() === 'tool3'}), 'The afterrender event must provide the tool3 instance');

            assert.equal($ctnr && $ctnr.length, 1, 'The afterrender event must provide a jQuery element');
            assert.ok($ctnr.is(containerSelector), 'The afterrender event must provide the container element');
            assert.strictEqual(testContext, mockTestContext, 'The afterrender event must provide the testContext object');
            assert.strictEqual(testRunner, mockTestRunner, 'The afterrender event must provide the testRunner instance');
            assert.strictEqual(obj, actionBarTools, 'The afterrender event must provide the actionBarTools instance');

            assert.equal($container.find('.action').length, 2, 'The render method must renders the buttons');
            assert.equal($container.find('[data-control="tool1"]').length, 1, 'The render method must renders the tool1 button');
            assert.equal($container.find('[data-control="tool3"]').length, 1, 'The render method must renders the tool3 button');

            QUnit.start();
        });

        QUnit.stop(3);

        actionBarTools.register(qtiTools);
        actionBarTools.render($container, mockTestContext, mockTestRunner);
    });
});


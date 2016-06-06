define([
    'jquery',
    'lodash',
    'taoQtiTest/testRunner/actionBarHook',
    'core/errorHandler'
], function($, _, actionBarHook, errorHandler){
    'use strict';

    var containerId = 'tools-container';


    QUnit.module('validation');

    var tools = [{
        tool : {
            'label' : 'tool 1',
            'hook' : 'taoQtiTest/test/samples/hooks/validHook'
        },
        title : 'valid tool',
        expected : true
    }, {
        tool : {
            'title' : 'my tool 1 does amazing stuff',
            'label' : 'tool 1',
            'hook' : 'taoQtiTest/test/samples/hooks/validHook'
        },
        title : 'valid tool',
        expected : true
    }, {
        tool : 'taoQtiTest/test/samples/hooks/validHook',
        title : 'invalid tool',
        expected : false
    }, {
        tool : {
            'title' : 'my tool 1 does amazing stuff',
            'label' : 'tool 1'
        },
        title : 'invalid tool',
        expected : false
    }, {
        tool : {
            'title' : 'my tool 1 does amazing stuff',
            'hook' : 'taoQtiTest/test/samples/hooks/validHook'
        },
        title : 'valid tool',
        expected : true
    }, {
        tool : {
            'label' : 'tool X',
            'hook' : 'taoQtiTest/test/samples/hooks/invalidHookMissingMethod'
        },
        title : 'valid tool',
        expected : true
    }, {
        tool : {
            'label' : 'tool X',
            'hook' : 'taoQtiTest/test/samples/hooks/noexisting'
        },
        title : 'valid tool',
        expected : true
    }, {
        tool : {
            'label' : 'hidden tool',
            'hook' : 'taoQtiTest/test/samples/hooks/validHookHidden'
        },
        title : 'valid tool',
        expected : true
    }];

    QUnit
        .cases(tools)
        .test('isValidConfig ', function(data, assert) {
            assert.equal(actionBarHook.isValid(data.tool), data.expected, data.title);
        });


    QUnit.module('initQtiTool');


    QUnit.asyncTest('ok', function(assert){

        QUnit.expect(1);

        var $container = $('#' + containerId);
        actionBarHook.initQtiTool($container, 'tool1', tools[0].tool, {});

        $container.on('ready.actionBarHook', function(){
            assert.equal($container.find('[data-control=tool1]').length, 1, 'button found');
            QUnit.start();
        });

    });


    QUnit.asyncTest('multiple times the same', function(assert){

        QUnit.expect(3);

        var $container = $('#' + containerId);
        actionBarHook.initQtiTool($container, 'tool1', tools[0].tool, {});
        actionBarHook.initQtiTool($container, 'tool1', tools[0].tool, {});
        actionBarHook.initQtiTool($container, 'tool1', tools[0].tool, {});

        var count = 0;
        $container.on('ready.actionBarHook', function(){

            //no matter how many times the initQtiTool is called, only have one button available at once
            assert.equal($container.find('[data-control=tool1]').length, 1, 'button found');

            if(++count === 3){
                QUnit.start();
            }
        });

    });


    QUnit.test('hidden tool', function(assert){

        QUnit.expect(0);

        var $container = $('#' + containerId);
        actionBarHook.initQtiTool($container, 'tool1', tools[7].tool, {});

        $container.on('ready.actionBarHook', function(){
            //the test is not supposed to be there
            assert.equal($container.find('[data-control=tool1]').length, 1, 'button found');
        });

    });


    QUnit.asyncTest('invalid hook', function(assert){

        QUnit.expect(2);
        var $container = $('#' + containerId);

        errorHandler.listen('.actionBarHook', function(err){
            assert.equal(err.message, 'invalid hook format', 'error thrown for invlid hook format');
            assert.equal($container.children('[data-control=toolX]').length, 0, 'button found');
            QUnit.start();
        });

        actionBarHook.initQtiTool($container, 'toolX', tools[5].tool, {});

    });


    QUnit.asyncTest('inexisting hook', function(assert){

        QUnit.expect(2);
        var $container = $('#' + containerId);

        errorHandler.listen('.actionBarHook', function(err){
            assert.equal(err.message, 'the hook amd module cannot be found', 'error thrown for hook not found');
            assert.equal($container.children('[data-control=toolX]').length, 0, 'button found');
            QUnit.start();
        });

        actionBarHook.initQtiTool($container, 'toolX', tools[6].tool, {});

    });


    QUnit.asyncTest('ordering', function(assert){

        QUnit.expect(11);

        var samples = [
            {
                'title' : 'tool A in position 1',
                'label' : 'tool A',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 1
            },
            {
                'title' : 'tool B in position 2',
                'label' : 'tool B',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 2
            },
            {
                'title' : 'tool A1 in position 1',
                'label' : 'tool A1',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 1
            },
            {
                'title' : 'tool C in position -1',
                'label' : 'tool C',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : -1
            },
            {
                'title' : 'tool D in position 0',
                'label' : 'tool D',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 0
            },
            {
                'title' : 'tool B2 in position 2',
                'label' : 'tool B2',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 2
            },
            {
                'title' : 'tool AX in invalid position',
                'label' : 'tool AX',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 'X'
            },
            {
                'title' : 'tool AY in unknown position',
                'label' : 'tool AY',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook'
            },
            {
                'title' : 'tool E in first position',
                'label' : 'tool E',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 'first'
            },
            {
                'title' : 'tool F in last position',
                'label' : 'tool F',
                'hook' : 'taoQtiTest/test/samples/hooks/validHook',
                'order' : 'last'
            }
        ];

        var $container = $('#' + containerId);
        actionBarHook.initQtiTool($container, 'toolY', samples[7], {});
        actionBarHook.initQtiTool($container, 'toolB', samples[1], {});
        actionBarHook.initQtiTool($container, 'toolA', samples[0], {});
        actionBarHook.initQtiTool($container, 'toolX', samples[6], {});
        actionBarHook.initQtiTool($container, 'toolA1', samples[2], {});
        actionBarHook.initQtiTool($container, 'toolD', samples[4], {});
        actionBarHook.initQtiTool($container, 'toolC', samples[3], {});
        actionBarHook.initQtiTool($container, 'toolB2', samples[5], {});
        actionBarHook.initQtiTool($container, 'toolF', samples[9], {});
        actionBarHook.initQtiTool($container, 'toolE', samples[8], {});


        //check the order when all the buttons are ready
        var count = 0;
        $container.on('ready.actionBarHook', function(){
            if(++count === 10){
                var $buttons = $container.children('.action');
                assert.equal($buttons.length, 10, 'all ten buttons added');
                assert.equal($($buttons[0]).data('control'), 'toolE');
                assert.equal($($buttons[1]).data('control'), 'toolC');
                assert.equal($($buttons[2]).data('control'), 'toolD');
                assert.equal($($buttons[3]).data('control'), 'toolA');
                assert.equal($($buttons[4]).data('control'), 'toolA1');
                assert.equal($($buttons[5]).data('control'), 'toolB');
                assert.equal($($buttons[6]).data('control'), 'toolB2');
                assert.equal($($buttons[7]).data('control'), 'toolY');
                assert.equal($($buttons[8]).data('control'), 'toolX');
                assert.equal($($buttons[9]).data('control'), 'toolF');
                QUnit.start();
            }

        });
    });

});


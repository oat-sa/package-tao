define(['jquery', 'ui', 'ui/btngrouper'], function($, ui, btngrouper){
    'use strict';    
    
    QUnit.module('Button Grouper Stand Alone Test');
   
    QUnit.test('plugin', function(assert){
       QUnit.expect(1);
       assert.ok(typeof $.fn.btngrouper === 'function', 'The Button Grouper plugin is registered');
    });
   
    QUnit.asyncTest('Initialization', function(assert){
        QUnit.expect(2);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='toggle']", $fixture);
        assert.ok($group.length === 1, 'The Group is available');
        
        $group.on('create.btngrouper', function(){
            assert.ok(typeof $group.data('ui.btngrouper') === 'object', 'The element is runing the plugin');
            QUnit.start();
        });
        $group.btngrouper({
            action : 'toggle'
        });
    });
    
    QUnit.asyncTest('Toggle', function(assert){
        QUnit.expect(6);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='toggle']", $fixture);
        assert.ok($group.length === 1, 'The Group is available');
        
        $group.on('create.btngrouper', function(){
            assert.equal($group.find('.active').length, 1, 'Only one element is active');
            assert.equal($group.btngrouper('value'), 'Y', 'The group value is Y');
            
            $group.find('li:first').trigger('click');
        });
        $group.on('toggle.btngrouper', function(){
            assert.equal($group.find('.active').length, 1, 'Only one element is active');
            assert.ok($group.find('li:last').hasClass('active'), 'The active element is toggled');
            assert.equal($group.btngrouper('value'), 'N', 'The group value is N');
            QUnit.start();
        });
        $group.btngrouper({
            action : 'toggle'
        });
    });
    
    QUnit.asyncTest('switch', function(assert){
        QUnit.expect(5);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='switch']", $fixture);
        assert.ok($group.length === 1, 'The Group is available');
        assert.ok($group.find('li:first').hasClass('active'), 'The first element is active');
        
        $group.on('create.btngrouper', function(){
            assert.equal($group.btngrouper('value'), 'B', 'The group value is B');
            $group.find('li:first').trigger('click');
        });
        $group.on('switch.btngrouper', function(){
            assert.equal($group.find('.active').length, 0, 'No more element are active');
            assert.equal($group.btngrouper('value'), [], 'No values');
            QUnit.start();
        });
        $group.btngrouper({
            action : 'switch'
        });
    });
    
    QUnit.module('Button Grouper Data Attr Test');
     
     QUnit.asyncTest('initialization', function(assert){
        QUnit.expect(3);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='toggle']", $fixture);
        assert.ok($group.length === 1, 'The Group is available');
        
        $group.on('toggle.btngrouper', function(){
            assert.equal($group.find('.active').length, 1, 'Only one element is active');
            assert.ok($group.find('li:last').hasClass('active'), 'The active element is toggled');
            QUnit.start();
        });
       
        btngrouper($fixture);
        $group.find('li:last').trigger('click');
    });
    
});



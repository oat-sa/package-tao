define(['jquery', 'ui', 'ui/btngrouper'], function($, ui, btngrouper){
    'use strict';    
    
    module('Button Grouper Stand Alone Test');
   
    test('plugin', function(){
       expect(1);
       ok(typeof $.fn.btngrouper === 'function', 'The Button Grouper plugin is registered');
    });
   
    asyncTest('Initialization', function(){
        expect(2);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='toggle']", $fixture);
        ok($group.length === 1, 'The Group is available');
        
        $group.on('create.btngrouper', function(){
            ok(typeof $group.data('ui.btngrouper') === 'object', 'The element is runing the plugin');
            start();
        });
        $group.btngrouper({
            action : 'toggle'
        });
    });
    
    asyncTest('Toggle', function(){
        expect(6);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='toggle']", $fixture);
        ok($group.length === 1, 'The Group is available');
        
        $group.on('create.btngrouper', function(){
            equal($group.find('.active').length, 1, 'Only one element is active');
            equal($group.btngrouper('value'), 'Y', 'The group value is Y');
            
            $group.find('li:first').trigger('click');
        });
        $group.on('toggle.btngrouper', function(){
            equal($group.find('.active').length, 1, 'Only one element is active');
            ok($group.find('li:last').hasClass('active'), 'The active element is toggled');
            equal($group.btngrouper('value'), 'N', 'The group value is N');
            start();
        });
        $group.btngrouper({
            action : 'toggle'
        });
    });
    
    asyncTest('switch', function(){
        expect(5);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='switch']", $fixture);
        ok($group.length === 1, 'The Group is available');
        ok($group.find('li:first').hasClass('active'), 'The first element is active');
        
        $group.on('create.btngrouper', function(){
            equal($group.btngrouper('value'), 'B', 'The group value is B');
            $group.find('li:first').trigger('click');
        });
        $group.on('switch.btngrouper', function(){
            equal($group.find('.active').length, 0, 'No more element are active');
            equal($group.btngrouper('value'), [], 'No values');
            start();
        });
        $group.btngrouper({
            action : 'switch'
        });
    });
    
    module('Button Grouper Data Attr Test');
     
     asyncTest('initialization', function(){
        expect(3);
        
        var $fixture = $('#qunit-fixture');
        
        var $group = $("[data-button-group='toggle']", $fixture);
        ok($group.length === 1, 'The Group is available');
        
        $group.on('toggle.btngrouper', function(){
            equal($group.find('.active').length, 1, 'Only one element is active');
            ok($group.find('li:last').hasClass('active'), 'The active element is toggled');
            start();
        });
       
        btngrouper($fixture);
        $group.find('li:last').trigger('click');
    });
    
});



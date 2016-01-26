define(['jquery', 'ui/adder'], function($, adder){
    
    
    QUnit.module('Adder Stand Alone Test');
   
    QUnit.test('plugin', function(assert){
       QUnit.expect(1);
       assert.ok(typeof $.fn.adder === 'function', 'The Adder plugin is registered');
    });
   
    QUnit.asyncTest('Initialization', function(assert){
        QUnit.expect(6);
        
        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        assert.ok($elt.length === 1, 'Adder link is available');
        
        var $target = $('.content', $container);
        assert.ok($target.length === 1, 'Target is available');
        
        var $tmpl = $('.tmpl', $container);
        assert.ok($tmpl.length === 1, 'Template is available');
        
        $elt.on('create.adder', function(){
            var data = $elt.data('ui.adder'); 
            assert.ok(typeof data === 'object', 'The element is runing the plugin');

            $elt.adder('options', { test : true});
            strictEqual(data.test, true, 'The plugin options methods update the element data');

            QUnit.start();
        });
        $elt.adder({
            target : $target,
            content : $tmpl
        });
    });
    
    QUnit.asyncTest('Template adding', function(assert){
        QUnit.expect(7);
        
        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        assert.ok($elt.length === 1, 'Adder link is available');
        
        var $target = $('.content', $container);
        assert.ok($target.length === 1, 'Target is available');
        
        var $tmpl = $('.tmpl', $container);
        assert.ok($tmpl.length === 1, 'Template is available');
        
        $elt.on('create.adder', function(){
            $elt.trigger('click');
        });
         $elt.on('add.adder', function(){
            var $p = $target.find('p');
            assert.ok($p.length === 1, 'Content is added');
            
            assert.equal($p.attr('id'), 'foo', 'Template data inserted');
            assert.equal($p.text(), 'bar', 'Template data inserted');
             
            QUnit.start();
         });
        $elt.adder({
            target : $target,
            content : $tmpl,
            templateData : function(cb){
                cb({
                   id : 'foo',
                   text : 'bar'
                });
            }
        });
    });
    
    QUnit.asyncTest('HTML adding', function(assert){
        QUnit.expect(5);
        
        var $container = $('#container-2');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        assert.ok($elt.length === 1, 'Adder link is available');
        
        var $target = $('.list2', $container);
        assert.ok($target.length === 1, 'Target is available');
        
        var $content = $('.list1', $container);
        assert.ok($content.length === 1, 'DOM content is available');
        
        $elt.on('create.adder', function(){
            $elt.trigger('click');
            setTimeout(function(){
                $elt.trigger('click');
            }, 500);
        });
        var counter = 0;
         $elt.on('add.adder', function(){
            if(counter >= 1){
                assert.ok($target.find('li').length === 2, 'HTML content added 2 times');
                QUnit.start();
            }
            counter++;
         });
        $elt.adder({
            target : $target,
            content : $content
        });
    });
    
    QUnit.module('Adder Data Attr Test');
     
     QUnit.asyncTest('initialization', function(assert){
        QUnit.expect(3);
        
        var $container = $('#container-3');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        assert.ok($elt.length === 1, 'Test input is available');
        
        $elt.on('add.adder', function(){
            assert.ok($('#c3-list2').find('li').length === 1, 'Content added');
            QUnit.start();
        });
       
        adder($container);
        $elt.trigger('click');
    });
    
});



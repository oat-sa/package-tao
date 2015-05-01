define(['jquery', 'ui/adder'], function($, adder){
    
    
    module('Adder Stand Alone Test');
   
    test('plugin', function(){
       expect(1);
       ok(typeof $.fn.adder === 'function', 'The Adder plugin is registered');
    });
   
    asyncTest('Initialization', function(){
        expect(6);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        ok($elt.length === 1, 'Adder link is available');
        
        var $target = $('.content', $container);
        ok($target.length === 1, 'Target is available');
        
        var $tmpl = $('.tmpl', $container);
        ok($tmpl.length === 1, 'Template is available');
        
        $elt.on('create.adder', function(){
            var data = $elt.data('ui.adder'); 
            ok(typeof data === 'object', 'The element is runing the plugin');

            $elt.adder('options', { test : true});
            strictEqual(data.test, true, 'The plugin options methods update the element data');

            start();
        });
        $elt.adder({
            target : $target,
            content : $tmpl
        });
    });
    
    asyncTest('Template adding', function(){
        expect(7);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        ok($elt.length === 1, 'Adder link is available');
        
        var $target = $('.content', $container);
        ok($target.length === 1, 'Target is available');
        
        var $tmpl = $('.tmpl', $container);
        ok($tmpl.length === 1, 'Template is available');
        
        $elt.on('create.adder', function(){
            $elt.trigger('click');
        });
         $elt.on('add.adder', function(){
            var $p = $target.find('p');
            ok($p.length === 1, 'Content is added');
            
            equal($p.attr('id'), 'foo', 'Template data inserted');
            equal($p.text(), 'bar', 'Template data inserted');
             
            start();
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
    
    asyncTest('HTML adding', function(){
        expect(5);
        
        var $container = $('#container-2');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        ok($elt.length === 1, 'Adder link is available');
        
        var $target = $('.list2', $container);
        ok($target.length === 1, 'Target is available');
        
        var $content = $('.list1', $container);
        ok($content.length === 1, 'DOM content is available');
        
        $elt.on('create.adder', function(){
            $elt.trigger('click');
            setTimeout(function(){
                $elt.trigger('click');
            }, 500);
        });
        var counter = 0;
         $elt.on('add.adder', function(){
            if(counter >= 1){
                ok($target.find('li').length === 2, 'HTML content added 2 times');
                start();
            }
            counter++;
         });
        $elt.adder({
            target : $target,
            content : $content
        });
    });
    
    module('Adder Data Attr Test');
     
     asyncTest('initialization', function(){
        expect(3);
        
        var $container = $('#container-3');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.adder', $container);
        ok($elt.length === 1, 'Test input is available');
        
        $elt.on('add.adder', function(){
            ok($('#c3-list2').find('li').length === 1, 'Content added');
            start();
        });
       
        adder($container);
        $elt.trigger('click');
    });
    
});



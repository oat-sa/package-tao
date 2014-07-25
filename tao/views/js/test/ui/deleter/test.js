define(['jquery', 'ui', 'ui/deleter'], function($, ui, deleter){
    
    
    module('Deleter Stand Alone Test');
   
    test('plugin', function(){
       expect(1);
       ok(typeof $.fn.deleter === 'function', 'The Deleter plugin is registered');
    });
   
    asyncTest('Initialization', function(){
        expect(4);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.deleter', $container);
        ok($elt.length === 1, 'Deleter link is available');
        
        var $target = $('.content', $container);
        ok($target.length === 1, 'Target is available');
        
        $elt.on('create.deleter', function(){
            ok(typeof $elt.data('ui.deleter') === 'object', 'The element is runing the plugin');
            start();
        });
        $elt.deleter({ 
            target : $target,
            confirm : false
        });
    });
    
    asyncTest('Deleting', function(){
        expect(4);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.deleter', $container);
        ok($elt.length === 1, 'Deleter link is available');
        
        var $target = $('.content', $container);
        ok($target.length === 1, 'Target is available');
        
        $elt.on('create.deleter', function(){
            $elt.trigger('click');
        });
        
         $elt.on('deleted.deleter', function(e){
            if(e.namespace === 'deleter'){
                ok($('.content', $container).length === 0, 'Target doesn\'t exists anymore');
                start();
            }
        });
        $elt.deleter({ 
            target : $target,
            confirm : false
        });
    });
    
    
    module('Deleter Data Attr Test');
     
     asyncTest('Initialization', function(){
        expect(4);
        
        //prevent the confirm message to lock the test
        window.confirm = function(){
            return true;
        };
        
        var $container = $('#container-2');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.deleter', $container);
        ok($elt.length === 1, 'Deleter link is available');
        
        var $target = $('.content', $container);
        ok($target.length === 1, 'Target is available');
        
        $elt.on('deleted.deleter', function(e){
            if(e.namespace === 'deleter'){
                ok($('.content', $container).length === 0, 'Target doesn\'t exists anymore');
                start();
            }
        });
        
        deleter($container);
        $elt.trigger('click');
    });
 
    module('Undo');
 
    asyncTest('Undo Delete', function(){
        expect(5);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $('.deleter', $container);
        ok($elt.length === 1, 'Deleter link is available');
        
        var $target = $('.content', $container);
        ok($target.length === 1, 'Target is available');
        
        $elt.on('create.deleter', function(){
            $elt.trigger('click');
        });
        
        $elt.on('delete.deleter', function(e){
            if(e.namespace === 'deleter'){
                setTimeout(function(){
                    var $undo = $('body').find('a.undo');
                    console.log($undo);
                    ok( $undo.length === 1, 'the undo link is there');
                    $undo.trigger('click');
                }, 100);
            }
        });
        $elt.on('undo.deleter', function(e){
            console.log('undo triggered');
            var $target = $('.content', $container);
            ok($target.length === 1, 'Target is available');
            start();
        });
        $elt.deleter({ 
            target : $target,
            confirm : false,
            undo : true,
            undoTimeout: 10000,
            undoContainer : $('#qunit-fixture')
        });
    });
});



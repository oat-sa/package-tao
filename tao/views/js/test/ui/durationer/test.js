define(['jquery', 'ui',  'ui/durationer'], function($, ui, durationer){
    
    
    module('Durationer Stand Alone Test');
   
    test('plugin', function(){
       expect(1);
       ok(typeof $.fn.durationer === 'function', 'The Durationer plugin is registered');
    });
   
    asyncTest('initialization', function(){
        expect(5);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        
        $elt.on('create.durationer', function(){
            ok(typeof $elt.data('ui.durationer') === 'object');
            var $controls = $container.find('.duration-ctrl');
            equal($controls.length, 3, 'The plugins has created controls');
            ok(typeof $controls.data('ui.incrementer') === 'object', 'The plugins has initialized incrementer on controls');
            
            start();
        });
        $elt.durationer();
    });
      
     asyncTest('update seconds', function(){
        expect(3);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        
        $elt.on('create.durationer', function(){
            var $secCtrl = $container.find("[data-duration-type='seconds']");
            equal($secCtrl.length, 1, 'The seconds controls exists');
            $secCtrl.val('10').trigger('change');
        });
        $elt.on('update.durationer', function(){
            
            equal($elt.val(), '00:00:10', 'The element value has been synchronized');
            
            start();
        });
        $elt.durationer();
    });
    
    asyncTest('update minutes', function(){
        expect(3);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        
        $elt.on('create.durationer', function(){
            var $minCtrl = $container.find("[data-duration-type='minutes']");
            equal($minCtrl.length, 1, 'The minutes controls exists');
            $minCtrl.val('9').trigger('change');
        });
        $elt.on('update.durationer', function(){
            
            equal($elt.val(), '00:09:00', 'The element value has been synchronized');
            
            start();
        });
        $elt.durationer();
    });
    
    asyncTest('update hours from incrementer', function(){
        expect(3);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        
        $elt.on('create.durationer', function(){
            var $hourCtrlInc = $container.find("[data-duration-type='hours']").next('.incrementer-ctrl:first').find('.inc');
            
            
            equal($hourCtrlInc.length, 1, 'The hours incrementer button controls exists');
            
            $hourCtrlInc.click();
            
        });
        $elt.on('update.durationer', function(){
            
            equal($elt.val(), '01:00:00', 'The element value has been synchronized');
            
            start();
        });
        $elt.durationer();
    });
    
    asyncTest('initialization with value', function(){
        expect(5);
        
        var $container = $('#container-2');
        ok($container.length === 1, 'Test the fixture is available');
        var $elt = $(':text', $container);
        
        $elt.on('create.durationer', function(){
            
            var $hourCtrl = $container.find("[data-duration-type='hours']");
            var $minCtrl = $container.find("[data-duration-type='minutes']");
            var $secCtrl = $container.find("[data-duration-type='seconds']");
            
            equal($hourCtrl.val(), '2');
            equal($minCtrl.val(), '39');
            equal($secCtrl.val(), '59');
            
            equal($elt.val(), "02:39:59");
           
            start();
        });
        $elt.durationer();
    });
    
     module('Durationer Data Attr Test');
     
     asyncTest('initialization', function(){
        expect(5);
        
        var $container = $('#container-3');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        
        $elt.on('create.durationer', function(){
            ok(typeof $elt.data('ui.durationer') === 'object');
            var $controls = $container.find('.duration-ctrl');
            equal($controls.length, 3, 'The plugins has created controls');
            ok(typeof $controls.data('ui.incrementer') === 'object', 'The plugins has initialized incrementer on controls');
            
            start();
        });
       
        durationer($container);
    });
});



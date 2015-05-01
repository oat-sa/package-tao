define(['jquery', 'ui',  'ui/incrementer'], function($, ui, incrementer){
    
    
    module('Incrementer Stand Alone Test');
   
    test('plugin', function(){
        expect(1);
        ok(typeof $.fn.incrementer === 'function', 'The Durationer plugin is registered');
    });

    test('initialization', function(){
        expect(4);

        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');

        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');

        $elt.on('create.incrementer', function(){
            ok(typeof $elt.data('ui.incrementer') === 'object');

            var $control = $container.find('.ctrl > a');
            equal($control.length, 2, 'The plugins has created controls');
        });
        $elt.incrementer();
    });

    test('update seconds', function(){
        expect(3);

        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');

        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');

        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
        $elt.on('increment.incrementer', function(){
            equal($elt.val(), 2, "The value has been incremented");
        });

        $elt.incrementer({
            min : 0,
            max : 10,
            step : 2
        });
    });
    
    test('increment decimal 0.5 + 1.00 = 1', function(){
        expect(3);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        $elt.val(0.5);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
         $elt.on('increment.incrementer', function(){
            equal($elt.val(), 1, "The value has been incremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
        
    });
    
    test('increment decimal 1.01 + 1.00 = 2', function(){
        expect(3);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        $elt.val(1.01);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
         $elt.on('increment.incrementer', function(){
            equal($elt.val(), 2, "The value has been incremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
    });
    
    test('decrement decimal 0.5 - 1.00 = 0', function(){
        expect(3);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        $elt.val(0.5);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.dec').click();
        });
         $elt.on('decrement.incrementer', function(){
            equal($elt.val(), 0, "The value has been decremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
    });
    
    test('decrement decimal 0 - 1.00 = -1', function(){
        expect(3);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        $elt.val(0);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.dec').click();
        });
         $elt.on('decrement.incrementer', function(){
            equal($elt.val(), -1, "The value has been decremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
    });
    
     module('Incrementer Data Attr Test');
     
     test('initialization', function(){
        expect(3);
        
        var $container = $('#container-2');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
         $elt.on('increment.incrementer', function(){
            equal($elt.val(), 5, "The value has been incremented");
        });
       
        incrementer($container);
    });
    
     test('decimal', function(){
        
        var $container = $('#container-3');
        ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        ok($elt.length === 1, 'Test input is available');
        
        incrementer($container);
        var options = $elt.data('ui.incrementer');
        
        equal(options.decimal, 2, 'option decimal ok');
        equal(options.step, 1, 'option step ok');
    });
});



define(['jquery', 'ui',  'ui/incrementer'], function($, ui, incrementer){
    
    
    QUnit.module('Incrementer Stand Alone Test');
   
    QUnit.test('plugin', function(assert){
        QUnit.expect(1);
        assert.ok(typeof $.fn.incrementer === 'function', 'The Durationer plugin is registered');
    });

    QUnit.test('initialization', function(assert){
        QUnit.expect(4);

        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');

        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');

        $elt.on('create.incrementer', function(){
            assert.ok(typeof $elt.data('ui.incrementer') === 'object');

            var $control = $container.find('.ctrl > a');
            assert.equal($control.length, 2, 'The plugins has created controls');
        });
        $elt.incrementer();
    });

    QUnit.test('update seconds', function(assert){
        QUnit.expect(3);

        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');

        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');

        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
        $elt.on('increment.incrementer', function(){
            assert.equal($elt.val(), 2, "The value has been incremented");
        });

        $elt.incrementer({
            min : 0,
            max : 10,
            step : 2
        });
    });
    
    QUnit.test('increment decimal 0.5 + 1.00 = 1', function(assert){
        QUnit.expect(3);
        
        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');
        $elt.val(0.5);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
         $elt.on('increment.incrementer', function(){
            assert.equal($elt.val(), 1, "The value has been incremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
        
    });
    
    QUnit.test('increment decimal 1.01 + 1.00 = 2', function(assert){
        QUnit.expect(3);
        
        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');
        $elt.val(1.01);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
         $elt.on('increment.incrementer', function(){
            assert.equal($elt.val(), 2, "The value has been incremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
    });
    
    QUnit.test('decrement decimal 0.5 - 1.00 = 0', function(assert){
        QUnit.expect(3);
        
        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');
        $elt.val(0.5);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.dec').click();
        });
         $elt.on('decrement.incrementer', function(){
            assert.equal($elt.val(), 0, "The value has been decremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
    });
    
    QUnit.test('decrement decimal 0 - 1.00 = -1', function(assert){
        QUnit.expect(3);
        
        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');
        $elt.val(0);
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.dec').click();
        });
         $elt.on('decrement.incrementer', function(){
            assert.equal($elt.val(), -1, "The value has been decremented");
        });
        
        $elt.incrementer({
            step: 1.00
        });
    });
    
     QUnit.module('Incrementer Data Attr Test');
     
     QUnit.test('initialization', function(assert){
        QUnit.expect(3);
        
        var $container = $('#container-2');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');
        
        $elt.on('create.incrementer', function(){
            $container.find('.ctrl > a.inc').click();
        });
         $elt.on('increment.incrementer', function(){
            assert.equal($elt.val(), 5, "The value has been incremented");
        });
       
        incrementer($container);
    });
    
     QUnit.test('decimal', function(assert){
        
        var $container = $('#container-3');
        assert.ok($container.length === 1, 'Test the fixture is available');
        
        var $elt = $(':text', $container);
        assert.ok($elt.length === 1, 'Test input is available');
        
        incrementer($container);
        var options = $elt.data('ui.incrementer');
        
        assert.equal(options.decimal, 2, 'option decimal ok');
        assert.equal(options.step, 1, 'option step ok');
    });
});



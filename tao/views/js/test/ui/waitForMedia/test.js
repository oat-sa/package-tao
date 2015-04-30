define(['jquery', 'ui/waitForMedia'], function($){

    module('WaitForMedia Stand Alone Test');

    asyncTest('waitForMedia long form', function(){
        ok(typeof $.fn.waitForMedia === 'function', 'The waitForMedia plugin is registered');

        var $fixture = $('#qunit-fixture');
        var $elt = $fixture.find('#div1');
        ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('loaded.wait', function(){
            //expect this 4 times because there are 4 <img>
            ok(true, 'an image has been loaded');
        });
        $elt.on('all-loaded.wait', function(){
            //expect this once at the end of the loading chain
            ok(true, 'all images has been loaded');
            start();
        });
        $elt.waitForMedia();
    });

    asyncTest('waitForMedia short form', function(){
        expect(1);
        var $fixture = $('#qunit-fixture');
        var $elt = $fixture.find('#div1');
        $elt.waitForMedia(function(){
            //expect this once at the end of the loading chain
            ok(true, 'all images has been loaded');
            start();
        });

    });
    
    asyncTest('waitForMedia no images', function(){
        expect(1);
        var $fixture = $('#qunit-fixture');
        var $elt = $fixture.find('#div2');
        $elt.waitForMedia(function(){
            ok(true, 'callback function was executed');
            start();
        });

    });

});

define(['jquery', 'ui/waitForMedia'], function($){

    QUnit.module('WaitForMedia Stand Alone Test');

    QUnit.asyncTest('waitForMedia long form', function(assert){
        assert.ok(typeof $.fn.waitForMedia === 'function', 'The waitForMedia plugin is registered');

        var $fixture = $('#qunit-fixture');
        var $elt = $fixture.find('#div1');
        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('loaded.wait', function(){
            //expect this 4 times because there are 4 <img>
            assert.ok(true, 'an image has been loaded');
        });
        $elt.on('all-loaded.wait', function(){
            //expect this once at the end of the loading chain
            assert.ok(true, 'all images has been loaded');
            QUnit.start();
        });
        $elt.waitForMedia();
    });

    QUnit.asyncTest('waitForMedia short form', function(assert){
        QUnit.expect(1);
        var $fixture = $('#qunit-fixture');
        var $elt = $fixture.find('#div1');
        $elt.waitForMedia(function(){
            //expect this once at the end of the loading chain
            assert.ok(true, 'all images has been loaded');
            QUnit.start();
        });

    });
    
    QUnit.asyncTest('waitForMedia no images', function(assert){
        QUnit.expect(1);
        var $fixture = $('#qunit-fixture');
        var $elt = $fixture.find('#div2');
        $elt.waitForMedia(function(){
            assert.ok(true, 'callback function was executed');
            QUnit.start();
        });

    });

});

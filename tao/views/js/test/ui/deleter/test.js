define(['jquery', 'ui', 'ui/deleter'], function($, ui, deleter){
    'use strict';

    QUnit.module('Deleter Stand Alone Test');

    QUnit.test('plugin', function(assert){
       QUnit.expect(1);
       assert.ok(typeof $.fn.deleter === 'function', 'The Deleter plugin is registered');
    });

    QUnit.asyncTest('Initialization', function(assert){
        QUnit.expect(4);

        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');

        var $elt = $('.deleter', $container);
        assert.ok($elt.length === 1, 'Deleter link is available');

        var $target = $('.content', $container);
        assert.ok($target.length === 1, 'Target is available');

        $elt.on('create.deleter', function(){
            assert.ok(typeof $elt.data('ui.deleter') === 'object', 'The element is runing the plugin');
            QUnit.start();
        });
        $elt.deleter({
            target : $target,
            confirm : false
        });
    });

    QUnit.asyncTest('Deleting', function(assert){
        QUnit.expect(4);

        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');

        var $elt = $('.deleter', $container);
        assert.ok($elt.length === 1, 'Deleter link is available');

        var $target = $('.content', $container);
        assert.ok($target.length === 1, 'Target is available');

        $elt.on('create.deleter', function(){
            $elt.trigger('click');
        });

         $elt.on('deleted.deleter', function(e){
            if(e.namespace === 'deleter'){
                assert.ok($('.content', $container).length === 0, 'Target doesn\'t exists anymore');
                QUnit.start();
            }
        });
        $elt.deleter({
            target : $target,
            confirm : false
        });
    });


    QUnit.module('Deleter Data Attr Test');

     QUnit.asyncTest('Initialization', function(assert){
        QUnit.expect(4);

        //prevent the confirm message to lock the test
        window.confirm = function(){
            return true;
        };

        var $container = $('#container-2');
        assert.ok($container.length === 1, 'Test the fixture is available');

        var $elt = $('.deleter', $container);
        assert.ok($elt.length === 1, 'Deleter link is available');

        var $target = $('.content', $container);
        assert.ok($target.length === 1, 'Target is available');

        $elt.on('deleted.deleter', function(e){
            if(e.namespace === 'deleter'){
                assert.ok($('.content', $container).length === 0, 'Target doesn\'t exists anymore');
                QUnit.start();
            }
        });

        deleter($container);
        $elt.trigger('click');
    });

    QUnit.module('Undo');

    QUnit.asyncTest('Undo Delete', function(assert){
        QUnit.expect(5);

        var $container = $('#container-1');
        assert.ok($container.length === 1, 'Test the fixture is available');

        var $elt = $('.deleter', $container);
        assert.ok($elt.length === 1, 'Deleter link is available');

        var $target = $('.content', $container);
        assert.ok($target.length === 1, 'Target is available');

        $elt.on('create.deleter', function(){
            $elt.trigger('click');
        });

        $elt.on('delete.deleter', function(e){
            if(e.namespace === 'deleter'){
                setTimeout(function(){
                    var $undo = $('body').find('a.undo');
                    assert.ok( $undo.length === 1, 'the undo link is there');
                    $undo.trigger('click');
                }, 100);
            }
        });
        $elt.on('undo.deleter', function(e){
            var $target = $('.content', $container);
            assert.ok($target.length === 1, 'Target is available');
            QUnit.start();
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



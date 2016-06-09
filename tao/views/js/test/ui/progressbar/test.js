define(['jquery', 'ui/progressbar'], function($){
    'use strict';

    var containerId ='mypg';

    QUnit.module('ProgressBar');

    QUnit.test('plugin', function(assert){
       QUnit.expect(1);
       assert.ok(typeof $.fn.progressbar === 'function', 'The progressbar plugin is registered');
    });

    QUnit.asyncTest('Initialization', function(assert){
        QUnit.expect(3);

        var $elt = $('#' + containerId);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('create.progressbar', function(){

            assert.ok($elt.hasClass('progressbar'), 'the element has the right class');
            assert.equal($elt.find('span').length, 1, 'the sub element has been inserted');

            QUnit.start();
        });
        $elt.progressbar();
    });

    QUnit.asyncTest('Success style', function(assert){
        QUnit.expect(3);

        var $elt = $('#' + containerId);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('create.progressbar', function(){

            assert.equal($elt.find('span').length, 1, 'the sub element has been inserted');
            assert.ok($elt.hasClass('success'), 'the sub element has the success CSS class');

            QUnit.start();
        });
        $elt.progressbar({
            style : 'success'
        });
    });



    QUnit.asyncTest('Destroy', function(assert){
        QUnit.expect(5);

        var $elt = $('#' + containerId);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('create.progressbar', function(){

            assert.ok($elt.hasClass('progressbar'), 'the element has the right class');
            assert.equal($elt.find('span').length, 1, 'the sub element has been inserted');

        }).on('destroy.progressbar', function(){

            assert.ok( ! $elt.hasClass('progressbar'), 'the element class has been removed');
            assert.equal($elt.find('span').length, 0, 'the sub element has been removed');

            QUnit.start();
        });

        $elt.progressbar()
            .progressbar('destroy');

    });

    QUnit.asyncTest('update', function(assert){
        QUnit.expect(3);

        var $elt = $('#' + containerId);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('update.progressbar', function(e, value){
            assert.equal(value, 55, 'the given value matches');
            assert.equal($elt.find('span')[0].style.width, '55%', 'the sub element width matches the value');

            QUnit.start();
        });

        $elt.progressbar()
            .progressbar('update', 55);

    });

    QUnit.asyncTest('set value', function(assert){
        QUnit.expect(3);

        var $elt = $('#' + containerId);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('update.progressbar', function(e, value){
            assert.equal(value, 42, 'the given value matches');
            assert.equal($elt.find('span')[0].style.width, '42%', 'the sub element width matches the value');

            QUnit.start();
        });

        $elt.progressbar()
            .progressbar('value', 42);

    });

    QUnit.asyncTest('get value', function(assert){
        QUnit.expect(2);

        var $elt = $('#' + containerId);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('update.progressbar', function(){

            assert.equal($elt.progressbar('value'), 66, 'Get the current progress value');

            QUnit.start();
        });

        $elt.progressbar()
            .progressbar('value', 66);
    });

    QUnit.asyncTest('show progress', function(assert){
        QUnit.expect(2);

        var $elt = $('#' + containerId);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('update.progressbar', function(e, value){
            assert.equal($elt.find('span').text(), '38%', 'the sub element contains the progress text');

            QUnit.start();
        });

        $elt.progressbar({showProgress : true})
            .progressbar('update', 38);

    });
});



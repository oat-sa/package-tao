define(['jquery', 'ui', 'ui/previewer'], function($, ui, previewer){
    'use strict';

    QUnit.module('Previewer Stand Alone Test');
    QUnit.test('plugin', function(assert){
       QUnit.expect(1);
       assert.ok(typeof $.fn.previewer === 'function', 'The Previewer plugin is registered');
    });

    QUnit.asyncTest('Initialization', function(assert){
        QUnit.expect(3);
        var $fixture = $('#qunit-fixture');
        var $elt = $('#p1', $fixture);
        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('create.previewer', function(){
            assert.ok(typeof $elt.data('ui.previewer') === 'object', 'The element is runing the plugin');
            assert.ok($elt.hasClass('previewer'), 'The element has the right css clas');
            QUnit.start();
        });
        $elt.previewer({
            url: 'http://taotesting.com/sites/tao/themes/tao/img/tao_logo.png',
            type: 'image/png'
        });
    });

    QUnit.asyncTest('Image preview', function(assert){
        QUnit.expect(5);

        var options     = {
            url    : 'http://taotesting.com/sites/tao/themes/tao/img/tao_logo.png',
            mime    : 'image/png',
            width   : 50,
            height  : 50
        };
        var $fixture    = $('#qunit-fixture');
        var $elt        = $('#p1', $fixture);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('create.previewer', function(){
            assert.equal($elt.find('img').length, 1, 'The image element is created');
            assert.equal($elt.find('img').attr('src'), options.url, 'The image src is set');
            assert.equal($elt.find('img').width(), options.width, 'The image width is set');
            assert.equal($elt.find('img').height(), options.height, 'The image height is set');
            QUnit.start();
        });
        $elt.previewer(options);
    });

    QUnit.asyncTest('Data Attribute', function(assert){
        QUnit.expect(4);

        var options     = {
            url    : 'http://techslides.com/demos/sample-videos/small.mp4',
            mime    : 'video/mp4'
        };
        var $fixture    = $('body');
        var $elt        = $('#p2', $fixture);

        assert.ok($elt.length === 1, 'Test the fixture is available');

        $elt.on('create.previewer', function(){
            setTimeout(function(){
                assert.equal($elt.find('video').length, 1, 'The video element is created');
                assert.equal($elt.find('video').attr('src'), options.url, 'The video src is set');
                assert.equal($elt.find('.mejs-container').length, 1, 'The media element player is set up');
                QUnit.start();
            }, 500);
        });
        previewer($fixture);
    });
});

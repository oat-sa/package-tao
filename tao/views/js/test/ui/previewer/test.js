define(['jquery', 'ui', 'ui/previewer'], function($, ui, previewer){
      
    module('Previewer Stand Alone Test');
    test('plugin', function(){
       expect(1);
       ok(typeof $.fn.previewer === 'function', 'The Previewer plugin is registered');
    });
   
    asyncTest('Initialization', function(){
        expect(3);
        var $fixture = $('#qunit-fixture');
        var $elt = $('#p1', $fixture);
        ok($elt.length === 1, 'Test the fixture is available');
        
        $elt.on('create.previewer', function(){
            ok(typeof $elt.data('ui.previewer') === 'object', 'The element is runing the plugin');
            ok($elt.hasClass('previewer'), 'The element has the right css clas');
            start();
        });
        $elt.previewer({
            url: 'http://taotesting.com/sites/tao/themes/tao/img/tao_logo.png',
            type: 'image/png' 
        });
    });
    asyncTest('Image preview', function(){
        expect(5);

        var options     = {
            url    : 'http://taotesting.com/sites/tao/themes/tao/img/tao_logo.png',
            mime    : 'image/png',
            width   : 50,
            height  : 50
        };
        var $fixture    = $('#qunit-fixture');
        var $elt        = $('#p1', $fixture);

        ok($elt.length === 1, 'Test the fixture is available');
        
        $elt.on('create.previewer', function(){
            equal($elt.find('img').length, 1, 'The image element is created');
            equal($elt.find('img').attr('src'), options.url, 'The image src is set');
            equal($elt.find('img').width(), options.width, 'The image width is set');
            equal($elt.find('img').height(), options.height, 'The image height is set');
            start();
        });
        $elt.previewer(options);
    });
    asyncTest('Data Attribute', function(){
        expect(6);

        var options     = {
            url    : 'http://techslides.com/demos/sample-videos/small.mp4',
            mime    : 'video/mp4',
            width   : 200,
            height  : 150
        };
        var $fixture    = $('body');
        var $elt        = $('#p2');//, $fixture);
        $elt.width(options.width);
        $elt.height(options.height);

        ok($elt.length === 1, 'Test the fixture is available');
        
        $elt.on('create.previewer', function(){
            equal($elt.find('video').length, 1, 'The video element is created');
            equal($elt.find('video').attr('src'), options.url, 'The video src is set');
            equal($elt.find('video').width(), options.width, 'The video width is set');
            equal($elt.find('video').height(), options.height, 'The video height is set');
            equal($elt.find('.mejs-container').length, 1, 'The media element player is set up');
            start();
        });
        previewer($fixture);
    });
});

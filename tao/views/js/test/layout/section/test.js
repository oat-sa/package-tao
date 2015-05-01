define(['jquery', 'layout/section'], function($, section){
        
    module('layout/section');

    test('module', function(){
        ok(typeof section === 'object', 'The module expose an object');
    });

    asyncTest('init sections', function(){
        expect(4);
        var $testScope = $('#qunit-fixture .multiple');

        equal($testScope.length, 1, 'the test scope exists');

        ok(typeof section.init === 'function', 'section has an init function');

        $testScope.on('init.section', function(){
            ok(true, 'the section triggers an init event on the scope');
            start();
        });
        ok(typeof section.init($testScope, { history : false }) === 'object', 'section.init() returns an object');
    });

    asyncTest('init multiple sections', function(){
        expect(8);
        var $testScope = $('#qunit-fixture .multiple');

        equal($testScope.length, 1, 'the test scope exists');

        $testScope.on('activate.section', function(){
            ok($('.tab-container li:first', $testScope).hasClass('active'), 'the first opener is active');
            ok(!$('.tab-container li:eq(2)', $testScope).hasClass('active'), 'the 3rd opener is not active');
            ok(!$('.tab-container li:last', $testScope).hasClass('active'), 'the last opener is not active');
            ok($('.content-panel:first', $testScope).css('display') !== 'none', 'the first panel is shown');
            ok($('.content-panel:eq(2)', $testScope).css('display') === 'none', 'the 3rd panel is hidden');
            ok($('.content-panel:last', $testScope).css('display') === 'none', 'the last panel is hidden');

            start();
        });
        ok(typeof section.init($testScope, { history : false }) === 'object', 'section.init() returns an object');
    });

    asyncTest('switch from sections', function(){
        expect(10);
        var $testScope = $('#qunit-fixture .multiple');

        equal($testScope.length, 1, 'the test scope exists');

        ok(typeof section.init($testScope, { history : false }) === 'object', 'section.init() returns an object');

        ok($('.tab-container li:first', $testScope).hasClass('active'), 'the first opener is active');
        ok(!$('.tab-container li:eq(1)', $testScope).hasClass('active'), 'the 2nd opener is not active');
        
        ok($('.content-panel:first', $testScope).css('display') !== 'none', 'the first panel is shown');
        ok($('.content-panel:eq(1)', $testScope).css('display') === 'none', 'the 2nd panel is hidden');

        $testScope.on('activate.section', function(){

            ok(!$('.tab-container li:first', $testScope).hasClass('active'), 'the first opener isn\'t active anymore');
            ok($('.tab-container li:eq(1)', $testScope).hasClass('active'), 'the 2nd opener is now active');
            
            ok($('.content-panel:first', $testScope).css('display') === 'none', 'the first panel is now hidden');
            ok($('.content-panel:eq(1)', $testScope).css('display') !== 'none', 'the 2nd panel is now shown');

            start();
        });
        $('.tab-container li:eq(1)').trigger('click');
    });
});



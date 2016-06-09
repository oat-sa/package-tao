define(['jquery', 'layout/section'], function($, section){
        
    QUnit.module('layout/section');

    QUnit.test('module', function(assert){
        assert.ok(typeof section === 'object', 'The module expose an object');
    });

    QUnit.asyncTest('init sections', function(assert){
        QUnit.expect(4);
        var $testScope = $('#qunit-fixture .multiple');

        assert.equal($testScope.length, 1, 'the test scope exists');

        assert.ok(typeof section.init === 'function', 'section has an init function');

        $testScope.on('init.section', function(){
            assert.ok(true, 'the section triggers an init event on the scope');
            QUnit.start();
        });
        assert.ok(typeof section.init($testScope, { history : false }) === 'object', 'section.init() returns an object');
    });

    QUnit.asyncTest('init multiple sections', function(assert){
        QUnit.expect(8);
        var $testScope = $('#qunit-fixture .multiple');

        assert.equal($testScope.length, 1, 'the test scope exists');

        $testScope.on('activate.section', function(){
            assert.ok($('.tab-container li:first', $testScope).hasClass('active'), 'the first opener is active');
            assert.ok(!$('.tab-container li:eq(2)', $testScope).hasClass('active'), 'the 3rd opener is not active');
            assert.ok(!$('.tab-container li:last', $testScope).hasClass('active'), 'the last opener is not active');
            assert.ok($('.content-panel:first', $testScope).css('display') !== 'none', 'the first panel is shown');
            assert.ok($('.content-panel:eq(2)', $testScope).css('display') === 'none', 'the 3rd panel is hidden');
            assert.ok($('.content-panel:last', $testScope).css('display') === 'none', 'the last panel is hidden');

            QUnit.start();
        });
        assert.ok(typeof section.init($testScope, { history : false }) === 'object', 'section.init() returns an object');
    });

    QUnit.asyncTest('switch from sections', function(assert){
        QUnit.expect(10);
        var $testScope = $('#qunit-fixture .multiple');

        assert.equal($testScope.length, 1, 'the test scope exists');

        assert.ok(typeof section.init($testScope, { history : false }) === 'object', 'section.init() returns an object');

        assert.ok($('.tab-container li:first', $testScope).hasClass('active'), 'the first opener is active');
        assert.ok(!$('.tab-container li:eq(1)', $testScope).hasClass('active'), 'the 2nd opener is not active');
        
        assert.ok($('.content-panel:first', $testScope).css('display') !== 'none', 'the first panel is shown');
        assert.ok($('.content-panel:eq(1)', $testScope).css('display') === 'none', 'the 2nd panel is hidden');

        $testScope.on('activate.section', function(){

            assert.ok(!$('.tab-container li:first', $testScope).hasClass('active'), 'the first opener isn\'t active anymore');
            assert.ok($('.tab-container li:eq(1)', $testScope).hasClass('active'), 'the 2nd opener is now active');
            
            assert.ok($('.content-panel:first', $testScope).css('display') === 'none', 'the first panel is now hidden');
            assert.ok($('.content-panel:eq(1)', $testScope).css('display') !== 'none', 'the 2nd panel is now shown');

            QUnit.start();
        });
        $('.tab-container li:eq(1)').trigger('click');
    });
});



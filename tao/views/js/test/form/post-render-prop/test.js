define(['jquery', 'form/post-render-props'], function($, postRenderProps){
    'use strict';

    QUnit.module('form/post-render-props');

    QUnit.test('module', function(assert){
        assert.ok(typeof postRenderProps === 'object', 'The module expose an object');
    });

    QUnit.test('index icon visibility in Advanced mode', function(assert) {
        var $testScope = $('#qunit-fixture');

        // setup advanced mode
        // mode state is stored in .property-mode, as a toggle for the opposite mode
        $testScope.find('.property-mode').removeClass('property-mode-advanced');
        $testScope.find('.property-mode').addClass('property-mode-simple');

        postRenderProps.init();

        var indexIconVisibility = $testScope.find('.icon-find').is(':visible');
        assert.ok(!indexIconVisibility, 'index icon should be invisible!');
    });

    QUnit.test('index icon visibility in Simple mode', function(assert) {
        var $testScope = $('#qunit-fixture');

        // setup simple mode
        $testScope.find('.property-mode').addClass('property-mode-advanced');
        $testScope.find('.property-mode').removeClass('property-mode-simple');

        postRenderProps.init();

        var indexIconVisibility = $testScope.find('.icon-find').is(':visible');
        assert.ok(indexIconVisibility, 'index icon should be visible!');
    });
});



/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'taoQtiTest/testRunner/actionBar/collapseReview'
], function($, _, collapseReview) {
    'use strict';

    // global mock for button config
    var configMock = {
        label: 'Collapse review',
        icon: 'anchor',
        hook: 'taoQtiTest/testRunner/actionBar/collapseReview'
    };


    QUnit.module('collapseReview');


    QUnit.test('module', function(assert) {
        assert.equal(typeof collapseReview, 'object', "The collapseReview module exposes an object");
    });

    var collapseReviewApi = [
        { name : 'init', title : 'init' },
        { name : 'clear', title : 'clear' },
        { name : 'render', title : 'render' },
        { name : 'bindTo', title : 'bindTo' },
        { name : 'bindEvents', title : 'bindEvents' },
        { name : 'unbindEvents', title : 'unbindEvents' },
        { name : 'isVisible', title : 'isVisible' },
        { name : 'hasMenu', title : 'hasMenu' },
        { name : 'isMenuOpen', title : 'isMenuOpen' },
        { name : 'closeMenu', title : 'closeMenu' },
        { name : 'openMenu', title : 'openMenu' },
        { name : 'toggleMenu', title : 'toggleMenu' },
        { name : 'setActive', title : 'setActive' },
        { name : 'trigger', title : 'trigger' },
        { name : 'on', title : 'on' },
        { name : 'off', title : 'off' },
        { name : 'setup', title : 'setup' },
        { name : 'action', title : 'action' },
        { name : 'menuAction', title : 'menuAction' }
    ];

    QUnit
        .cases(collapseReviewApi)
        .test('API ', function(data, assert) {
            assert.equal(typeof collapseReview[data.name], 'function', 'The collapseReview module exposes a "' + data.title + '" function');
        });


    QUnit.test('button enabled/disabled', function(assert) {
        var testContextMock = {
            reviewScreen: false,
            considerProgress: true,
            categories : []
        };
        
        testContextMock.reviewScreen = true;
        collapseReview.init('collapseReview', configMock, testContextMock, {});
        assert.ok(!collapseReview.isVisible(), 'The collapseReview button is not visible when the test taker screen is enabled and when the special category is not set');

        testContextMock.reviewScreen = false;
        collapseReview.init('collapseReview', configMock, testContextMock, {});
        assert.ok(!collapseReview.isVisible(), 'The collapseReview button is not visible when the test taker screen is disabled and when the special category is not set');
        
        //add special category to enable markForReview
        testContextMock.categories.push('x-tao-option-reviewScreen');
        
        testContextMock.reviewScreen = true;
        collapseReview.init('collapseReview', configMock, testContextMock, {});
        assert.ok(collapseReview.isVisible(), 'The collapseReview button is visible when the test taker screen is enabled and when the special category is set');

        testContextMock.reviewScreen = false;
        collapseReview.init('collapseReview', configMock, testContextMock, {});
        assert.ok(!collapseReview.isVisible(), 'The collapseReview button is not visible when the test taker screen is disabled and when the special category is set');
    });


    QUnit.asyncTest('button install/uninstall', function(assert) {
        var callExpected = true;
        var callExecuted = false;
        var i = 0;
        var testRunnerMock = {
            testReview: {
                toggle: function() {
                    if (callExpected && ++i ==2) {
                        assert.ok(true, 'The button must trigger two calls to toggle');
                        callExecuted = true;
                        QUnit.start();
                    }
                }
            }
        };

        var testContextMock = {
            reviewScreen: true,
            considerProgress: true,
            categories : ['x-tao-option-reviewScreen']
        };

        var $container = $('#mark-for-review-1');
        var $btn;

        collapseReview.init('collapseReview', configMock, testContextMock, testRunnerMock);
        $btn = collapseReview.render();
        $container.append($btn);

        $btn.click();

        collapseReview.clear();

        _.delay(function() {
            if(!callExecuted){
                assert.ok(true, 'The button is uninstalled and did not trigger a call to collapseReview');
                QUnit.start();
            }
        }, 600);
        $btn.click();

    });


    QUnit.test('button active/idle', function(assert) {
        var testRunnerMock = {
            testReview: {
                toggle: function () {
                },
                hidden: true
            }
        };

        var testContextMock = {
            reviewScreen: true,
            considerProgress: true,
            categories : ['x-tao-option-reviewScreen']
        };

        var $container = $('#mark-for-review-2');
        var $btn;

        collapseReview.init('collapseReview', configMock, testContextMock, testRunnerMock);
        $btn = collapseReview.render();
        $container.append($btn);

        assert.ok(!$btn.hasClass('active'), 'The collapseReview button is idle when the component is hidden');

        $container = $('#mark-for-review-3');

        testRunnerMock.testReview.hidden = false;
        collapseReview.init('collapseReview', configMock, testContextMock, testRunnerMock);
        $btn = collapseReview.render();
        $container.append($btn);

        assert.ok($btn.hasClass('active'), 'The collapseReview button is activate when the component is visible');
    });


    QUnit.asyncTest('button click', function(assert) {
        var expectedHidden = true;
        var i = 1;
        var testRunnerMock = {
            testReview: {
                toggle: function () {
                    if(i === 1){
                        assert.ok(true, 'first call to toggle after init');
                    }else if(i > 1){
                        testRunnerMock.testReview.hidden = !testRunnerMock.testReview.hidden;
                        assert.equal(testRunnerMock.testReview.hidden, expectedHidden, 'The collapseReview button state must reflect the display state of the component');
                        assert.equal($btn.hasClass('active'), testRunnerMock.testReview.hidden, 'The collapseReview button is idle when the component is hidden, or active when the component is visible');
                        QUnit.start();
                    }
                    i++;
                },
                hidden: false
            }
        };

        var testContextMock = {
            reviewScreen: true,
            considerProgress: true
        };

        var $container = $('#mark-for-review-4');
        var $btn;

        collapseReview.init('collapseReview', configMock, testContextMock, testRunnerMock);
        $btn = collapseReview.render();
        $container.append($btn);

        assert.ok($btn.hasClass('active'), 'The collapseReview button is active when the component is visible');

        $btn.click();

        QUnit.stop();
        expectedHidden = false;
        $btn.click();

        QUnit.stop();
        expectedHidden = true;
        $btn.click();

        QUnit.stop();
        expectedHidden = false;
        $btn.click();
    });

});

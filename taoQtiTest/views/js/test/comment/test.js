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
    'taoQtiTest/testRunner/actionBar/comment'
], function($, _, comment) {
    'use strict';

    // global mock for button config
    var configMock = {
        label: 'Comment',
        icon: 'tag',
        hook: 'taoQtiTest/testRunner/actionBar/comment'
    };


    QUnit.module('comment');


    QUnit.test('module', function(assert) {
        assert.equal(typeof comment, 'object', "The comment module exposes an object");
    });


    var commentApi = [
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
        { name : 'menuAction', title : 'menuAction' },
        { name : 'closeForm', title : 'closeForm' },
        { name : 'submitForm', title : 'submitForm' }
    ];

    QUnit
        .cases(commentApi)
        .test('API ', function(data, assert) {
            assert.equal(typeof comment[data.name], 'function', 'The comment module exposes a "' + data.title + '" function');
        });


    QUnit.test('button enabled/disabled', function(assert) {
        var testContextMock = {
            allowComment: false
        };
        
        testContextMock.allowComment = true;
        comment.init('comment', configMock, testContextMock, {});
        assert.ok(comment.isVisible(), 'The comment button is visible when comment form is enabled');

        testContextMock.allowComment = false;
        comment.init('comment', configMock, testContextMock, {});
        assert.ok(!comment.isVisible(), 'The comment button is not visible when the comment form is disabled');
    });


    QUnit.test('button install/uninstall', function(assert) {
        var testRunnerMock = {};
        var testContextMock = {
            allowComment: true
        };

        var $container = $('#comment-1');
        var $btn;

        comment.init('comment', configMock, testContextMock, testRunnerMock);
        $btn = comment.render();
        $container.append($btn);

        assert.equal($container.find('[data-control="qti-comment"]').length, 1, 'The button must bring its own form.');
        assert.equal($container.find('[data-control="qti-comment-text"]').length, 1, 'The button must bring its own input field.');
        assert.equal($container.find('[data-control="qti-comment-cancel"]').length, 1, 'The button must bring its own cancel button.');
        assert.equal($container.find('[data-control="qti-comment-send"]').length, 1, 'The button must bring its own submit button.');

        assert.ok($container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be hidden by default');

        $btn.click();

        assert.ok(!$container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be visible when the button is activated');

        comment.clear();

        assert.ok($container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be hidden after uninstall');

        $btn.click();

        assert.ok($container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form cannot be displayed once the button is uninstalled');
    });


    QUnit.test('button active/idle', function(assert) {
        var testRunnerMock = {};
        var testContextMock = {
            allowComment: true
        };

        var $container = $('#comment-2');
        var $btn;

        comment.init('comment', configMock, testContextMock, testRunnerMock);
        $btn = comment.render();
        $container.append($btn);

        assert.ok($container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be hidden by default');
        assert.ok(!$btn.hasClass('active'), 'The comment button is idled when the form is hidden');

        $btn.click();

        assert.ok(!$container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be visible when the button is activated');
        assert.ok($btn.hasClass('active'), 'The comment button is activated when the form is open');

        comment.clear();
    });


    QUnit.test('button click', function(assert) {
        var testRunnerMock = {};
        var testContextMock = {
            allowComment: true
        };

        var $container = $('#comment-3');
        var $btn;

        comment.init('comment', configMock, testContextMock, testRunnerMock);
        $btn = comment.render();
        $container.append($btn);

        assert.ok($container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be hidden by default');
        assert.ok(!$btn.hasClass('active'), 'The comment button is idled when the form is hidden');

        $btn.click();

        assert.ok(!$container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be visible when the button is activated');
        assert.ok($btn.hasClass('active'), 'The comment button is activated when the form is open');

        $btn.find('[data-control="qti-comment-cancel"]').click();

        assert.ok($container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be hidden when canceled');
        assert.ok(!$btn.hasClass('active'), 'The comment button is idled when the form is hidden');

        $btn.click();

        assert.ok(!$container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be visible when the button is activated');
        assert.ok($btn.hasClass('active'), 'The comment button is activated when the form is open');

        $btn.click();

        assert.ok($container.find('[data-control="qti-comment"]').hasClass('hidden'), 'The comment form must be hidden when closed');
        assert.ok(!$btn.hasClass('active'), 'The comment button is idled when the form is hidden');

        comment.clear();
    });

});

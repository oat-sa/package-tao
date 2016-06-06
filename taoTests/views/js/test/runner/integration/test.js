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
 * @author Sam <sam@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'taoTests/runner/runner',
    'taoTests/test/runner/sample/minimalisticProvider',
    'taoTests/test/runner/sample/plugin/nextButton',
    'taoTests/test/runner/sample/plugin/previousButton',
    'taoTests/test/runner/sample/plugin/pauseButton',
    'taoTests/test/runner/sample/plugin/timer',
], function($, _, runner, minimalisticProvider, nextButton, previousButton, pauseButton, timer){
    'use strict';

    runner.registerProvider(minimalisticProvider.name, minimalisticProvider);

    QUnit.test('provider regsitration', function(assert){
        QUnit.expect(1);

        assert.deepEqual(runner.getProvider(minimalisticProvider.name), minimalisticProvider, 'The provider is regsitered');
    });

    QUnit.asyncTest('dom integration', function(assert){
        QUnit.expect(12);

        var $container = $('#qunit-fixture');

        runner(minimalisticProvider.name, {
            previousButtonh : previousButton,
            next : nextButton,
            pause : pauseButton,
            timer: timer
        }, {
            url : '/taoTests/views/js/test/runner/sample/minimalisticTest.json',
            renderTo : $container
        })
        .on('error', function(err){
            throw err;
        })
        .on('ready', function(){

            assert.equal($('.test-runner', $container).length, 1, 'The test runner container is attached');
            assert.equal($('.test-runner .content', $container).length, 1, 'The content area is attached');
            assert.equal($('.test-runner .navigation', $container).length, 1, 'The navigation area is attached');

            assert.equal($('.previous', $container).length, 1, 'The next button is attached');
            assert.equal($('.next', $container).length, 1, 'The previous button is attached');
            assert.equal($('.pause', $container).length, 1, 'The pause button is attached');

            assert.equal($('.next', $container).prop('disabled'), false, 'The next button is enabled');
            assert.equal($('.previous', $container).prop('disabled'), false, 'The previous button is enabled');


            assert.equal($('.content', $container).text(), '', 'The content is empty');

        })
        .after('renderitem', function(){

            assert.equal($('.next', $container).prop('disabled'), false, 'The next button is enabled');
            assert.equal($('.previous', $container).prop('disabled'), true, 'The previous button is disabled');

            assert.ok($('.content', $container).text().length > 0, 'The content is set');


            QUnit.start();
        })
        .init();
    });


    QUnit.asyncTest('visual integration', function(assert){
        QUnit.expect(1);

        var $container = $('#external');

        runner(minimalisticProvider.name, {
            previousButtonh : previousButton,
            next : nextButton,
            pause : pauseButton,
            timer: timer
        }, {
            url : '/taoTests/views/js/test/runner/sample/minimalisticTest.json',
            renderTo : $container
        })
        .on('error', function(err){
            throw err;
        })
        .on('ready', function(){

            assert.ok(true, 'the test is ready');


            QUnit.start();
        })
        .init();
    });
});

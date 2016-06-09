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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(['jquery', 'iframeResizer'], function($, iframeResizer){
    'use strict';

    var $fixture = $('#qunit-fixture');

    QUnit.test('parser structure', 2, function(assert){
        assert.ok(typeof iframeResizer === 'object');
        assert.ok(typeof iframeResizer.autoHeight === 'function');
    });

    QUnit.asyncTest('resize on load', 2, function(assert){

        var $frame = $('#iframe1', $fixture);
        assert.equal($frame.length, 1);

        iframeResizer.autoHeight($frame);
        $frame
            .on('load', function(){
                assert.equal(parseInt($frame.height(), 10), 500);
                QUnit.start();
            })
            .attr('src', 'js/test/iframeResizer/framecontent1.html');
    });

    QUnit.asyncTest('resize after load', 3, function(assert){

        var $frame = $('#iframe2', $fixture);
        assert.equal($frame.length, 1);

        iframeResizer.autoHeight($frame);
        $frame.on('load', function(){
                assert.equal(parseInt($frame.height(), 10), 200);
                setTimeout(function(){
                    assert.equal(parseInt($frame.height(), 10), 600);
                    QUnit.start();
                }, 2000);
            }).
            attr('src', 'js/test/iframeResizer/framecontent2.html');
    });

    QUnit.asyncTest('nested iframes', 2, function(assert){

        var $frame = $('#iframe3', $fixture);
        assert.equal($frame.length, 1);

        iframeResizer.autoHeight($frame, 'iframe');

        $frame.on('load', function(){
            var $nested = $frame.contents().find('iframe');

            iframeResizer
                .autoHeight($nested)
                .attr('src', 'framecontent2.html');

            setTimeout(function(){
                assert.ok(parseInt($frame.height(), 10) >= 600);   //the div that contains the iframe has a 604 height!
                QUnit.start();
            }, 2500);
        }).
        attr('src', 'js/test/iframeResizer/framecontent3.html');
    });
});



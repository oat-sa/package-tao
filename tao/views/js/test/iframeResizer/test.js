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
   
    var $fixture = $('#qunit-fixture');
   
    test('parser structure', function(){
        expect(2);
        
        ok(typeof iframeResizer === 'object');
        ok(typeof iframeResizer.autoHeight === 'function');
    });
    
    asyncTest('resize on load', function(){
        expect(2);
        
        var $frame = $('#iframe1', $fixture);
        equal($frame.length, 1);
        
        iframeResizer.autoHeight($frame);
        $frame
            .on('load', function(){
                equal(parseInt($frame.height(), 10), 500);
                start();    
            }).attr('src', 'js/test/iframeResizer/framecontent1.html');
    });
    
    asyncTest('resize after load', function(){
        expect(3);
        
        var $frame = $('#iframe2', $fixture);
        equal($frame.length, 1);
        
        iframeResizer.autoHeight($frame);
        $frame.on('load', function(){
                equal(parseInt($frame.height(), 10), 200);
                setTimeout(function(){
                    equal(parseInt($frame.height(), 10), 600);
                    start();   
                }, 2000);
            }).
            attr('src', 'js/test/iframeResizer/framecontent2.html');
    });
    
    asyncTest('nested iframes', function(){
        expect(2);
        
        var $frame = $('#iframe3', $fixture);
        equal($frame.length, 1);
        
        iframeResizer.autoHeight($frame, 'iframe');
        
        $frame.on('load', function(){
            var $nested = $frame.contents().find('iframe');
           
            iframeResizer
                .autoHeight($nested)
                .attr('src', 'framecontent2.html');
            
            setTimeout(function(){
                ok(parseInt($frame.height(), 10) >= 600);   //the div that contains the iframe has a 604 height!
                start();   
            }, 2500);
        }).
        attr('src', 'js/test/iframeResizer/framecontent3.html');
    });
});



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
define(['util/image'], function(imageUtil){
   
    var imageUrl = "img/tao_icon.png";
    var width = 53;
    var height = 40;  
 
    QUnit.test('utilitary structure', function(assert){
        QUnit.expect(2);
        
        assert.ok(typeof imageUtil === 'object');
        assert.ok(typeof imageUtil.getSize === 'function');
    });
    
    QUnit.asyncTest('getSize', function(assert){
        QUnit.expect(3);
        
        imageUtil.getSize(imageUrl, function(size){
            assert.notEqual(size, null, 'The size is not null');
            assert.equal(size.width, width, 'Check the image width');
            assert.equal(size.height, height, 'Check the image height');
            QUnit.start(); 
        }); 
    });
    
    QUnit.asyncTest('getSize with a short timeout', function(assert){
        QUnit.expect(3);
        
        imageUtil.getSize(imageUrl, 1, function(size){
            assert.notEqual(size, null, 'The size is not null');
            assert.equal(size.width, width, 'Check the image width');
            assert.equal(size.height, height, 'Check the image height');
            QUnit.start(); 
        }); 
    });

    QUnit.asyncTest('wrong url', function(assert){
        QUnit.expect(1);
        
        imageUtil.getSize("img/a-fake-url.png", function(size){
            strictEqual(size, null, 'The size is null');
            QUnit.start(); 
        }); 
    });
});



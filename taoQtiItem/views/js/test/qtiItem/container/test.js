/*
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 **/
define([
    'taoQtiItem/qtiItem/core/feedbacks/ModalFeedback',
    'taoQtiItem/qtiItem/helper/container'
], function (ModalFeedback, container){
    'use strict';
    
    QUnit.test('set/getEncodedData', function(assert){
        
        var fb = new ModalFeedback();
        fb.body('<p>AAA</p>');
        
        //set data
        container.setEncodedData(fb, 'customData1', 'customValueA');
        assert.equal(fb.body(), '<div class="x-tao-wrapper x-tao-customData1-customValueA"><p>AAA</p></div>');
        assert.equal(container.hasEncodedData(fb, 'customData1', 'customValueA'), true);
        assert.equal(container.getEncodedData(fb, 'customData1'), 'customValueA');
        
        //edit data
        container.setEncodedData(fb, 'customData1', 'customValueB');
        assert.equal(fb.body(), '<div class="x-tao-wrapper x-tao-customData1-customValueB"><p>AAA</p></div>');
        assert.equal(container.hasEncodedData(fb, 'customData1', 'customValueB'), true);
        assert.equal(container.getEncodedData(fb, 'customData1'), 'customValueB');
        
        //set another data
        container.setEncodedData(fb, 'customData2', 'customValueC');
        assert.equal(fb.body(), '<div class="x-tao-wrapper x-tao-customData1-customValueB x-tao-customData2-customValueC"><p>AAA</p></div>');
        assert.equal(container.hasEncodedData(fb, 'customData1', 'customValueB'), true);
        assert.equal(container.getEncodedData(fb, 'customData1'), 'customValueB');
        assert.equal(container.hasEncodedData(fb, 'customData2', 'customValueC'), true);
        assert.equal(container.getEncodedData(fb, 'customData2'), 'customValueC');
    });
    
    QUnit.test('removeEncodedData', function(assert){
        
        var fb = new ModalFeedback();
        fb.body('<p>AAA</p>');
        
        //set data
        container.setEncodedData(fb, 'customData1', 'customValueA');
        assert.equal(container.getEncodedData(fb, 'customData1'), 'customValueA');
        assert.equal(fb.body(), '<div class="x-tao-wrapper x-tao-customData1-customValueA"><p>AAA</p></div>');
        
        //remove data
        container.removeEncodedData(fb, 'customData1');
        assert.equal(container.getEncodedData(fb, 'customData1'), undefined);
        assert.equal(fb.body(), '<div class="x-tao-wrapper"><p>AAA</p></div>');
        
        //try removgin again
        container.removeEncodedData(fb, 'customData1');
        assert.equal(container.getEncodedData(fb, 'customData1'), undefined);
        
        //reset data
        container.setEncodedData(fb, 'customData1', 'customValueB');
        assert.equal(container.getEncodedData(fb, 'customData1'), 'customValueB');
        assert.equal(fb.body(), '<div class="x-tao-wrapper x-tao-customData1-customValueB"><p>AAA</p></div>');
        
    });

});


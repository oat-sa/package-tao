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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA
 */
define(['lodash', 'tpl!taoQtiItem/qtiXmlRenderer/tpl/element', 'taoQtiItem/qtiItem/helper/container'], function(_, tpl, containerHelper){
    'use strict';
    
    function encodeOutcomeInfo(fb){
        var relatedResponse = fb.data('relatedResponse');
        if(relatedResponse && relatedResponse.attr('identifier')){
            //encode the related outcome into a css class
            containerHelper.setEncodedData(fb, 'relatedOutcome', relatedResponse.attr('identifier'));
        }
    }
    
    return {
        qtiClass : 'modalFeedback',
        template : tpl,
        getData : function getData(fb, data){
            
            encodeOutcomeInfo(fb);
            data.body = fb.getBody().render(this);
            data.attributes.title = _.escape(data.attributes.title);
            return data;
        }
    };
});
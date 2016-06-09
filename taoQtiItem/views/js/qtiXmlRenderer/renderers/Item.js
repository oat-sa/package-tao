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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

define(['lodash', 'tpl!taoQtiItem/qtiXmlRenderer/tpl/item'], function(_, tpl, rendererConfig){

    'use strict';

    return {
        qtiClass : 'assessmentItem',
        template : tpl,
        getData : function(item, data){
            
            var renderer = this;
            var defaultData = {
                'class' : data.attributes.class || '',
                responses : [],
                outcomes : [],
                stylesheets : [],
                feedbacks : [],
                namespaces : item.getNamespaces(),
                schemaLocations : '',
                xsi: 'xsi:',//the standard namespace prefix for xml schema
                empty : item.isEmpty(),
                responseProcessing : item.responseProcessing ? item.responseProcessing.render(renderer) : '',
                apipAccessibility : item.getApipAccessibility() || ''
            };
            
            _.forIn(item.getSchemaLocations(), function(url, uri){
                defaultData.schemaLocations += uri+' '+url+' ';
            });
            defaultData.schemaLocations = defaultData.schemaLocations.trim();
            
            _.each(item.responses, function(response){
                defaultData.responses.push(response.render(renderer));
            });
            _.each(item.outcomes, function(outcome){
                defaultData.outcomes.push(outcome.render(renderer));
            });
            _.each(item.stylesheets, function(stylesheet){
                defaultData.stylesheets.push(stylesheet.render(renderer));
            });
            _.each(item.modalFeedbacks, function(feedback){
                defaultData.feedbacks.push(feedback.render(renderer));
            });
            
            data = _.merge({}, data || {}, defaultData);
            delete data.attributes.class;
            
            data.attributes.title = _.escape(data.attributes.title);
            
            return data;
        }
    };
});
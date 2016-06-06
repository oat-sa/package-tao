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
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * QTI Item Element model
 *
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/core/IdentifiedElement',
    'taoQtiItem/qtiItem/mixin/ContainerItemBody',
    'lodash',
    'jquery',
    'taoQtiItem/qtiItem/helper/util'
], function(Element, IdentifiedElement, Container, _, $, util){
    'use strict';

    var Item = IdentifiedElement.extend({
        qtiClass : 'assessmentItem',
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.relatedItem = this;
            this.stylesheets = {};
            this.responses = {};
            this.outcomes = {};
            this.modalFeedbacks = {};
            this.namespaces = {};
            this.schemaLocations = {};
            this.responseProcessing = null;
            this.apipAccessibility = null;
        },
        getInteractions : function(){
            var interactions = [];
            var elts = this.getComposingElements();
            for(var serial in elts){
                if(Element.isA(elts[serial], 'interaction')){
                    interactions.push(elts[serial]);
                }
            }
            return interactions;
        },
        addResponseDeclaration : function(response){
            if(Element.isA(response, 'responseDeclaration')){
                response.setRelatedItem(this);
                this.responses[response.getSerial()] = response;
            }else{
                throw 'is not a qti response declaration';
            }
            return this;
        },
        getResponseDeclaration : function(identifier){
            for(var i in this.responses){
                if(this.responses[i].attr('identifier') === identifier){
                    return this.responses[i];
                }
            }
            return null;
        },
        addOutcomeDeclaration : function(outcome){
            if(Element.isA(outcome, 'outcomeDeclaration')){
                outcome.setRelatedItem(this);
                this.outcomes[outcome.getSerial()] = outcome;
            }else{
                throw 'is not a qti outcome declaration';
            }
            return this;
        },
        addModalFeedback : function(feedback){
            if(Element.isA(feedback, 'modalFeedback')){
                feedback.setRelatedItem(this);
                this.modalFeedbacks[feedback.getSerial()] = feedback;
            }else{
                throw 'is not a qti modal feedback';
            }
            return this;
        },
        getComposingElements : function(){
            var elts = this._super(), _this = this;
            _.each(['responses', 'outcomes', 'modalFeedbacks', 'stylesheets'], function(elementCollection){
                for(var i in _this[elementCollection]){
                    var elt = _this[elementCollection][i];
                    elts[i] = elt;
                    elts = _.extend(elts, elt.getComposingElements());
                }
            });
            if(this.responseProcessing instanceof Element){
                elts[this.responseProcessing.getSerial()] = this.responseProcessing;
            }
            return elts;
        },
        find : function(serial){

            var found = this._super(serial);

            if(!found){
                found = util.findInCollection(this, ['responses', 'outcomes', 'modalFeedbacks', 'stylesheets'], serial);
            }

            return found;
        },
        getResponses : function(){
            return _.clone(this.responses);
        },
        getRelatedItem : function(){
            return this;
        },
        addNamespace : function(name, uri){
            this.namespaces[name] = uri;
        },
        setNamespaces : function(namespaces){
            this.namespaces = namespaces;
        },
        getNamespaces : function(){
            return _.clone(this.namespaces);
        },
        setSchemaLocations : function(locations){
            this.schemaLocations = locations;
        },
        getSchemaLocations : function(){
            return _.clone(this.schemaLocations);
        },
        setApipAccessibility : function(apip){
            this.apipAccessibility = apip || null;
        },
        getApipAccessibility : function(){
            return this.apipAccessibility;
        },
        addStylesheet : function(stylesheet){
            if(Element.isA(stylesheet, 'stylesheet')){
                stylesheet.setRelatedItem(this);
                this.stylesheets[stylesheet.getSerial()] = stylesheet;
            }else{
                throw 'is not a qti stylesheet declaration';
            }
            return this;
        },
        removeStyleSheet : function(stylesheet){
            delete this.stylesheets[stylesheet.getSerial()];
            return this;
        },
        stylesheetExists : function(href){
            var exists = false;
            _.each(this.stylesheets, function(stylesheet){
                if(stylesheet.attr('href') === href){
                    exists = true;
                    return false;//break each loop
                }
            });
            return exists;
        },
        setResponseProcessing : function(rp){
            if(Element.isA(rp, 'responseProcessing')){
                rp.setRelatedItem(this);
                this.responseProcessing = rp;
            }else{
                throw 'is not a response processing';
            }
            return this;
        },
        toArray : function(){
            var arr = this._super();
            var toArray = function(elt){
                return elt.toArray();
            };
            arr.namespaces = this.namespaces;
			arr.schemaLocations = this.schemaLocations;
            arr.outcomes = _.map(this.outcomes, toArray);
            arr.responses = _.map(this.responses, toArray);
            arr.stylesheets = _.map(this.stylesheets, toArray);
            arr.modalFeedbacks = _.map(this.modalFeedbacks, toArray);
            arr.responseProcessing = this.responseProcessing.toArray();
            return arr;
        },
        isEmpty : function(){

            var body = this.body().trim();

            if(body){

                //hack to fix #2652
                var $dummy = $('<div>').html(body),
                    $children = $dummy.children();

                if($children.length === 1 && $children.hasClass('empty')){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        },

        /**
         * Clean up an item rendering.
         * Ask the renderer to run destroy if exists.
         */
        clear : function(){
            var renderer = this.getRenderer();
            if(renderer){
                if(_.isFunction(renderer.destroy)){
                    renderer.destroy(this);
                }
            }
        },
    });

    Container.augment(Item);

    return Item;
});

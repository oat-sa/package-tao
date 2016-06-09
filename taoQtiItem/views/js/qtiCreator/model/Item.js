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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */
define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableContainer',
    'taoQtiItem/qtiItem/core/Item',
    'taoQtiItem/qtiCreator/model/Stylesheet',
    'taoQtiItem/qtiCreator/model/ResponseProcessing',
    'taoQtiItem/qtiCreator/model/variables/OutcomeDeclaration',
    'taoQtiItem/qtiCreator/model/feedbacks/ModalFeedback'
], function(_, editable, editableContainer, Item, Stylesheet, ResponseProcessing, OutcomeDeclaration, ModalFeedback){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableContainer);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                identifier : 'myItem_1',
                title : 'Item title',
                adaptive : false,
                timeDependent : false
            };
        },
        createResponseProcessing : function(){
            var rp = new ResponseProcessing();
            rp.processingType = 'templateDriven';
            this.setResponseProcessing(rp);
            return rp;
        },
        createStyleSheet : function(href){
            if(href && _.isString(href)){
                var stylesheet = new Stylesheet({href : href});
                stylesheet.setRenderer(this.getRenderer());
                this.addStylesheet(stylesheet);
                return stylesheet;
            }else{
                throw 'missing or invalid type for the required arg "href"';
                return null;
            }
        },
        createOutcomeDeclaration : function(attributes){

            var identifier = attributes.identifier || '';
            delete attributes.identifier;
            var outcome = new OutcomeDeclaration(attributes);

            this.addOutcomeDeclaration(outcome);
            outcome.buildIdentifier(identifier);

            return outcome;
        },
        createModalFeedback : function(attributes, response){

            var identifier = attributes.identifier || '';
            delete attributes.identifier;
            var modalFeedback = new ModalFeedback(attributes);

            this.addModalFeedback(modalFeedback);
            modalFeedback.buildIdentifier(identifier);
            modalFeedback.body('Some feedback text.');
            if(response && response.qtiClass === 'responseDeclaration'){
                modalFeedback.data('relatedResponse', response);
            }
        
            return modalFeedback;
        },
        deleteResponseDeclaration : function(response){
            var self = this;
            var serial;
            if(_.isString(response)){
                serial = response;
            }else if(response && response.qtiClass === 'responseDeclaration'){
                serial = response.getSerial();
            }
            if(this.responses[serial]){
                //remove feedback rules:
                _.each(this.responses[serial].feedbackRules, function(rule){
                    var feedbacks = [];
                    if(rule.feedbackThen && rule.feedbackThen.is('modalFeedback')){
                        feedbacks.push(rule.feedbackThen.serial);  
                    }
                    if(rule.feedbackElse && rule.feedbackElse.is('modalFeedback')){
                        feedbacks.push(rule.feedbackElse.serial);
                    }
                    self.modalFeedbacks = _.omit(self.modalFeedbacks, feedbacks);
                    
                    if(rule.feedbackOutcome && rule.feedbackOutcome.is('outcomeDeclaration')){
                        self.outcomes = _.omit(self.outcomes, rule.feedbackOutcome.serial);
                    }
                });
                this.responses = _.omit(this.responses, serial);
            }
            return this;
        }
    });
    return Item.extend(methods);
});

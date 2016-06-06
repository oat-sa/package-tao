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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 */
define(['taoQtiItem/qtiItem/core/Element', 'lodash'], function(Element, _){
    'use strict';
    
    var SimpleFeedbackRule = Element.extend({
        qtiClass : '_simpleFeedbackRule',
        serial : '',
        relatedItem : null,
        init : function(serial, feedbackOutcome, feedbackThen, feedbackElse){

            this._super(serial, {});

            this.condition = 'correct';
            this.comparedOutcome = null;
            this.comparedValue = 0.0;

            this.feedbackOutcome = feedbackOutcome;
            if(Element.isA(feedbackThen, 'feedback')){
                this.feedbackThen = feedbackThen;
            }else{
                this.feedbackThen = null;
            }
            if(Element.isA(feedbackElse, 'feedback')){
                this.feedbackElse = feedbackThen;
            }else{
                this.feedbackElse = null;
            }

        },
        setCondition : function(comparedOutcome, condition, comparedValue){
            var _comparedValues = [];
            if(Element.isA(comparedOutcome, 'variableDeclaration')){
                switch(condition){
                    case 'correct':
                    case 'incorrect':
                        if(Element.isA(comparedOutcome, 'responseDeclaration')){
                            this.comparedOutcome = comparedOutcome;
                            this.condition = condition;
                        }else{
                            throw 'invalid outcome type: must be a responseDeclaration';
                        }
                        break;
                    case 'lt':
                    case 'lte':
                    case 'equal':
                    case 'gte':
                    case 'gt':
                        if(comparedValue !== null && comparedValue !== undefined){
                            this.comparedOutcome = comparedOutcome;
                            this.condition = condition;
                            this.comparedValue = comparedValue;
                        }else{
                            throw 'compared value must not be null';
                        }
                        break;
                    case 'choices':
                        if(Element.isA(comparedOutcome, 'responseDeclaration') && comparedValue !== null && _.isArray(comparedValue)){
                            var choices = _.values(comparedOutcome.getInteraction().getChoices());
                            this.comparedOutcome = comparedOutcome;
                            this.condition = condition;
                            _.each(comparedValue, function(v){
                                if(v instanceof Element){
                                    _comparedValues.push(v);
                                }else if(_.isString(v)){
                                    _.each(choices, function(c){
                                        if(c.attr('identifier') === v){
                                            _comparedValues.push(c);
                                            return false;//break
                                        }
                                    });
                                }
                            });
                            
                            this.comparedValue = _comparedValues;
                        }else{
                            throw 'compared value must not be null';
                        }
                        break;
                    default:
                        throw 'unknown condition type : '.condition;
                }
            }else{
                throw 'invalid outcome type: must be a variableDeclaration';
            }

            return this;
        },
        setFeedbackElse : function(feedback){
            if(Element.isA(feedback, 'feedback')){
                this.feedbackElse = feedback;
            }
        },
        toArray : function(){
            var val = this.comparedValue;
            var _toString = function(v){
                if(val instanceof Element){
                    return val.attr('identifier') ;
                }else{
                    return val+'';
                }
            };
            if(_.isArray(val)){
                val = _.map(val, _toString);
            }else{
                val = _toString(val);
            }
            return {
                condition : this.condition,
                comparedOutcome : this.comparedOutcome.id(),
                comparedValue : val
            };
        }
    });

    return SimpleFeedbackRule;
});
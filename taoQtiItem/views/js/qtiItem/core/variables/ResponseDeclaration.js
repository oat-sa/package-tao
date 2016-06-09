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
define(['taoQtiItem/qtiItem/core/variables/VariableDeclaration', 'lodash'], function(VariableDeclaration, _){
    'use strict';
    
    var ResponseDeclaration = VariableDeclaration.extend({
        qtiClass : 'responseDeclaration',
        init : function(serial, attributes){

            this._super(serial, attributes);

            //MATCH_CORRECT, MAP_RESPONSE, MAP_RESPONSE_POINT
            this.template = '';//previously called 'howMatch'

            //when template equals ont of the "map" one (MAP_RESPONSE, MAP_RESPONSE_POINT)
            this.mappingAttributes = {};
            this.mapEntries = {};

            //correct response [0..*]
            this.correctResponse = null;

            //tao internal usage:
            this.feedbackRules = {};
        },
        getFeedbackRules : function(){
            return _.values(this.feedbackRules);
        },
        getComposingElements : function(){
            var elts = this._super();
            elts = _.extend(elts, this.feedbackRules);
            return elts;
        },
        toArray : function(){
            var arr = this._super();
            arr.howMatch = this.template;
            arr.correctResponses = this.correctResponse;
            arr.mapping = this.mapEntries;
            arr.mappingAttributes = this.mappingAttributes;
            arr.feedbackRules = _.map(this.feedbackRules, function(rule){
                return rule.toArray();
            });
            return arr;
        },
        getInteraction : function(){
            var interaction = null;
            var responseId = this.id();
            var item = this.getRelatedItem();
            var interactions = item.getInteractions();
            _.each(interactions, function(i){
                if(i.attributes.responseIdentifier === responseId){
                    interaction = i;
                    return false;//break
                }
            });
            return interaction;
        },
        isCardinality : function(cardinalities){
            var comparison;
            if(_.isArray(cardinalities)){
                comparison = cardinalities;
            }else if(_.isString(cardinalities)){
                cardinalities = [cardinalities];
            }else{
                return false;
            }
            return (_.indexOf(comparison, this.attr('cardinality')) >= 0);
        }
    });

    return ResponseDeclaration;
});



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

define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/AssociateInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/sizeAdapter',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager'
], function(_, __, commonRenderer, sizeAdapter, instructionMgr){

    'use strict';

    var ResponseWidget = {
        create : function(widget, responseMappingMode){

            var interaction = widget.element;

            commonRenderer.resetResponse(interaction);
            commonRenderer.destroy(interaction);

            if(responseMappingMode){
                instructionMgr.appendInstruction(widget.element, __('Please define association pairs and their scores below.'));
                interaction.responseMappingMode = true;
            }else{
                instructionMgr.appendInstruction(widget.element, __('Please define the correct association pairs below.'));
            }

            commonRenderer.render(interaction);
            
            sizeAdapter.adaptSize(widget);
        },
        setResponse : function(interaction, response){
            var responseDeclaration = interaction.getResponseDeclaration();
            commonRenderer.setResponse(interaction, ResponseWidget.formatResponse(response, responseDeclaration.attr('cardinality')));
            
            sizeAdapter.adaptSize(interaction.data('widget'));
            
        },
        destroy : function(widget){

            var interaction = widget.element;

            commonRenderer.resetResponse(interaction);
            commonRenderer.destroy(interaction);

            delete interaction.responseMappingMode;

            commonRenderer.renderEmptyPairs(interaction);
            
            sizeAdapter.adaptSize(widget);
        },
        getResponseSummary : function(responseDeclaration){
            
            var pairs = [],
                correctResponse = _.values(responseDeclaration.getCorrect()),
                mapEntries = responseDeclaration.getMapEntries();
            
            _.each(correctResponse, function(pair) {

                var sortedIdPair = pair.split(' ').sort(),
                    sortedIdPairKey = sortedIdPair.join(' ');

                pairs[sortedIdPairKey] = {
                    pair: sortedIdPair,
                    correct: true,
                    score: undefined
                };
            });

            _.forIn(mapEntries, function(score, pair) {

                var sortedIdPair = pair.split(' ').sort(),
                    sortedIdPairKey = sortedIdPair.join(' ');

                if (!pairs[sortedIdPairKey]) {
                    pairs[sortedIdPairKey] = {
                        pair: sortedIdPair,
                        correct: false,
                        score: score
                    };
                } else {
                    pairs[sortedIdPairKey].score = score;
                }
            });
            
            return pairs;
        },
        formatResponse : function(response, cardinality){

            var formatedRes;
            if(cardinality === 'single'){
                formatedRes = {base : { pair : [] }};
            } else {
                formatedRes = {list : { pair : [] }};
            }
            
            _.each(response, function(pairString){
                var pair = pairString.split(' ');
                if(cardinality === 'single'){
                    formatedRes.base.pair = pair;
                } else {
                    formatedRes.list.pair.push(pair);
                }
            });

            return formatedRes;
        },
        unformatResponse : function(formatedResponse){

            var res = [];

            if(formatedResponse.list && formatedResponse.list.pair){
                _.each(formatedResponse.list.pair, function(pair){
                    res.push(pair.join(' '));
                });
            }else if(formatedResponse.base && formatedResponse.base.pair){
                res.push(formatedResponse.base.pair.join(' '));
            }
            return res;
        }
    };

    return ResponseWidget;
});

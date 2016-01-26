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


/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicAssociateInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse', 
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/pairScoringForm'
], function($, _, __, stateFactory, Map, commonRenderer, instructionMgr, graphicHelper, PciResponse, scoringFormFactory){

    'use strict';

    /**
     * Initialize the state.
     */
    function initMapState(){
        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var corrects  = _.values(response.getCorrect());
        var currentResponses =  _.size(response.getMapEntries()) === 0 ? corrects : _.keys(response.getMapEntries());

        //really need to destroy before ? 
        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction);
        
        if(!interaction.paper){
            return;
        }

        //add a specific instruction
        instructionMgr.appendInstruction(interaction, __('Create assocations and fill the score in the form below'));
        interaction.responseMappingMode = true;

        //use the common Renderer
        commonRenderer.render.call(interaction.getRenderer(), interaction);    

		//display the choices ids
        showChoicesId(interaction);

        //and initialize the scoring form
        if(_.size(response.getMapEntries()) === 0){
            updateForm(widget, corrects);
        } else {
            updateForm(widget);
        }
        
        //each response change leads to an update of the scoring form
        widget.$container.on('responseChange.qti-widget', function(e, data){
            var type  = response.attr('cardinality') === 'single' ? 'base' : 'list';
            var pairs, entries;
            if(data && data.response &&  data.response[type]){
               pairs = _.invoke(data.response[type].pair, Array.prototype.join, ' ');
               entries = _.keys(response.getMapEntries());
                
               //add new pairs from  the difference between the current entries and the given data
               _(pairs).difference(entries).forEach(interaction.pairScoringForm.addPair, interaction.pairScoringForm);

                
               removePaths(interaction);
            }
        });
    }

    function removePaths(interaction){
        _.delay(function(){
           _.forEach(interaction._vsets, function(vset){
                _.invoke(vset, 'remove');
            });
            interaction._vsets = [];
        }, 500);
    }

    /**
     * Exit the map state
     */
    function exitMapState(){
        var widget = this.widget;
        var interaction = widget.element;
        
        if(!interaction.paper){
            return;
        }
        
        widget.$container.off('responseChange.qti-widget');

        if(interaction.pairScoringForm){
            interaction.pairScoringForm.destroy();
        }

        //destroy the common renderer
        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction);
        instructionMgr.removeInstructions(interaction);

        //initialize again the widget's paper
        interaction.paper = widget.createPaper();
        widget.createChoices();
    }

    function showChoicesId(interaction){
        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(element){
                graphicHelper.createShapeText(interaction.paper, element, {
                    shapeClick: true,
                    content : choice.id()
                }).transform('t0,-10').toFront();
            }
        });
    }

	/**
     * Update the scoring form
     * @param {Object} widget - the current widget
     * @param {Array} [entries] - to force the use of this collection instead of the mapEntries
     */
    function updateForm(widget, entries){

        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var mapEntries = response.getMapEntries();

        var mappingChange = function mappingChange(){
            //set the current responses, either the mapEntries or the corrects if nothing else
            commonRenderer.setResponse(
                interaction, 
                PciResponse.serialize(_.invoke(_.keys(response.getMapEntries()), String.prototype.split, ' '), interaction)
            );
        };

        var getPairValues = function getPairValues(){
            return _.map(interaction.getChoices(), function(choice){
                return {
                    id : choice.id(),
                    value : choice.id()
                };
            });
        };

        //set up the scoring form options
        var options = {
            leftTitle : __('left'),
            rightTitle : __('right'),
            type : 'pair',
            pairLeft : getPairValues,
            pairRight : getPairValues
        };

        //format the entries to match the needs of the scoring form
        if(entries){
            options.entries = _.transform(entries, function(result, value){
                result[value] = mapEntries[value] !== undefined ? mapEntries[value] : response.mappingAttributes.defaultValue;
            }, {}); 
        }

        //initialize the scoring form 
        interaction.pairScoringForm = scoringFormFactory(widget, options);
    }

    /**
     * The map answer state for the graphicAssociate interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Map
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicAssociateInteraction/states/Map
     */
    return  stateFactory.create(Map, initMapState, exitMapState);
});

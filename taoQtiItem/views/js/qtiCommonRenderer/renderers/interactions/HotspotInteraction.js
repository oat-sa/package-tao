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
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'core/promise',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/hotspotInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager'
], function($, _, __, Promise, tpl, graphic,  pciResponse, containerHelper, instructionMgr){
    'use strict';

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     *
     * @param {object} interaction
     */
    var render = function render(interaction){
        var self = this;

        return new Promise(function(resolve, reject){
            var $container = containerHelper.get(interaction);
            var background = interaction.object.attributes;

            $container
                .off('resized.qti-widget.resolve')
                .one('resized.qti-widget.resolve', resolve);

            interaction.paper = graphic.responsivePaper( 'graphic-paper-' + interaction.serial, interaction.serial, {
                width     : background.width,
                height    : background.height,
                img       : self.resolveUrl(background.data),
                container : $container
            });

            //call render choice for each interaction's choices
            _.forEach(interaction.getChoices(), _.partial(_renderChoice, interaction));

            //set up the constraints instructions
            instructionMgr.minMaxChoiceInstructions(interaction, {
                min: interaction.attr('minChoices'),
                max: interaction.attr('maxChoices'),
                getResponse : _getRawResponse,
                onError : function(data){
                    if(data.target.active){
                        data.target.active = false;
                        graphic.updateElementState(this, 'basic', __('Select this area'));
                        graphic.highlightError(data.target);
                        containerHelper.triggerResponseChangeEvent(interaction);
                        $container.trigger('inactiveChoice.qti-widget', [data.choice, data.target]);
                    }
                }
            });
        });
    };

    /**
     * Render a choice inside the paper.
     * Please note that the choice renderer isn't implemented separately because it relies on the Raphael paper instead of the DOM.
     * @param {Paper} paper - the raphael paper to add the choices to
     * @param {Object} interaction
     * @param {Object} choice - the hotspot choice to add to the interaction
     */
    var _renderChoice  =  function _renderChoice(interaction, choice){
        var $container = containerHelper.get(interaction);
        var rElement = graphic.createElement(interaction.paper, choice.attr('shape'), choice.attr('coords'), {
            id : choice.serial,
            title : __('Select this area')
        })
        .click(function(){
            if(this.active){
                graphic.updateElementState(this, 'basic', __('Select this area'));
                this.active = false;
                $container.trigger('inactiveChoice.qti-widget', [choice, this]);
            } else {
                graphic.updateElementState(this, 'active', __('Click again to remove'));
                this.active = true;
                $container.trigger('activeChoice.qti-widget', [choice, this]);
            }
            containerHelper.triggerResponseChangeEvent(interaction);
            instructionMgr.validateInstructions(interaction, { choice : choice, target : this });
        });
    };

    /**
     * Get the response from the interaction
     * @private
     * @param {Object} interaction
     * @returns {Array} the response in raw format
     */
    var _getRawResponse = function _getRawResponse(interaction){

       return _(interaction.getChoices())
        .map(function(choice){
            var rElement = interaction.paper.getById(choice.serial);
            return (rElement && rElement.active === true) ? choice.id() : false;
       })
        .filter(_.isString)
        .value();
    };

    /**
     * Set the response to the rendered interaction.
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     *
     * Special value: the empty object value {} resets the interaction responses
     *
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        var responseValues;
        if(response && interaction.paper){

            try{
                responseValues = pciResponse.unserialize(response, interaction);

            } catch(e){ }

            if(_.isArray(responseValues)){

                _.forEach(interaction.getChoices(), function(choice){
                    var rElement;
                    if(_.contains(responseValues, choice.attributes.identifier)){
                        rElement = interaction.paper.getById(choice.serial);
                        if(rElement){
                            rElement.active = true;
                            graphic.updateElementState(rElement, 'active', __('Click again to remove'));
                            instructionMgr.validateInstructions(interaction, { choice : choice, target : rElement });
                        }
                    }
                });
            }
        }
    };

    /**
     * Reset the current responses of the rendered interaction.
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     *
     * Special value: the empty object value {} resets the interaction responses
     *
     * @param {object} interaction
     * @param {object} response
     */
    var resetResponse = function resetResponse(interaction){
        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(element){
                element.active = false;
                graphic.updateElementState(element, 'basic');
            }
        });
        instructionMgr.resetInstructions(interaction);
    };


    /**
     * Return the response of the rendered interaction
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     *
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        var raw = _getRawResponse(interaction);
        var response =  pciResponse.serialize(_getRawResponse(interaction), interaction);
        return response;
    };

    /**
     * Clean interaction destroy
     * @param {Object} interaction
     */
    var destroy = function destroy(interaction){
        var $container;
        if(interaction.paper){
            $container = containerHelper.get(interaction);

            $(window).off('resize.qti-widget.' + interaction.serial);
            $container.off('resize.qti-widget.' + interaction.serial);

            interaction.paper.clear();
            instructionMgr.removeInstructions(interaction);

            $('.main-image-box', $container).empty().removeAttr('style');
            $('.image-editor', $container).removeAttr('style');
        }

        //remove all references to a cache container
        containerHelper.reset(interaction);
    };
    /**
     * Set the interaction state. It could be done anytime with any state.
     *
     * @param {Object} interaction - the interaction instance
     * @param {Object} state - the interaction state
     */
    var setState  = function setState(interaction, state){
        if(_.isObject(state)){
            if(state.response){
                interaction.resetResponse();
                interaction.setResponse(state.response);
            }
        }
    };

    /**
     * Get the interaction state.
     *
     * @param {Object} interaction - the interaction instance
     * @returns {Object} the interaction current state
     */
    var getState = function getState(interaction){
        var $container;
        var state =  {};
        var response =  interaction.getResponse();

        if(response){
            state.response = response;
        }
        return state;
    };

    /**
     * Expose the common renderer for the hotspot interaction
     * @exports qtiCommonRenderer/renderers/interactions/HotspotInteraction
     */
    return {
        qtiClass : 'hotspotInteraction',
        template : tpl,
        render : render,
        getContainer : containerHelper.get,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        destroy : destroy,
        setState : setState,
        getState : getState
    };
});

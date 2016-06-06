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
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/textEntryInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'util/locale',
    'polyfill/placeholders',
    'tooltipster'
], function($, _, __, tpl, containerHelper, instructionMgr, pciResponse, locale){
    'use strict';

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10333
     *
     * @param {object} interaction
     */
    var render = function(interaction){
        var attributes = interaction.getAttributes(),
            $el = interaction.getContainer();



        //setting up the width of the input field
        if(attributes.expectedLength){
            $el.css('width', parseInt(attributes.expectedLength) + 'ch');
        }

        //checking if there's a pattern mask for the input
        if(attributes.patternMask){
            //set up the tooltip plugin for the input
            $el.tooltipster({
                theme: 'tao-error-tooltip',
                content: __('This is not a valid answer'),
                delay: 350,
                trigger: 'custom'
            });
        }

        //checking if there's a placeholder for the input
        if(attributes.placeholderText){
            $el.attr('placeholder', attributes.placeholderText);
        }

        $el.on('keyup.commonRenderer', _.debounce(function(){
            
            var regex;
            if(attributes.patternMask){
                regex = new RegExp('^' + attributes.patternMask + '$');
                 if(regex.test($el.val())){
                    $el.tooltipster('hide').removeClass('invalid');
                } else {
                    
                    $el.tooltipster('show').addClass('invalid');//adding the class invalid prevent the invalid response to be submitted
                }
            }
            containerHelper.triggerResponseChangeEvent(interaction);
            
        }, 600)).on('keydown.commonRenderer', function(){
            //hide the error message while the test taker is inputing an error (let's be indulgent, she is trying to fix her error)
            if(attributes.patternMask){
                $el.tooltipster('hide');
            }
        });
    };

    var resetResponse = function(interaction){
        interaction.getContainer().val('');
    };

    /**
     * Set the response to the rendered interaction.
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10333
     *
     * Special value: the empty object value {} resets the interaction responses
     *
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        var responseValue;

        try{
            responseValue = pciResponse.unserialize(response, interaction);
        }catch(e){
        }

        if(responseValue && responseValue.length){
            interaction.getContainer().val(responseValue[0]);
        }
    };

    /**
     * Return the response of the rendered interaction
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10333
     *
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        var ret = {'base' : {}},
        value,
            $el = interaction.getContainer(),
            attributes = interaction.getAttributes(),
            baseType = interaction.getResponseDeclaration().attr('baseType'),
            numericBase = attributes.base || 10;
        
        if($el.hasClass('invalid') || (attributes.placeholderText && $el.val() === attributes.placeholderText)){
            //invalid response or response equals to the placeholder text are considered empty
            value = '';
        }else{
            if (baseType === 'integer') {
                value = locale.parseInt($el.val(), numericBase);
            } else if (baseType === 'float') {
                value = locale.parseFloat($el.val());
            } else if (baseType === 'string') {
                value = $el.val();
            }
        }

        ret.base[baseType] = isNaN(value) && typeof value === 'number' ? '' : value;

        return ret;
    };

    var destroy = function(interaction){

        //remove event
        $(document).off('.commonRenderer');
        containerHelper.get(interaction).off('.commonRenderer');

        //remove instructions
        instructionMgr.removeInstructions(interaction);

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


    return {
        qtiClass : 'textEntryInteraction',
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

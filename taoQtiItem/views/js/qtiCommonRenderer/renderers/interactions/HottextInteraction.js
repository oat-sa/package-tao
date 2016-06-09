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
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/hottextInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function($, _, __, tpl, containerHelper, instructionMgr, pciResponse){
    'use strict';

    /**
     * 'pseudo-label' is technically a div that behaves like a label.
     * This allows the usage of block elements inside the fake label
     */
    var pseudoLabel = function(interaction){

        var $container = containerHelper.get(interaction);

        var setChoice = function($choice, interaction){
            var $inupt = $choice.find('input');

            if($inupt.prop('checked') || $inupt.hasClass('disabled')){
                $inupt.prop('checked', false);
            }else{
                var maxChoices = parseInt(interaction.attr('maxChoices'));
                var currentChoices = _.values(_getRawResponse(interaction)).length;

                if(currentChoices < maxChoices || maxChoices === 0){
                    $inupt.prop('checked', true);
                }
            }
            containerHelper.triggerResponseChangeEvent(interaction);
            instructionMgr.validateInstructions(interaction, {choice : $choice});
        };

        $('.hottext', $container).on('click', function(e){
            e.preventDefault();
            setChoice($(this), interaction);
        });
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10278
     *
     * @param {object} interaction
     */
    var render = function(interaction){
        pseudoLabel(interaction);

        //set up the constraints instructions
        instructionMgr.minMaxChoiceInstructions(interaction, {
            min: interaction.attr('minChoices'),
            max: interaction.attr('maxChoices'),
            getResponse : _getRawResponse,
            onError : function(data){
                var $input, $choice, $icon;
                if(data.choice && data.choice.length){
                    $choice = data.choice.addClass('error');
                    $input  = $choice.find('input');
                    $icon   = $choice.find(' > label > span').addClass('error cross');


                    setTimeout(function(){
                        $input.prop('checked', false);
                        $choice.removeClass('error');
                        $icon.removeClass('error cross');
                    }, 350);
                }
            }
        });
    };

    var resetResponse = function(interaction){
        var $container = containerHelper.get(interaction);
        $('input', $container).prop('checked', false);
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
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        var $container = containerHelper.get(interaction);

        try{
            _.each(pciResponse.unserialize(response, interaction), function(identifier){
                $container.find('input[value=' + identifier + ']').prop('checked', true);
            });
            instructionMgr.validateInstructions(interaction);
        }catch(e){
            throw new Error('wrong response format in argument : ' + e);
        }
    };

    var _getRawResponse = function(interaction){
        var values = [];
        var $container = containerHelper.get(interaction);
        $('input:checked', $container).each(function(){
            values.push($(this).val());
        });
        return values;
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
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    /**
     * Clean interaction destroy
     * @param {Object} interaction
     */
    var destroy = function destroy(interaction){
        var $container = containerHelper.get(interaction);

        //restore selected choices:
        $container.find('.hottext').off('click');

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
        qtiClass : 'hottextInteraction',
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

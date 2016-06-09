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
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/sliderInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'nouislider'
], function($, _, __,tpl, containerHelper, instructionMgr, pciResponse){
    'use strict';

    var _slideTo = function(options){
        options.sliderCurrentValue.find('.qti-slider-cur-value').text(options.value);
        options.sliderValue.val(options.value);
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10333
     *
     * @param {object} interaction
     */
    var render = function(interaction){

        var attributes = interaction.getAttributes(),
            $container = interaction.getContainer(),
            $el = $('<div />').attr({'id' : attributes.responseIdentifier + '-qti-slider', 'class' : 'qti-slider'}), //slider element
        $sliderLabels = $('<div />').attr({'class' : 'qti-slider-values'}),
        $sliderCurrentValue = $('<div />').attr({'id' : attributes.responseIdentifier + '-qti-slider-cur-value', 'class' : 'qti-slider-cur-value'}), //show the current selected value
        $sliderValue = $('<input />').attr({'type' : 'hidden', 'id' : attributes.responseIdentifier + '-qti-slider-value'}); //the input that always holds the slider value

        //getting the options
        var orientation = 'horizontal',
            reverse = typeof attributes.reverse !== 'undefined' && attributes.reverse ? true : false, //setting the slider whether to be reverse or not
            min = parseInt(attributes.lowerBound),
            max = parseInt(attributes.upperBound),
            step = typeof attributes.step !== 'undefined' && attributes.step ? parseInt(attributes.step) : 1, //default value as per QTI standard
            steps = (max - min) / step; //number of the steps

        //add the containers
        $sliderCurrentValue.append('<span class="qti-slider-cur-value-text">'+__('Current value:')+' </span>')
            .append('<span class="qti-slider-cur-value"></span>');

        $sliderLabels.append('<span class="slider-min">' + (!reverse ? min : max) + '</span>')
            .append('<span class="slider-max">' + (!reverse ? max : min) + '</span>');

        interaction.getContainer().append($el)
            .append($sliderLabels)
            .append($sliderCurrentValue)
            .append($sliderValue);

        //setting the orientation of the slider
        if(typeof attributes.orientation !== 'undefined' && $.inArray(attributes.orientation, ['horizontal', 'vertical']) > -1){
            orientation = attributes.orientation;
        }

        var sliderSize = 0;

        if(orientation === 'horizontal'){
            $container.addClass('qti-slider-horizontal');
        }else{
            var maxHeight = 300;
            sliderSize = steps * 20;
            if(sliderSize > maxHeight){
                sliderSize = maxHeight;
            }
            $container.addClass('qti-slider-vertical');
            $el.height(sliderSize + 'px');
            $sliderLabels.height(sliderSize + 'px');
        }

        //set the middle value if the stepLabel attribute is enabled
        if(typeof attributes.stepLabel !== 'undefined' && attributes.stepLabel){
            var middleStep = parseInt(steps / 2),
                leftOffset = (100 / steps) * middleStep,
                middleValue = reverse ? max - (middleStep * step) : min + (middleStep * step);

            if(orientation === 'horizontal'){
                $sliderLabels.find('.slider-min').after('<span class="slider-middle" style="left:' + leftOffset + '%">' + middleValue + '</span>');
            }else{
                $sliderLabels.find('.slider-min').after('<span class="slider-middle" style="top:' + leftOffset + '%">' + middleValue + '</span>');
            }

        }

        //create the slider
        $el.noUiSlider({
            start : reverse ? max : min,
            range: {
                'min': min,
                'max': max
            },
            step : step,
            orientation : orientation
        }).on('slide', function(e){
            var val = parseInt($(this).val());
            if((interaction.attr('reverse'))){
                val = (max + min) - val;
            }
            val = Math.round(val * 1000) / 1000;
            _slideTo({
                'value' : val,
                'sliderValue' : $sliderValue,
                'sliderCurrentValue' : $sliderCurrentValue
            });

            containerHelper.triggerResponseChangeEvent(interaction);
        });

        _slideTo({
            'value' : min,
            'sliderValue' : $sliderValue,
            'sliderCurrentValue' : $sliderCurrentValue
        });
    };

    var resetResponse = function(interaction){
        var attributes = interaction.getAttributes(),
            $el = $('#' + attributes.responseIdentifier + '-qti-slider'),
            $sliderValue = $('#' + attributes.responseIdentifier + '-qti-slider-value'),
            $sliderCurrentValue = $('#' + attributes.responseIdentifier + '-qti-slider-cur-value'),
            min = parseInt(attributes.lowerBound),
            max = parseInt(attributes.upperBound),
            reverse = typeof attributes.reverse !== 'undefined' && attributes.reverse ? true : false,
            startValue = reverse ? max : min;

        _slideTo({
            'value' : min,
            'sliderValue' : $sliderValue,
            'sliderCurrentValue' : $sliderCurrentValue
        });

        $el.val(startValue);
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
        var attributes = interaction.getAttributes(),
            $sliderValue = $('#' + attributes.responseIdentifier + '-qti-slider-value'),
            $sliderCurrentValue = $('#' + attributes.responseIdentifier + '-qti-slider-cur-value'),
            $el = $('#' + attributes.responseIdentifier + '-qti-slider'),
            min = parseInt(attributes.lowerBound),
            max = parseInt(attributes.upperBound),
            value;

        value = pciResponse.unserialize(response, interaction)[0];

        _slideTo({
            'value' : value,
            'sliderValue' : $sliderValue,
            'sliderCurrentValue' : $sliderCurrentValue
        });

        $el.val(interaction.attr('reverse') ? (max + min) - value : value);
    };

    var _getRawResponse = function(interaction){

        var value,
            attributes = interaction.getAttributes(),
            baseType = interaction.getResponseDeclaration().attr('baseType'),
            min = parseInt(attributes.lowerBound),
            $sliderValue = $('#' + attributes.responseIdentifier + '-qti-slider-value');
        
        if(baseType === 'integer'){
            value = parseInt($sliderValue.val());
        }else if(baseType === 'float'){
            value = parseFloat($sliderValue.val());
        }

        return isNaN(value) ? min : value;
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
        return pciResponse.serialize([_getRawResponse(interaction)], interaction);
    };

    var destroy = function(interaction) {
        var $container = interaction.getContainer();

        $container.empty();

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
        qtiClass : 'sliderInteraction',
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

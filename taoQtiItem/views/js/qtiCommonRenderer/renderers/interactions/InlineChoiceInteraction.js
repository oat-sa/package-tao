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
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/inlineChoiceInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'select2',
    'tooltipster'
], function($, _, __, tpl, containerHelper, instructionMgr, pciResponse){
    'use strict';

    /**
     * The value of the "empty" option
     * @type String
     */
    var _emptyValue = 'empty';

    var _defaultOptions = {
        allowEmpty:true,
        placeholderText:__('select a choice')
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     *
     * @param {object} interaction
     */
    var render = function(interaction, options){

        var opts = _.clone(_defaultOptions),
            required = !!interaction.attr('required');
        _.extend(opts, options);

        var $container = containerHelper.get(interaction);

        if(opts.allowEmpty && !required){
            $container.find('option[value=' + _emptyValue + ']').text('--- ' + __('leave empty') + ' ---');
        }else{
            $container.find('option[value=' + _emptyValue + ']').remove();
        }

        $container.select2({
            width : 'element',
            placeholder : opts.placeholderText,
            minimumResultsForSearch : -1,
            dropdownCssClass  : 'qti-inlineChoiceInteraction-dropdown'
        });

        var $el = $container.select2('container');

        _setInstructions(interaction);

        $container.on('change', function(){

            if(required && $container.val() !== "") {
                $el.tooltipster('hide');
            }

            containerHelper.triggerResponseChangeEvent(interaction);

        }).on('select2-open', function(){
            if(required){
                $el.tooltipster('hide');
            }
        }).on('select2-close', function(){

            if(required && $container.val() === "") {
                $el.tooltipster('show');
            }
        });
    };

    var _setInstructions = function(interaction){

        var required = !!interaction.attr('required'),
            $container = interaction.getContainer(),
            $el = $container.select2('container');

        if(required){
            //set up the tooltip plugin for the input
            $el.tooltipster({
                theme: 'tao-warning-tooltip',
                content: __('A choice must be selected'),
                delay: 250,
                trigger: 'custom'
            });

            if($container.val() === "") {
                $el.tooltipster('show');
            }
        }

    };

    var resetResponse = function(interaction){
        _setVal(interaction, _emptyValue);
    };

    var _setVal = function(interaction, choiceIdentifier){

        containerHelper.get(interaction)
            .val(choiceIdentifier)
            .select2('val', choiceIdentifier);
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
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

         _setVal(interaction, pciResponse.unserialize(response, interaction)[0]);
    };

    var _getRawResponse = function(interaction){
        var value = containerHelper.get(interaction).val();
        return (value && value !== _emptyValue) ? [value] : [];
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
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

     /**
     * Clean interaction destroy
     * @param {Object} interaction
     */
    var destroy = function(interaction){

        var $container = containerHelper.get(interaction);

        //remove event
        $(document).off('.commonRenderer');

        $container.select2('destroy');

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
        var $container;

        if(_.isObject(state)){
            if(state.response){
                interaction.resetResponse();
                interaction.setResponse(state.response);
            }

            //restore order of previously shuffled choices
            if(_.isArray(state.order) && state.order.length === _.size(interaction.getChoices())){

                $container = containerHelper.get(interaction);

                //just in case the dropdown is opened
                $container.select2('disable')
                          .select2('close');

                $('option[data-identifier]', $container)
                    .sort(function(a, b){
                        var aIndex = _.indexOf(state.order, $(a).data('identifier'));
                        var bIndex = _.indexOf(state.order, $(b).data('identifier'));
                        if(aIndex > bIndex) {
                            return 1;
                        }
                        if(aIndex < bIndex) {
                            return -1;
                        }
                        return 0;
                    })
                    .detach()
                    .appendTo($container);

                $container.select2('enable');
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

        //we store also the choice order if shuffled
        if(interaction.attr('shuffle') === true){
            $container = containerHelper.get(interaction);

            state.order = [];
            $('option[data-identifier]', $container).each(function(){
               state.order.push($(this).data('identifier'));
            });
        }
        return state;
    };

    /**
     * Expose the common renderer for the inline choice interaction
     * @exports qtiCommonRenderer/renderers/interactions/InlineChoiceInteraction
     */
    return {
        qtiClass : 'inlineChoiceInteraction',
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

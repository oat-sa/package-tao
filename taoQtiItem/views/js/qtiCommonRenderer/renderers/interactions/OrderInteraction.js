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
    'lodash',
    'jquery',
    'i18n',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/orderInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function(_, $, __, tpl, containerHelper, instructionMgr, pciResponse){
    'use strict';

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10283
     *
     * @param {Object} interaction - the interaction instance
     */
    var render = function(interaction){

        var $container = containerHelper.get(interaction),
            $choiceArea = $container.find('.choice-area'),
            $resultArea = $container.find('.result-area'),
            $iconAdd = $container.find('.icon-add-to-selection'),
            $iconRemove = $container.find('.icon-remove-from-selection'),
            $iconBefore = $container.find('.icon-move-before'),
            $iconAfter = $container.find('.icon-move-after'),
            $activeChoice = null;

        var _activeControls = function(){
            $iconAdd.addClass('inactive');
            $iconRemove.removeClass('inactive').addClass('active');
            $iconBefore.removeClass('inactive').addClass('active');
            $iconAfter.removeClass('inactive').addClass('active');
        };

        var _resetControls = function(){
            $iconAdd.removeClass('inactive');
            $iconRemove.removeClass('active').addClass('inactive');
            $iconBefore.removeClass('active').addClass('inactive');
            $iconAfter.removeClass('active').addClass('inactive');
        };

        var _setSelection = function($choice){
            if($activeChoice){
                $activeChoice.removeClass('active');
            }
            $activeChoice = $choice;
            $activeChoice.addClass('active');
            _activeControls();
        };

        var _resetSelection = function(){
            if($activeChoice){
                $activeChoice.removeClass('active');
                $activeChoice = null;
            }
            _resetControls();
        };

        $container.on('mousedown.commonRenderer', function(e){
            _resetSelection();
        });

        $choiceArea.on('mousedown.commonRenderer', '>li:not(.deactivated)', function(e){

            e.stopPropagation();

            _resetSelection();

            $iconAdd.addClass('triggered');
            setTimeout(function(){
                $iconAdd.removeClass('triggered');
            }, 150);

            //move choice to the result list:
            $resultArea.append($(this));
            containerHelper.triggerResponseChangeEvent(interaction);

            //update constraints :
            instructionMgr.validateInstructions(interaction);
        });

        $resultArea.on('mousedown.commonRenderer', '>li', function(e){

            e.stopPropagation();

            var $choice = $(this);
            if($choice.hasClass('active')){
                _resetSelection();
            }else{
                _setSelection($(this));
            }
        });

        $iconRemove.on('mousedown.commonRenderer', function(e){

            e.stopPropagation();

            if($activeChoice){

                //restore choice back to choice list
                $choiceArea.append($activeChoice);
                containerHelper.triggerResponseChangeEvent(interaction);

                //update constraints :
                instructionMgr.validateInstructions(interaction);
            }

            _resetSelection();
        });

        $iconBefore.on('mousedown.commonRenderer', function(e){

            e.stopPropagation();

            var $prev = $activeChoice.prev();
            if($prev.length){
                $prev.before($activeChoice);
                containerHelper.triggerResponseChangeEvent(interaction);
            }
        });

        $iconAfter.on('mousedown.commonRenderer', function(e){

            e.stopPropagation();

            var $next = $activeChoice.next();
            if($next.length){
                $next.after($activeChoice);
                containerHelper.triggerResponseChangeEvent(interaction);
            }
        });

        _setInstructions(interaction);

        //bind event listener in case the attributes change dynamically on runtime
        $(document).on('attributeChange.qti-widget.commonRenderer', function(e, data){
            if(data.element.getSerial() === interaction.getSerial()){
                if(data.key === 'maxChoices' || data.key === 'minChoices'){
                    instructionMgr.removeInstructions(interaction);
                    _setInstructions(interaction);
                    instructionMgr.validateInstructions(interaction);
                }
            }
        });

        _freezeSize($container);
    };

    var _freezeSize = function($container){
        var $orderArea = $container.find('.order-interaction-area');
        $orderArea.height($orderArea.height());
    };

    var _setInstructions = function(interaction){

        var $container = containerHelper.get(interaction);
        var $choiceArea = $('.choice-area', $container),
            $resultArea = $('.result-area', $container),
            min = parseInt(interaction.attr('minChoices')),
            max = parseInt(interaction.attr('maxChoices'));

        if(min){
            instructionMgr.appendInstruction(interaction, __('You must use at least %d choices', min), function(){
                if($resultArea.find('>li').length >= min){
                    this.setLevel('success');
                }else{
                    this.reset();
                }
            });
        }

        if(max && max < _.size(interaction.getChoices())){
            var instructionMax = instructionMgr.appendInstruction(interaction, __('You can use maximum %d choices', max), function(){
                if($resultArea.find('>li').length >= max){
                    $choiceArea.find('>li').addClass('deactivated');
                    this.setMessage(__('Maximum choices reached'));
                }else{
                    $choiceArea.find('>li').removeClass('deactivated');
                    this.reset();
                }
            });

            $choiceArea.on('mousedown.commonRenderer', '>li.deactivated', function(){
                var $choice = $(this);
                $choice.addClass('brd-error');
                instructionMax.setLevel('warning', 2000);
                setTimeout(function(){
                    $choice.removeClass('brd-error');
                }, 150);
            });
        }
    };

    var resetResponse = function(interaction){

        var $container = containerHelper.get(interaction);
        var initialOrder = _.keys(interaction.getChoices());
        var $choiceArea = $('.choice-area', $container).append($('.result-area>li', $container));
        var $choices = $choiceArea.children('.qti-choice');

        $container.find('.qti-choice.active').mousedown();

        $choices.detach().sort(function(choice1, choice2){
            return (_.indexOf(initialOrder, $(choice1).data('serial')) > _.indexOf(initialOrder, $(choice2).data('serial')));
        });
        $choiceArea.prepend($choices);
    };

    /**
     * Set the response to the rendered interaction.
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10283
     *
     * Special value: the empty object value {} resets the interaction responses
     *
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        var $container = containerHelper.get(interaction);
        var $choiceArea = $('.choice-area', $container);
        var $resultArea = $('.result-area', $container);

        if(response === null || _.isEmpty(response)){
            resetResponse(interaction);
        }else{
            try{
                _.each(pciResponse.unserialize(response, interaction), function(identifier){
                    $resultArea.append($choiceArea.find('[data-identifier=' + identifier + ']'));
                });
            }catch(e){
                throw new Error('wrong response format in argument : ' + e);
            }
        }

        instructionMgr.validateInstructions(interaction);
    };

    var _getRawResponse = function(interaction){
        var $container = containerHelper.get(interaction);
        var response = [];
        $('.result-area>li', $container).each(function(){
            response.push($(this).data('identifier'));
        });
        return response;
    };

    /**
     * Return the response of the rendered interaction
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10283
     *
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    /**
     * Set additionnal data to the template (data that are not really part of the model).
     * @param {Object} interaction - the interaction
     * @param {Object} [data] - interaction custom data
     * @returns {Object} custom data
     */
    var getCustomData = function(interaction, data){

        return _.merge(data || {}, {
            horizontal : (interaction.attr('orientation') === 'horizontal')
        });

    };

    /**
     * Destroy the interaction by leaving the DOM exactly in the same state it was before loading the interaction.
     * @param {Object} interaction - the interaction
     */
    var destroy = function(interaction){

        var $container = containerHelper.get(interaction);

        //first, remove all events
        var selectors = [
            '.choice-area',
            '.result-area',
            '.icon-add-to-selection',
            '.icon-remove-from-selection',
            '.icon-move-before',
            '.icon-move-after'
        ];
        $container.find(selectors.join(',')).andSelf().off('.commonRenderer');

        $(document).off('.commonRenderer');

        $container.find('.order-interaction-area').removeAttr('style');

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

                $('.choice-area .qti-choice', $container)
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
                    .appendTo($('.choice-area', $container));
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
            $('.choice-area .qti-choice', $container).each(function(){
               state.order.push($(this).data('identifier'));
            });
        }
        return state;
    };

     /**
     * Expose the common renderer for the order interaction
     * @exports qtiCommonRenderer/renderers/interactions/OrderInteraction
     */
    return {
        qtiClass : 'orderInteraction',
        getData : getCustomData,
        template : tpl,
        render : render,
        getContainer : containerHelper.get,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse: resetResponse,
        destroy : destroy,
        setState : setState,
        getState : getState
    };
});

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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
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
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/choiceInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'taoQtiItem/qtiCommonRenderer/helpers/sizeAdapter'
], function (_, $, __, tpl, containerHelper, instructionMgr, pciResponse, sizeAdapter) {

    'use strict';

    var KEY_CODE_SPACE = 32;
    var KEY_CODE_ENTER = 13;
    var KEY_CODE_UP    = 38;
    var KEY_CODE_DOWN  = 40;
    var KEY_CODE_TAB   = 9;

    /**
     * 'pseudo-label' is technically a div that behaves like a label.
     * This allows the usage of block elements inside the fake label
     *
     * @private
     * @param {Object} interaction - the interaction instance
     * @param {jQueryElement} $container
     */
    var _pseudoLabel = function(interaction, $container){

        $container.off('.commonRenderer');

        var $choiceInputs = $container.find('.qti-choice').find('input:radio,input:checkbox').not('[disabled]').not('.disabled');

        $choiceInputs.on('keydown.commonRenderer', function(e){
            var keyCode = e.keyCode ? e.keyCode : e.charCode;
            if(keyCode !== KEY_CODE_TAB){
                e.preventDefault();
            }

            if( keyCode === KEY_CODE_SPACE || keyCode === KEY_CODE_ENTER){
                _triggerInput($(this).closest('.qti-choice'));
            }

            var $nextInput = $(this).closest('.qti-choice').next('.qti-choice').find('input:radio,input:checkbox').not('[disabled]').not('.disabled');
            var $prevInput = $(this).closest('.qti-choice').prev('.qti-choice').find('input:radio,input:checkbox').not('[disabled]').not('.disabled');

            if (keyCode === KEY_CODE_UP){
                $prevInput.focus();
            } else if (keyCode === KEY_CODE_DOWN){
                $nextInput.focus();
            }
        });

        $container.on('click.commonRenderer', '.qti-choice', function(e){
            var $choiceBox = $(this);

            e.preventDefault();
            e.stopPropagation();//required otherwise any tao scoped ,form initialization might prevent it from working

            _triggerInput($choiceBox);

            instructionMgr.validateInstructions(interaction, {choice : $choiceBox});
            containerHelper.triggerResponseChangeEvent(interaction);
        });
    };

    var _triggerInput = function($choiceBox){
        var $input = $choiceBox.find('input:radio,input:checkbox').not('[disabled]').not('.disabled');

        if($input.length){
            $input.prop('checked', !$input.prop('checked'));
            $input.trigger('change');
        }
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10278
     *
     * @param {Object} interaction - the interaction instance
     */
    var render = function(interaction){
        var $container = containerHelper.get(interaction);

        _pseudoLabel(interaction, $container);

        _setInstructions(interaction);

        if(interaction.attr('orientation') === 'horizontal') {
            sizeAdapter.adaptSize($('.add-option, .result-area .target, .choice-area .qti-choice', $container));
        }
    };

    /**
     * Define the instructions for the interaction
     * @private
     * @param {Object} interaction - the interaction instance
     */
    var _setInstructions = function(interaction){

        var min = interaction.attr('minChoices'),
            max = interaction.attr('maxChoices'),
            msg,
            choiceCount = _.size(interaction.getChoices()),
            minInstructionSet = false;

        //if maxChoice = 1, use the radio group behaviour
        //if maxChoice = 0, infinite choice possible
        if(max > 1 && max < choiceCount){

            var highlightInvalidInput = function($choice){
                var $input = $choice.find('.real-label > input'),
                    $li = $choice.css('color', '#BA122B'),
                    $icon = $choice.find('.real-label > span').css('color', '#BA122B').addClass('cross error');
                var timeout = interaction.data('__instructionTimeout');

                if(timeout){
                    clearTimeout(timeout);
                }
                timeout = setTimeout(function(){
                    $input.prop('checked', false);
                    $li.removeAttr('style');
                    $icon.removeAttr('style').removeClass('cross');
                    containerHelper.triggerResponseChangeEvent(interaction);
                }, 150);
                interaction.data('__instructionTimeout', timeout);
            };

            if(max === min){
                minInstructionSet = true;
                msg = __('You must select exactly %s choices', max);
                instructionMgr.appendInstruction(interaction, msg, function(data){
                    if(_getRawResponse(interaction).length >= max){
                        this.setLevel('success');
                        if(this.checkState('fulfilled')){
                            this.update({
                                level : 'warning',
                                message : __('Maximum choices reached'),
                                timeout : 2000,
                                start : function(){
                                    if(data && data.choice){
                                        highlightInvalidInput(data.choice);
                                    }
                                },
                                stop : function(){
                                    this.update({level : 'success', message : msg});
                                }
                            });
                        }
                        this.setState('fulfilled');
                    }else{
                        this.reset();
                    }
                });
            }else if(max > min){
                msg = max === 1 ? __('You can select maximum of 1 choice') : __('You can select maximum of %s choices', max);
                instructionMgr.appendInstruction(interaction, msg, function(data){
                    if(_getRawResponse(interaction).length >= max){
                        this.setMessage(__('Maximum choices reached'));
                        if(this.checkState('fulfilled')){
                            this.update({
                                level : 'warning',
                                timeout : 2000,
                                start : function(){
                                    if(data && data.choice){
                                        highlightInvalidInput(data.choice);
                                    }
                                },
                                stop : function(){
                                    this.setLevel('info');
                                }
                            });
                        }
                        this.setState('fulfilled');
                    }else{
                        this.reset();
                    }
                });
            }
        }

        if(!minInstructionSet && min > 0 && min < choiceCount){
            msg = min === 1 ? __('You must select at least 1 choice') : __('You must select at least %s choices', min);
            instructionMgr.appendInstruction(interaction, msg, function(){
                if(_getRawResponse(interaction).length >= min){
                    this.setLevel('success');
                }else{
                    this.reset();
                }
            });
        }
    };

    /**
     * Reset the responses previously set
     *
     * @param {Object} interaction - the interaction instance
     */
    var resetResponse = function(interaction){
        var $container = containerHelper.get(interaction);

        $('.real-label > input', $container).prop('checked', false);
    };

    /**
     * Set a new response to the rendered interaction.
     * Please note that it does not reset previous responses.
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10278
     *
     * @param {Object} interaction - the interaction instance
     * @param {0bject} response - the PCI formated response
     */
    var setResponse = function(interaction, response){
        var $container = containerHelper.get(interaction);

        try{
            _.each(pciResponse.unserialize(response, interaction), function(identifier){
                $container.find('.real-label > input[value=' + identifier + ']').prop('checked', true);
            });
            instructionMgr.validateInstructions(interaction);
        }catch(e){
            throw new Error('wrong response format in argument : ' + e);
        }
    };

    /**
     * Get the responses from the DOM.
     * @private
     * @param {Object} interaction - the interaction instance
     * @returns {Array} the list of choices identifiers
     */
    var _getRawResponse = function(interaction){
        var values = [];
        var $container = containerHelper.get(interaction);
        $('.real-label > input[name=response-' + interaction.getSerial() + ']:checked', $container).each(function(){
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
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10278
     *
     * @param {Object} interaction - the interaction instance
     * @returns {Object} the response formatted in PCI
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
    var getCustomData = function(interaction, data) {
        var listStyles = (interaction.attr('class') || '').match(/\blist-style-[\w-]+/) || [];
        return _.merge(data || {}, {
            horizontal : (interaction.attr('orientation') === 'horizontal'),
            listStyle: listStyles.pop()
        });
    };


    /**
     * Destroy the interaction by leaving the DOM exactly in the same state it was before loading the interaction.
     * @param {Object} interaction - the interaction
     */
    var destroy = function destroy(interaction){
        var $container = containerHelper.get(interaction);

        var timeout = interaction.data('__instructionTimeout');

        if(timeout){
            clearTimeout(timeout);
        }

        //remove event
        $container.off('.commonRenderer');
        $(document).off('.commonRenderer');

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

                $('.qti-simpleChoice', $container)
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
            $('.qti-simpleChoice', $container).each(function(){
               state.order.push($(this).data('identifier'));
            });
        }
        return state;
    };

    /**
     * Expose the common renderer for the choice interaction
     * @exports qtiCommonRenderer/renderers/interactions/ChoiceInteraction
     */
    return {
        qtiClass : 'choiceInteraction',
        template : tpl,
        getData : getCustomData,
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

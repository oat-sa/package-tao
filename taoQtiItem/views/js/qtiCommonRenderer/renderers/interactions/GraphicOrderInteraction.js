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
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/graphicOrderInteraction',
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
            var $orderList = $('ul', $container);
            var background = interaction.object.attributes;

            $container
                .off('resized.qti-widget.resolve')
                .one('resized.qti-widget.resolve', resolve);

            //create the paper
            interaction.paper = graphic.responsivePaper( 'graphic-paper-' + interaction.serial, interaction.serial, {
                width       : background.width,
                height      : background.height,
                img         : self.resolveUrl(background.data),
                imgId       : 'bg-image-' + interaction.serial,
                container   : $container
            });

            //create the list of number to order
            _renderOrderList(interaction, $orderList);

            //call render choice for each interaction's choices
            _.forEach(interaction.getChoices(), _.partial(_renderChoice, interaction, $orderList));

            //set up the constraints instructions
            instructionMgr.minMaxChoiceInstructions(interaction, {
                min: interaction.attr('minChoices'),
                max: interaction.attr('maxChoices'),
                getResponse : _getRawResponse,
                onError : function(data){
                    graphic.highlightError(data.target);
                }
            });
        });
    };

    /**
     * Render a choice inside the paper.
     * Please note that the choice renderer isn't implemented separately because it relies on the Raphael paper instead of the DOM.
     * @private
     * @param {Object} interaction
     * @param {jQueryElement} $orderList - the list than contains the orderers
     * @param {Object} choice - the hotspot choice to add to the interaction
     */
    var _renderChoice  =  function _renderChoice(interaction, $orderList, choice){

        var rElement = graphic.createElement(interaction.paper, choice.attr('shape'), choice.attr('coords'), {
            id : choice.serial,
            title : __('Select this area')
        })
        .click(function(){
            if(this.active){
                _unselectShape(interaction.paper, this, $orderList);
            } else {
                _selectShape(interaction.paper, this, $orderList);
            }
            containerHelper.triggerResponseChangeEvent(interaction);
            instructionMgr.validateInstructions(interaction, { choice : choice });
        });
    };

    /**
     * Render the list of numbers
     * @private
     * @param {Object} interaction
     * @param {jQueryElement} $orderList - the list than contains the orderers
     */
    var _renderOrderList = function _renderOrderList(interaction, $orderList){
        var $orderers;
        var size = _.size(interaction.getChoices());
        var min = interaction.attr('minChoices');
        var max = interaction.attr('maxChoices');

        //calculate the number of orderer to display
        if(max > 0 && max < size){
            size = max;
        } else if(min > 0 && min < size){
           size = min;
        }

        //add them to the list
        _.times(size, function(index){
            var position = index + 1;
            var $orderer = $('<li class="selectable" data-number="' + position + '">' + position +  '</li>');
            if(index === 0){
                $orderer.addClass('active');
            }
            $orderList.append($orderer);
        });

        //create related svg texts
        _createTexts(interaction.paper, size, $orderList);

        //bind the activation event
        $orderers = $orderList.children('li');
        $orderers.click(function(e){
            e.preventDefault();
            var $orderer = $(this);

            if(!$orderer.hasClass('active') && !$orderer.hasClass('disabled')){
                $orderers.removeClass('active');
                $orderer.addClass('active');
            }
        });
    };

    /**
     * Select a shape to position an order
     * @private
     * @param {Raphael.Paper} paper - the interaction paper
     * @param {Raphael.element} element - the selected shape
     * @param {jQueryElement} $orderList - the list than contains the orderers
     */
    var _selectShape = function _selectShape(paper, element, $orderList){

        //lookup for the active number
        var $active = $orderList.find('.active:first');
        if($active.length && $active.data('number') > 0){

            //associate the current number directly to the element
            element.data('number', $active.data('number'));
            element.active  = true;
            _showText(paper, element);
            graphic.updateElementState(element, 'active');

            //update the state of the order list
            $active.toggleClass('active disabled').siblings(':not(.disabled)').first().toggleClass('active');
        }
    };

    /**
     * Unselect a shape to free the position
     * @private
     * @param {Raphael.Paper} paper - the interaction paper
     * @param {Raphael.element} element - the unselected shape
     * @param {jQueryElement} $orderList - the list than contains the orderers
     */
    var _unselectShape = function _unselectShape(paper, element, $orderList){
        var number = element.data('number');

        //update element state
        element.active  = false;
        _hideText(paper, element);
        element.removeData('number');
        graphic.updateElementState(element, 'basic');

        //reset order list state and activate the removed number
        $orderList
            .children().removeClass('active')
            .filter('[data-number='+number+']').removeClass('disabled').addClass('active');
    };

    /**
     * Creates ALL the texts (the numbers to display in the shapes). They are created styled but hidden.
     *
     * @private
     * @param {Raphael.Paper} paper - the interaction paper
     * @param {Number} size - the number of numbers to create...
     * @param {jQueryElement} $orderList - the list than contains the orderers
     * @return {Array} the creates text element
     */
    var _createTexts = function _createTexts(paper, size){
        var texts = [];
        _.times(size, function(index){

            var number = index + 1;
            var text = graphic.createText(paper, {
                id          : 'text-' + number,
                content     : number,
                title       : __('Remove'),
                style       : 'order-text',
                hide        : true
            });

            //clicking the text will has the same effect that clicking the shape: unselect.
            text.click(function(){
                paper.forEach(function(element){
                    if(element.data('number') === number && element.events){  //we just need to retrieve the right element
                        //call the click event
                        var evt = _.where(element.events, { name : 'click'});
                        if(evt.length && evt[0] && typeof evt[0].f === 'function'){
                            evt[0].f.call(element);
                        }
                    }
                });
            });
            texts.push(text);
        });
        return texts;
    };

    /**
     * Show the text that match the element's number.
     * We need to display it at the center of the shape.
     * @private
     * @param {Raphael.Paper} paper - the interaction paper
     * @param {Raphael.Element} element - the element to show the text for
     */
    var _showText =  function _showText(paper, element){
        var bbox = element.getBBox();
        var transf;

        //we retrieve the good text from it's id
        var text = paper.getById('text-' + element.data('number'));
        if(text){

            //move it to the center of the shape (using absolute transform), and than display it
            transf = 'T' + (bbox.x + (bbox.width / 2)) + ',' +
                           (bbox.y + (bbox.height / 2));
            text.transform(transf)
                .show()
                .toFront();
        }
    };

    /**
     * Hide an element text.
     * @private
     * @param {Raphael.Paper} paper - the interaction paper
     * @param {Raphael.Element} element - the element to hide the text for
     */
    var _hideText =  function _hideText (paper, element){
        var text = paper.getById('text-' + element.data('number'));
        if(text){
            text.hide();
        }
    };

    /**
     * Get the responses from the interaction
     * @private
     * @param {Object} interaction
     * @returns {Array} of points
     */
    var _getRawResponse = function _getRawResponse(interaction){
        var response = [];
        _.forEach(interaction.getChoices(), function(choice){
            var elt = interaction.paper.getById(choice.serial);
            if(elt && elt.data('number')){
                response.push({
                    index : elt.data('number'),
                    id : choice.id()
                });
            }
        });
        return _(response).sortBy('index').map('id').value();
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
        var $container = containerHelper.get(interaction);
        var $orderList = $('ul', $container);
        if(response && interaction.paper){

            try {
                //try to unserualize tthe pci response
                responseValues = pciResponse.unserialize(response, interaction);
            } catch(e){}

            if(_.isArray(responseValues)){
                _.forEach(responseValues, function(responseValue, index){
                    var element;
                    var number = index + 1;

                    //get the choice that match the response
                    var choice = _(interaction.getChoices()).where({'attributes' : { 'identifier' : responseValue}}).first();
                    if(choice){
                       element = interaction.paper.getById(choice.serial);
                       if(element){
                            //activate the orderer to be consistant
                            $orderList.children('[data-number='+number+']').addClass('active');

                            //select the related shape
                            _selectShape(interaction.paper, element, $orderList);
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
        var $container = containerHelper.get(interaction);
        var $orderList = $('ul', $container);

        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(element){
                _unselectShape(interaction.paper, element, $orderList);
            }
        });

        $orderList.children('li').removeClass('active disabled').first().addClass('active');
    };


    /**
     i* Return the response of the rendered interaction
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
        return  pciResponse.serialize(_getRawResponse(interaction), interaction);
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
            $('ul', $container).empty();
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
     * Expose the common renderer for the interaction
     * @exports qtiCommonRenderer/renderers/interactions/SelectPointInteraction
     */
    return {
        qtiClass : 'graphicOrderInteraction',
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


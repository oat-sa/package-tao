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
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/graphicGapMatchInteraction',
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
            var $gapList = $('ul.source', $container);
            var $imageBox  = $('.main-image-box', $container);
            var background = interaction.object.attributes;

            interaction.gapFillers = [];

            $container
                .off('resized.qti-widget.resolve')
                .one('resized.qti-widget.resolve', resolve);

            //create the paper
            interaction.paper = graphic.responsivePaper( 'graphic-paper-' + interaction.serial, interaction.serial, {
                width       : background.width,
                height      : background.height,
                img         : self.resolveUrl(background.data),
                imgId       : 'bg-image-' + interaction.serial,
                container   : $container,
                resize      : function(newSize, factor){
                   $gapList.css('max-width', newSize + 'px');
                   if(factor !== 1){
                        $gapList.find('img').each(function(){
                            var $img = $(this);
                            $img.width( $img.attr('width') * factor );
                            $img.height( $img.attr('height') * factor );
                        });
                   }
                }
            });

            //call render choice for each interaction's choices
            _.forEach(interaction.getChoices(), _.partial(_renderChoice, interaction));

            //create the list of gap images
            _renderGapList(interaction, $gapList);

            //clicking the paper to reset selection
            _paperUnSelect(interaction);

        });
    };


    /**
     * Render a choice inside the paper.
     * Please note that the choice renderer isn't implemented separately because it relies on the Raphael paper instead of the DOM.
     *
     * @private
     * @param {Paper} paper - the raphael paper to add the choices to
     * @param {Object} interaction
     * @param {Object} choice - the hotspot choice to add to the interaction
     */
    var _renderChoice  =  function _renderChoice(interaction, choice){

        //create the shape
        var rElement = graphic.createElement(interaction.paper, choice.attr('shape'), choice.attr('coords'), {
            id : choice.serial,
            title : __('Select an image first'),
            hover : false
        })
        .data('max', choice.attr('matchMax') )
        .data('matching', [])
        .click(function onClickShape(){

            // check if can make the shape selectable on click
            if(_isMatchable(this) && this.selectable === true){
                _selectShape(interaction, this);
            }
        });
    };

    /**
     * Render the list of gap choices
     * @private
     * @param {Object} interaction
     * @param {jQueryElement} $orderList - the list than contains the orderers
     */
    var _renderGapList = function _renderGapList(interaction, $gapList){

        //activate the gap filling
        $gapList.children('li').click(function onClickGapImg (e){
            e.preventDefault();
            var $elt = $(this);
            if(!$elt.hasClass('disabled')){

                //toggle tha active state of the gap images
                if($elt.hasClass('active')){
                    $elt.removeClass('active');
                    _shapesUnSelectable(interaction);
                } else {
                    $gapList.children('li').removeClass('active');
                    $elt.addClass('active');
                    _shapesSelectable(interaction);
                }
            }
        });
    };

    /**
     * By clicking the paper image the shapes are restored to their default state
     * @private
     * @param {Object} interaction
     */
    var _paperUnSelect = function _paperUnSelect(interaction){
        var $container = containerHelper.get(interaction);
        var $gapImages = $('ul > li', $container);
        var image = interaction.paper.getById('bg-image-' + interaction.serial);
        if(image){
            image.click(function(){
                _shapesUnSelectable(interaction);
                $gapImages.removeClass('active');
            });
        }
    };
    /**
     * Select a shape (a gap image must be active)
     * @private
     * @param {Object} interaction
     * @param {Raphael.Element} element - the selected shape
     * @param {Boolean} [trackResponse = true] - if the selection trigger a response chane
     */
    var _selectShape = function _selectShape(interaction, element, trackResponse){
        var $img,
            $clone,
            gapFiller,
            id,
            bbox,
            shapeOffset,
            activeOffset,
            matching,
            currentCount;

        if(typeof trackResponse === 'undefined'){
            trackResponse = true;
        }

        //lookup for the active element
        var $container = containerHelper.get(interaction);
        var $gapList = $('ul', $container);
        var $active = $gapList.find('.active:first');
        var $imageBox = $('.main-image-box', $container);
        var boxOffset   = $imageBox.offset();

        if($active.length){

            //the macthing elements are linked to the shape
            id = $active.data('identifier');
            matching = element.data('matching') || [];
            matching.push(id);
            element.data('matching', matching);
            currentCount = matching.length;

            //the image to clone
            $img = $active.find('img');

            //then reset the state of the shapes and the gap images
            _shapesUnSelectable(interaction);
            $gapList.children().removeClass('active');

            $clone = $img.clone();
            shapeOffset  = $(element.node).offset();
            activeOffset   = $active.offset();

            $clone.css({
                'position' : 'absolute',
                'display'  : 'block',
                'z-index'  : 10000,
                'opacity'  : 0.8,
                'top'      : activeOffset.top - boxOffset.top,
                'left'     : activeOffset.left - boxOffset.left
            });

            $clone.appendTo($imageBox);
            $clone.animate({
                'top'       : shapeOffset.top - boxOffset.top,
                'left'      : shapeOffset.left - boxOffset.left
            }, 400, function animationEnd(){

                $clone.remove();

                //extract some coords for positioning
                bbox = element.getBBox();

                //create an image into the paper and move it to the selected shape
                gapFiller = graphic.createBorderedImage(interaction.paper, {
                    url     :  $img.attr('src'),
                    left    : bbox.x + (3 * (currentCount - 1)),
                    top     : bbox.y + (3 * (currentCount - 1)),
                    width   : parseInt($img.attr('width'), 10),
                    height  : parseInt($img.attr('height'), 10),
                    padding : 0,
                    border  : false,
                    shadow  : true
                })
                .data('identifier', id)
                .toFront()
                .unclick()
                .click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    if($gapList.find('.active').length > 0){

                        //simulate a click on the shape
                        graphic.trigger(element, 'click');

                    } else {
                        //update the element matching array
                        element.data('matching', _.without(element.data('matching') || [], this.data('identifier')));

                        //delete interaction.gapFillers[interaction.gapFillers.indexOf(gapFiller)];
                        interaction.gapFillers = _.without(interaction.gapFillers, gapFiller);

                        //and remove the filler
                        gapFiller.remove();

                        containerHelper.triggerResponseChangeEvent(interaction);
                    }
                });

                interaction.gapFillers.push(gapFiller);

                containerHelper.triggerResponseChangeEvent(interaction);
            });
        }
    };

    /**
     * Makes the shapes selectable (at least thos who can still accept matches)
     * @private
     * @param {Object} interaction
     */
    var _shapesSelectable = function _shapesSelectable(interaction){

        var tooltip = __('Select the area to add an image');

        //update the shape state
        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(_isMatchable(element)){
                element.selectable = true;
                graphic.updateElementState(element, 'selectable', tooltip);
            }
        });

        //update the gap images tooltip
        _.forEach(interaction.gapFillers, function(gapFiller){
            gapFiller.forEach(function(element){
                graphic.updateTitle(element, tooltip);
            });
        });
    };

    /**
     * Makes all the shapes UNselectable
     * @private
     * @param {Object} interaction
     */
    var _shapesUnSelectable = function _shapesUnSelectable(interaction){
        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(element){
                element.selectable = false;
                graphic.updateElementState(element, 'basic', __('Select an image first'));
            }
        });

        //update the gap images tooltip
        _.forEach(interaction.gapFillers, function(gapFiller){
            gapFiller.forEach(function(element){
                graphic.updateTitle(element, __('Remove'));
            });
        });
    };

    /**
     * Check if a shape can accept matches
     * @private
     * @param {Raphael.Element} element - the shape
     * @returns {Boolean} true if the element is matchable
     */
    var _isMatchable = function(element){
        var matchable = false;
        var matching, matchMax;
        if(element){
            matchMax = element.data('max') || 0;
            matching = element.data('matching') || [];
            matchable = (matchMax === 0 || matchMax > matching.length);
        }
        return matchable;
    };

    /**
     * Get the responses from the interaction
     * @private
     * @param {Object} interaction
     * @returns {Array} of matches
     */
    var _getRawResponse = function _getRawResponse(interaction){
        var pairs = [];

       _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(element && _.isArray(element.data('matching'))){
                _.forEach(element.data('matching'), function(match){
                    pairs.push([choice.id(), match]);
                });
            }
       });
       return pairs;
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
        var $container = containerHelper.get(interaction);
        var responseValues;
        if(response && interaction.paper){
            try{
                responseValues = pciResponse.unserialize(response, interaction);
            } catch(e){ }

            if(_.isArray(responseValues)){

                _.forEach(interaction.getChoices(), function(choice){
                    var element = interaction.paper.getById(choice.serial);
                    if(element){
                        _.forEach(responseValues, function(pair){
                            var index = _.indexOf(pair, choice.id());
                            if(index > -1 && pair.length === 2){

                                $("[data-identifier=" + pair[index === 0 ? 1 : 0] + "]", $container).addClass('active');
                                _selectShape(interaction, element, false);
                            }
                        });
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

        _shapesUnSelectable(interaction);

        _.forEach(interaction.gapFillers, function(gapFiller){
            graphic.trigger(gapFiller.items[0], 'click');
        });
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
     * Expose the common renderer for the hotspot interaction
     * @exports qtiCommonRenderer/renderers/interactions/HotspotInteraction
     */
    return {
        qtiClass        : 'graphicGapMatchInteraction',
        template        : tpl,
        render          : render,
        getContainer    : containerHelper.get,
        setResponse     : setResponse,
        getResponse     : getResponse,
        resetResponse   : resetResponse,
        destroy : destroy,
        setState : setState,
        getState : getState
    };
});

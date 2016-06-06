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
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/graphicAssociateInteraction',
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
            var $imageBox  = $('.main-image-box', $container);
            interaction._vsets = [];

            $container
                .off('resized.qti-widget.resolve')
                .one('resized.qti-widget.resolve', resolve);

            interaction.paper = graphic.responsivePaper( 'graphic-paper-' + interaction.serial, interaction.serial, {
                width       : background.width,
                height      : background.height,
                img         : self.resolveUrl(background.data),
                imgId       : 'bg-image-' + interaction.serial,
                container   : $container
            });

            //call render choice for each interaction's choices
            _.forEach(interaction.getChoices(), _.partial(_renderChoice, interaction));

            //make the paper clear the selection by clicking it
            _paperUnSelect(interaction);

            //set up the constraints instructions
            instructionMgr.minMaxChoiceInstructions(interaction, {
                min: interaction.attr('minAssociations'),
                max: interaction.attr('maxAssociations'),
                getResponse : _getRawResponse,
                onError : function(data){
                    if(data && data.target){
                        graphic.highlightError(data.target);
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
        var shape = choice.attr('shape');
        var coords = choice.attr('coords');
        var maxAssociations = interaction.attr('maxAssociations');

        var rElement = graphic.createElement(interaction.paper, shape, coords, {
            id : choice.serial,
            title : __('Select this area to start an association')
        })
        .data('max', choice.attr('matchMax'))
        .data('matching', 0)
        .removeData('assocs')
        .click(function(){
            var self = this;
            var active, assocs;

            //can't create more associations than the maxAssociations attr
            if(maxAssociations > 0 && _getRawResponse(interaction).length >= maxAssociations){
                _shapesUnSelectable(interaction);
                instructionMgr.validateInstructions(interaction, { choice : choice, target : this });
                return;
            }

            if(this.selectable) {
                active = _getActiveElement(interaction);
                if(active){

                    //increment the matching counter
                    active.data('matching', active.data('matching') + 1);
                    this.data('matching', this.data('matching') + 1);

                    //attach the response to the active (not the dest)
                    assocs = active.data('assocs') || [];
                    assocs.push(choice.id());
                    active.data('assocs', assocs);

                    //and create the path
                    _createPath(interaction, active, this, function onRemove(){

                        //decrement the matching counter
                        active.data('matching', active.data('matching') - 1);
                        self.data('matching', self.data('matching') - 1);

                        //detach the response from the active
                        active.data('assocs', _.remove(active.data('assocs') || [], choice.id()));

                        containerHelper.triggerResponseChangeEvent(interaction);
                        instructionMgr.validateInstructions(interaction, { choice : choice, target : self });
                    });
                }
                _shapesUnSelectable(interaction);

            } else if(this.active) {
                graphic.updateElementState(this, 'basic', __('Select another area to complete the association'));
                this.active = false;
                _shapesUnSelectable(interaction);
            } else if(_isMatchable(this)){
                graphic.updateElementState(this, 'active', __('Select this area to start an association'));
                this.active = true;
                _shapesSelectable(interaction, this);
            }

            containerHelper.triggerResponseChangeEvent(interaction);
            instructionMgr.validateInstructions(interaction, { choice : choice, target : this });
        });
    };

    /**
     * By clicking the paper image the shapes are restored to their default state
     * @private
     * @param {Object} interaction
     */
    var _paperUnSelect = function _paperUnSelect(interaction){
        var $container = containerHelper.get(interaction);
        var image = interaction.paper.getById('bg-image-' + interaction.serial);
        if(image){
            image.click(function(){
                _shapesUnSelectable(interaction);
                $container.trigger('unselect.graphicassociate');
            });
        }
    };

    /**
     * Get the element that has the active state
     * @private
     * @param {Object} interaction
     * @returns {Raphael.Element} the active element
     */
    var _getActiveElement = function _getActiveElement(interaction){
        var active;
        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(element && element.active === true){
                active = element;
                return false;
            }
        });
        return active;
    };

    /**
     * Create a path from a src element to a destination.
     * The path is selectable and can be removed by itself
     * @private
     * @param {Object} interaction
     * @param {Raphael.Element} srcElement - the path starts from this shape
     * @param {Raphael.Element} destElement - the path ends to this shape
     * @param {Function} onRemove - called back on path remove
     */
    var _createPath = function _createPath(interaction, srcElement, destElement, onRemove){
        var $container = containerHelper.get(interaction);

        //virtual set, not a raphael one, just to group the elements
        var vset = [];

        //get the middle point of the source shape
        var src = srcElement.getBBox();
        var sx = src.x + (src.width / 2);
        var sy = src.y + (src.height / 2);

        //get the middle point of the source shape
        var dest = destElement.getBBox();
        var dx = dest.x + (dest.width / 2);
        var dy = dest.y + (dest.height / 2);

        //create a path with bullets at the beginning and the end
        var srcBullet = interaction.paper.circle(sx, sy, 3)
            .attr(graphic._style['assoc-bullet']);

        var destBullet = interaction.paper.circle(dx, dy, 3)
            .attr(graphic._style['assoc-bullet']);

        var path = interaction.paper.path('M' + sx + ',' + sy + 'L' + sx + ',' + sy)
            .attr(graphic._style.assoc)
            .animate({path : 'M' + sx + ',' + sy + 'L' + dx + ',' + dy}, 300);

        //create an overall layer that make easier the path selection
        var layer = interaction.paper.path('M' + sx + ',' + sy + 'L' + dx + ',' + dy)
            .attr(graphic._style['assoc-layer']);

        //get the middle of the path
        var midPath = layer.getPointAtLength(layer.getTotalLength() / 2);

        //create an hidden background for the closer
        var closerBg = interaction.paper.circle(midPath.x, midPath.y, 9)
            .attr(graphic._style['close-bg'])
            .toBack();

        //create an hidden closer
        var closer = interaction.paper.path(graphic._style.close.path)
            .attr(graphic._style.close)
            .transform('T' + (midPath.x - 9 ) + ',' + (midPath.y - 9))
            .attr('title', _('Click again to remove'))
            .toBack();

        //the path is below the shapes
        srcElement.toFront();
        destElement.toFront();

        //add the path into a set
        vset = [srcBullet, path, destBullet, layer, closerBg, closer];
        interaction._vsets.push(vset);

        //to identify the element of the set outside the context
        _.invoke(vset, 'data', 'assoc-path', true);

        //enable to select the path by clicking the invisible layer
        layer.click(function selectLigne (){
            if(closer.attrs.opacity === 0){
                showCloser();
            } else {
                hideCloser();
            }
        });

        $container.on('unselect.graphicassociate', function(){
            hideCloser();
        });

        function showCloser(){
            closerBg
                .toFront()
                .animate({opacity: 0.8}, 300)
                .click(removeSet);
            closer
                .toFront()
                .animate({opacity: 1}, 300)
                .click(removeSet);
        }

        function hideCloser(){
           if(closerBg && closerBg.type){
                closerBg
                    .animate({opacity: 0}, 300, function(){
                        closerBg.toBack();
                    })
                    .unclick();
                closer
                    .animate({opacity: 0}, 300, function(){
                        closer.toBack();
                    })
                    .unclick();
            }
        }

        //remove set handler
        function removeSet(){
            _.invoke(vset, 'remove');
            interaction._vsets = _.without(interaction._vsets, vset);
            if(typeof onRemove === 'function'){
                onRemove();
            }
        }
    };

    /**
     * Makes the shapes selectable
     * @private
     * @param {Object} interaction
     * @param {Raphael.Element} active - the active shape
     */
    var _shapesSelectable = function _shapesSelectable(interaction, active){

        var assocs = active.data('assocs') || [];

        //update the shape state
        _.forEach(interaction.getChoices(), function(choice){
            var element;
            if(!_.contains(assocs, choice.id())){
                element = interaction.paper.getById(choice.serial);
                if(!element.active && element.id !== active.id &&  _isMatchable(element, active)){
                    element.selectable = true;
                    graphic.updateElementState(element, 'selectable');
                }
            }
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
                element.active = false;
                graphic.updateElementState(element, 'basic');
            }
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
            matching = element.data('matching') || 0;
            matchable = (matchMax === 0 || matchMax > matching);
        }
        return matchable;
    };

    /**
     * Get the response from the interaction
     * @private
     * @param {Object} interaction
     * @returns {Array} the response in raw format
     */
    var _getRawResponse = function _getRawResponse(interaction){
        var responses = [];
        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            var assocs = element.data('assocs');
            if(element && assocs){
               responses = responses.concat(_.map(assocs, function(id){
                    return [choice.id(), id];
               }));
            }
        });
        return responses;
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
            } catch(e){}

            if(_.isArray(responseValues)){
                //create an object with choiceId => shapeElement
                var map =  _.transform(interaction.getChoices(), function(res, choice){
                    res[choice.id()] = interaction.paper.getById(choice.serial);
                });
               _.forEach(responseValues, function(responseValue){
                    var el1, el2;
                    if(_.isArray(responseValue) && responseValue.length === 2){
                        el1 = map[responseValue[0]];
                        el2 = map[responseValue[1]];
                        if(el1 && el2){
                           graphic.trigger(el1, 'click');
                           graphic.trigger(el2, 'click');
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
        var toRemove = [];

        //reset response and state bound to shapes
        _.forEach(interaction.getChoices(), function(choice){
            var element = interaction.paper.getById(choice.serial);
            if(element){
                element.data({
                    'max' : choice.attr('matchMax'),
                    'matching' : 0,
                    'assocs' : []
                });
            }
        });

        //remove the paths, but outside the forEach as it is implemented as a linked list
        interaction.paper.forEach(function(elt){
            if(elt.data('assoc-path')){
                toRemove.push(elt);
            }
        });
        _.invoke(toRemove, 'remove');
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

            $container.off('.graphicassociate');

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
     * @exports qtiCommonRenderer/renderers/interactions/GraphicAssociateInteraction
     */
    return {
        qtiClass : 'graphicAssociateInteraction',
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

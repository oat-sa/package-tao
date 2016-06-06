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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */


/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 'i18n',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCreator/helper/dummyElement'
], function($, _, __, graphic, dummyElement){

    'use strict';

    /**
     * The Widget that provides components used by the QTI Creator for the Hotspot Interaction
     *
     * @exports taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/Widget
     */      
    var GraphicInteractionWidget = {

        /**
         * Create a basic Raphael paper or a placeholder of no bg is defined.
         * @param {Function} resize - called back on resize with the width and factor
         * @returns {Raphael.Paper} the raphael paper if initialized?
         */ 
        createPaper : function(resize){
            var paper;
            var $container  = this.$original;
            var $item       = $container.parents('.qti-item');
            var background  = this.element.object.attributes;
            var serial      = this.element.serial;

            if(!background.data){
                this._createPlaceholder();
            } else {
                paper = graphic.responsivePaper( 'graphic-paper-' + serial, serial, {
                    width       : background.width, 
                    height      : background.height,
                    img         : this.options.assetManager.resolve(background.data),
                    imgId       : 'bg-image-' + serial,
                    container   : $container,
                    resize      : function() {
                        var $blocks = $('.image-editor.solid, .block-listing.source', $container);
                        var minWidth = arguments[0];
                        if(typeof resize === 'function') {
                            resize.call(this, arguments);
                        }
                        if(!$container.hasClass('responsive')) {
                            $blocks.each(function() {
                                if(!parseInt(this.style.minWidth)){
                                    this.style.minWidth = (minWidth).toString() + 'px';
                                }
                            });
                        }
                    }
                });

                //listen for internal size change
                $item.on('resize.gridEdit.' + serial, function(){
                    $container.trigger('resize.qti-widget.' + serial);
                });

            }

            return paper;
        },

        /**
         * Creates a dummy placeholder for background.
         * @private
         */
        _createPlaceholder : function(){
            var self       = this;
            var $container = this.$original;
            var $imageBox  = $container.find('.main-image-box');
            var $editor    = $container.find('.image-editor');
            var diff       = ($editor.outerWidth() - $editor.width()) + ($container.outerWidth() - $container.width()) + 1;
            dummyElement.get({
                icon: 'image',
                css: {
                    width  : $container.innerWidth() - diff,
                    height : 200
                },
                title : __('Select an image first.')
            })
            .click(function(){
                var $upload  = $('[data-role="upload-trigger"]', self.$form);
                if($upload.length){
                    $upload.trigger('click');
                }
            })
            .appendTo($imageBox);
        },

        /**
         * call render choice for each interaction's choices
         */ 
        createChoices : function(){
            _.forEach(this.element.getChoices(), this._currentChoices, this);
        },
        
        /**
         * Add shape to the Raphael paper for a QTI choice
         * @private
         * @param {Object} choice - the QTI choice 
         */ 
        _currentChoices : function(choice){
            graphic.createElement(this.element.paper, choice.attr('shape'), choice.attr('coords'), {
                id          : choice.serial,
                touchEffect : false
            });
        }
   };

    return GraphicInteractionWidget;
});

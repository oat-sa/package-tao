/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'taoQtiItem/qtiCreator/widgets/helpers/content',
    'taoQtiItem/qtiCreator/widgets/helpers/textWrapper',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/htmlEditorTrigger'
], function(_, $, stateFactory, Question, htmlEditor, gridContentHelper, htmlContentHelper, textWrapper, toolbarTpl){
    
    'use strict';
    
    var ContainerInteractionStateQuestion = stateFactory.extend(Question, function(){

        this.buildEditor();

    }, function(){

        this.destroyTextWrapper();
        this.destroyEditor();
    });

    ContainerInteractionStateQuestion.prototype.buildEditor = function(){

        var _this = this,
            _widget = this.widget,
            container = _widget.element.getBody(),
            $container = _widget.$container,
            $editableContainer = $container.find('.qti-flow-container');

        $editableContainer.attr('data-html-editable-container', true);

        if(!htmlEditor.hasEditor($editableContainer)){

            var $bodyTlb = $(toolbarTpl({
                serial : _widget.serial,
                state : 'question'
            }));

            //add toolbar once only:
            $editableContainer.append($bodyTlb);
            $bodyTlb.show();

            //init text wrapper
            _this.initTextWrapper();

            //hack : prevent ckeditor from removing empty spans
            $container.find('.gapmatch-content').html('...');

            //create editor
            htmlEditor.buildEditor($editableContainer, {
                shieldInnerContent : false,
                change : gridContentHelper.getChangeCallbackForBlockStatic(container),
                markupChange : function(){
                    textWrapper.unwrap(this);
                },
                data : {
                    container : container,
                    widget : _widget
                }
            });
            
            //restore gaps
            $container.find('.gapmatch-content').empty();
        }
    };

    ContainerInteractionStateQuestion.prototype.destroyEditor = function(){

        var $container = this.widget.$container,
            $flowContainer = $container.find('.qti-flow-container');

        //hack : prevent ckeditor from removing empty spans
        $container.find('.gapmatch-content').html('...');

        //search and destroy the editor
        htmlEditor.destroyEditor($flowContainer);
        
        //restore gaps
        $container.find('.gapmatch-content').empty();

        //remove toolbar
        $flowContainer.find('.mini-tlb[data-role=cke-launcher-tlb]').remove();
    };

    ContainerInteractionStateQuestion.prototype.initTextWrapper = function(){

        var widget = this.widget,
            interaction = widget.element,
            $editable = widget.$container.find('.qti-flow-container [data-html-editable]'),
            gapModel = this.getGapModel(),
            $gapTlb = $(gapModel.toolbarTpl()).show();

        $gapTlb.on('mousedown', function(e){

            e.stopPropagation();//prevent rewrapping

            var $wrapper = $gapTlb.parent(),
                text = $wrapper.text().trim();

            //detach it from the DOM for another usage in the next future
            $gapTlb.detach();

            //create gap:
            $wrapper
                .removeAttr('id')
                .addClass('widget-box')
                .attr('data-new', true)
                .attr('data-qti-class', gapModel.qtiClass);

            textWrapper.destroy($editable);

            htmlContentHelper.createElements(interaction.getBody(), $editable, htmlEditor.getData($editable), function(newGapWidget){

                newGapWidget.changeState('question');
                textWrapper.create($editable);
                gapModel.afterCreate(widget, newGapWidget, _.escape(text));
            });
            
        }).on('mouseup', function(e){
            e.stopPropagation();//prevent rewrapping
        });

        //init text wrapper:
        $editable.on('editorready.wrapper', function(){
            textWrapper.create($(this));
        }).on('wrapped.wrapper', function(e, $wrapper){
            $wrapper.append($gapTlb);
        }).on('beforeunwrap.wrapper', function(){
            $gapTlb.detach();
        });
    };

    ContainerInteractionStateQuestion.prototype.destroyTextWrapper = function(){

        //destroy text wrapper:
        var $editable = this.widget.$container.find('.qti-flow-container [data-html-editable]');
        textWrapper.destroy($editable);
        $editable.off('.wrapper');

    };

    ContainerInteractionStateQuestion.prototype.getGapModel = function(){

        this.throwMissingRequiredImplementationError('getModel');
    };

    return ContainerInteractionStateQuestion;
});
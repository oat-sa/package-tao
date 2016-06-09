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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */
define([
    'lodash',
    'i18n',
    'jquery',
    'helpers',
    'taoQtiItem/qtiCreator/widgets/Widget',
    'taoQtiItem/qtiCreator/widgets/item/states/states',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiCreator/model/helper/container',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'taoQtiItem/qtiCreator/helper/xmlRenderer',
    'taoQtiItem/qtiCreator/helper/devTools',
    'taoQtiItem/qtiCreator/widgets/static/text/Widget',
    'taoQtiItem/qtiCreator/editor/styleEditor/styleEditor',
    'taoQtiItem/qtiCreator/editor/editor',
    'tpl!taoQtiItem/qtiCreator/tpl/notifications/genericFeedbackPopup',
    'taoQtiItem/qtiCreator/editor/jquery.gridEditor'
], function(
    _,
    __,
    $,
    helpers,
    Widget,
    states,
    Element,
    creatorRenderer,
    containerHelper,
    contentHelper,
    xmlRenderer,
    devTools,
    TextWidget,
    styleEditor,
    itemEditor,
    genericFeedbackPopup
    ){

    'use strict';

    var ItemWidget = Widget.clone();

    ItemWidget.initCreator = function(config){

        this.registerStates(states);

        Widget.initCreator.call(this);

        if(!config || !config.uri){
            throw new Error('missing required config parameter uri in item widget initialization');
        }

        this.renderer = config.renderer;

        this.itemUri = config.uri;

        this.initUiComponents();

        this.initTextWidgets(function(){

            //when the text widgets are ready:
            this.initGridEditor();

            //active debugger
            this.debug({
                state : false,
                xml : false
            });
        });
    };

    ItemWidget.buildContainer = function(){

        this.$container = this.$original;
    };

    ItemWidget.save = function(){
        return $.ajax({
            url : helpers._url('saveItem', 'QtiCreator', 'taoQtiItem', {uri : this.itemUri}),
            type : 'POST',
            contentType : 'text/xml',
            dataType : 'json',
            data : xmlRenderer.render(this.element)
        });
    };

    ItemWidget.initUiComponents = function(){

        var _widget = this,
            element = _widget.element,
            $saveBtn = $('#save-trigger'),
            $previewBtn = $('.preview-trigger');

        //init save button:
        $saveBtn.on('click', function(e){

            var $saveButton = $(this);

            //trigger save event
            $saveButton.trigger('beforesave.qti-creator');

            if($saveButton.hasClass('disabled')){
                e.preventDefault();
                return;
            }

            $saveButton.addClass('active');

            //defer exceution of save function to give beforesave chance to be executed
            _.defer(function(){

                $.when(styleEditor.save(), _widget.save()).done(function(){

                    var success = true,
                        feedbackArgs = {
                        message : __('Your item has been saved'),
                        type : 'success'
                    },
                    i = arguments.length;

                    $saveButton.removeClass('active');

                    while(i--){
                        if(arguments[i][1].toLowerCase() !== 'success'){
                            feedbackArgs = {
                                message : __('Failed to save item'),
                                type : 'error'
                            };
                            success = false;
                            break;
                        }
                    }

                    $saveButton.trigger('aftersave.qti-creator', [success]);
                    _createInfoBox(feedbackArgs);
                });
            });

        });

        $previewBtn.on('click', function(){
            itemEditor.initPreview(_widget);
        });

        //listen to invalid states:
        _widget.on('metaChange', function(data){
            if(data.element.getSerial() === element.getSerial() && data.key === 'invalid'){
                var invalid = element.data('invalid');
                if(_.size(invalid)){
                    $saveBtn.addClass('disabled');
                }else{
                    $saveBtn.removeClass('disabled');
                }
            }
        }, true);

    };

    ItemWidget.initGridEditor = function(){

        var _this = this,
            item = this.element,
            $itemBody = this.$container.find('.qti-itemBody'),
            $itemEditorPanel = $('#item-editor-panel');

        $itemBody.gridEditor();
        $itemBody.gridEditor('resizable');
        $itemBody.gridEditor('addInsertables', $('.tool-list > [data-qti-class]:not(.disabled)'), {
            helper : function(){
                return $(this).find('.icon').clone().addClass('dragging');
            }
        });

        $itemBody.on('beforedragoverstart.gridEdit', function(){

            $itemEditorPanel.addClass('dragging');
            $itemBody.removeClass('hoverable').addClass('inserting');

        }).on('dragoverstop.gridEdit', function(){

            $itemEditorPanel.removeClass('dragging');
            $itemBody.addClass('hoverable').removeClass('inserting');

        }).on('dropped.gridEdit.insertable', function(e, qtiClass, $placeholder){

            //a new qti element has been added: update the model + render
            $placeholder.removeAttr('id');//prevent it from being deleted

            if(qtiClass === 'rubricBlock'){
                //qti strange exception: a rubricBlock must be the first child of itemBody, nothing else...
                //so in this specific case, consider the whole row as the rubricBlock
                //by the way, in our grid system, rubricBlock can only have a width of col-12
                $placeholder = $placeholder.parent('.col-12').parent('.grid-row');
            }

            $placeholder.addClass('widget-box');//required for it to be considered as a widget during container serialization
            $placeholder.attr({
                'data-new' : true,
                'data-qti-class' : qtiClass
            });//add data attribute to get the dom ready to be replaced by rendering

            var $widget = $placeholder.parent().closest('.widget-box, .qti-item');
            var $editable = $placeholder.closest('[data-html-editable], .qti-itemBody');
            var widget = $widget.data('widget');
            var element = widget.element;
            var container = Element.isA(element, '_container') ? element : element.getBody();

            if(!element || !$editable.length){
                throw new Error('cannot create new element');
            }

            containerHelper.createElements(container, contentHelper.getContent($editable), function(newElts){

                creatorRenderer.get().load(function(){
                    var self = this;

                    _.forEach(newElts, function(elt, serial){
                        var $widget,
                            widget,
                            $colParent = $placeholder.parent();


                        elt.setRenderer(self);

                        if(Element.isA(elt, '_container')){
                            $colParent.empty();//clear the col content, and leave an empty text field
                            $colParent.html(elt.render());
                            widget = _this.initTextWidget(elt, $colParent);
                            $widget = widget.$container;
                        }else{
                            elt.render($placeholder);

                            //TODO resolve the promise it returns
                            elt.postRender();
                            widget = elt.data('widget');
                            if(Element.isA(elt, 'blockInteraction')){
                                $widget = widget.$container;
                            }else{
                                //leave the container in place
                                $widget = widget.$original;
                            }
                        }

                        //inform height modification
                        $widget.trigger('contentChange.gridEdit');
                        $widget.trigger('resize.gridEdit');

                        //active it right away:
                        if(Element.isA(elt, 'interaction')){
                            widget.changeState('question');
                        }else{
                            widget.changeState('active');
                        }

                    });
                }, this.getUsedClasses());
            });

        }).on('resizestop.gridEdit', function(){

            item.body($itemBody.gridEditor('getContent'));

        });

    };

    ItemWidget.initTextWidgets = function(callback){

        var _this = this,
            item = this.element,
            $originalContainer = this.$container,
            i = 1,
            subContainers = [];

        callback = callback || _.noop;

        //temporarily tag col that need to be transformed into
        $originalContainer.find('.qti-itemBody > .grid-row').each(function(){

            var $row = $(this);

            if(!$row.hasClass('widget-box')){//not a rubricBlock
                $row.children().each(function(){

                    var $col = $(this),
                        isTextBlock = false;

                    $col.contents().each(function(){
                        if(this.nodeType === 3 && this.nodeValue && this.nodeValue.trim()){
                            isTextBlock = true;
                            return false;
                        }
                    });

                    var $widget = $col.children();
                    if($widget.length > 1 || !$widget.hasClass('widget-blockInteraction')){//not an immediate qti element
                        if($widget.hasClass('colrow')){
                            $widget.each(function(){
                                var $subElement = $(this);
                                var $subWidget = $subElement.children();
                                if($subWidget.length > 1 || !$subWidget.hasClass('widget-blockInteraction')){
                                    $subElement.attr('data-text-block-id', 'text-block-' + i);
                                    i++;
                                }
                            });
                        }else{
                            isTextBlock = true;
                        }
                    }

                    if(isTextBlock){
                        $col.attr('data-text-block-id', 'text-block-' + i);
                        i++;
                    }
                });
            }
        });

        //clone the container to create the new container model:
        var $clonedContainer = $originalContainer.clone();
        $clonedContainer.find('.qti-itemBody > .grid-row [data-text-block-id]').each(function(){

            var $originalTextBlock = $(this),
                textBlockId = $originalTextBlock.data('text-block-id'),
                $subContainer = $originalTextBlock.clone(),
                subContainerElements = contentHelper.serializeElements($subContainer),
                subContainerBody = $subContainer.html();//get serialized body

            $originalTextBlock.removeAttr('data-text-block-id').html('{{_container:new}}');

            subContainers.push({
                body : subContainerBody,
                elements : subContainerElements,
                $original : $originalContainer.find('[data-text-block-id="' + textBlockId + '"]').removeAttr('data-text-block-id')
            });
        });

        //create new container model with the created sub containers
        contentHelper.serializeElements($clonedContainer);

        var serializedItemBody = $clonedContainer.find('.qti-itemBody').html(),
            itemBody = item.getBody();

        if(subContainers.length){

            containerHelper.createElements(itemBody, serializedItemBody, function(newElts){

                if(_.size(newElts) !== subContainers.length){

                    throw 'number of sub-containers mismatch';
                }else{

                    _.each(newElts, function(container){

                        var containerData = subContainers.shift();//get data in order
                        var containerElements = _detachElements(itemBody, containerData.elements);

                        container.setElements(containerElements, containerData.body);

                        _this.initTextWidget(container, containerData.$original);

                    });

                    _.defer(function(){
                        callback.call(_this);
                    });
                }
            });

        }else{

            callback.call(_this);
        }

    };

    var _detachElements = function(container, elements){

        var containerElements = {};
        _.each(elements, function(elementSerial){
            containerElements[elementSerial] = container.elements[elementSerial];
            delete container.elements[elementSerial];
        });
        return containerElements;
    };

    ItemWidget.initTextWidget = function(container, $col){
        return TextWidget.build(container, $col, this.renderer.getOption('textOptionForm'), {});
    };

    /**
     * Enable debugging
     *
     * @param {Boolean} [options.state = false] - log state change in console
     * @param {Boolean} [options.xml = false] - real-time qti xml display under the creator
     */
    ItemWidget.debug = function(options){

        options = options || {};

        if(options.state){
            devTools.listenStateChange();
        }

        if(options.xml){
            var $code = $('<code>', {'class' : 'language-markup'}),
                $pre = $('<pre>', {'class' : 'line-numbers'}).append($code);

            $('#item-editor-wrapper').append($pre);
            devTools.liveXmlPreview(this.element, $code);
        }

    };


    var _createInfoBox = function(data){
        var $messageBox = $(genericFeedbackPopup(data)),
            closeTrigger = $messageBox.find('.close-trigger');

        $('body').append($messageBox);

        closeTrigger.on('click', function(){
            $messageBox.fadeOut(function(){
                $(this).remove();
            });
        });

        setTimeout(function(){
            closeTrigger.trigger('click');
        }, 2000);

        return $messageBox;
    };

    return ItemWidget;
});

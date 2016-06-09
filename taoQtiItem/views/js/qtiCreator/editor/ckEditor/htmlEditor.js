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
    'i18n',
    'jquery',
    'ckeditor',
    'taoQtiItem/qtiCreator/helper/ckConfigurator',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/widgets/helpers/content',
    'taoQtiItem/qtiCreator/widgets/helpers/deletingState'
], function(_, __, $, CKEditor, ckConfigurator, Element, contentHelper, deletingHelper){
    "use strict";

    //prevent auto inline editor creation:
    CKEditor.disableAutoInline = true;

    var _defaults = {
        placeholder : __('some text ...'),
        shieldInnerContent : true,
        passthroughInnerContent : false,
        hideTriggerOnBlur : false
    };

    /**
     * Find the ck launcher (the trigger that toggle visibility of the editor) in the editor's container
     *
     * @param {JQuery} $editableContainer
     */
    function getTrigger($editableContainer){
        var $toolbar = $editableContainer.data('editor-toolbar');
        if($toolbar && $toolbar.length){
            return $toolbar.find('[data-role="cke-launcher"]');
        }else{
            return $editableContainer.find('[data-role="cke-launcher"]');
        }
    }

    /**
     * @param {JQuery} $editable - the element to be transformed into an editor
     * @param {JQuery} $editableContainer - the container of the editor
     * @param {Object} [options]
     * @param {String} [options.placeholder] - the place holder text
     * @param {Boolean} [options.shieldInnerContent] - define if the inner widget content should be protected or not
     * @param {Boolean} [options.passthroughInnerContent] - define if the inner widget content should be accessible directly or not
     * @param {Boolean} [options.hideTriggerOnBlur] - define if the ckeditor trigger should be hidden when the editor is blurred
     */
    function _buildEditor($editable, $editableContainer, options){

        var toolbarType, $trigger;

        options = _.defaults(options, _defaults);

        if( !($editable instanceof $) || !$editable.length){
            throw 'invalid jquery element for $editable';
        }
        if( !($editableContainer instanceof $) || !$editableContainer.length){
            throw 'invalid jquery element for $editableContainer';
        }

        $trigger = getTrigger($editableContainer);
        $editable.attr('placeholder', options.placeholder);
        var ckConfig = {
            dtdMode : 'qti',
            autoParagraph : false,
            enterMode : CKEditor.ENTER_P,
            floatSpaceDockedOffsetY : 10,
            taoQtiItem : {
                insert : function(){
                    if(options.data && options.data.container && options.data.widget){
                        contentHelper.createElements(options.data.container, $editable, _htmlEncode(this.getData()), function(createdWidget){
                            _activateInnerWidget(options.data.widget, createdWidget);
                        });
                    }
                }
            },
            floatSpace : {
                debug : true,
                initialHide : true,
                centerElement : function(){
                    //cke initialize the config too early.. so need to provide a callback to initialize it...
                    return getTrigger($editableContainer)[0];
                },
                on : {
                    ready : function(floatSpaceApi){

                        // works around tiny bug in tao floating space
                        // nose must be in .cke_toolbox rather than .cke
                        // otherwise z-indexing will fail
                        var nose = $(this).find('.cke_nose'),
                            oldParent = nose.parent(),
                            newParent = $(this).find('.cke_toolbox');
                        if(oldParent.is('.cke')){
                            newParent.append(nose);
                        }

                        floatSpaceApi.hide();
                        //@todo namespace this event: .editor.qti-widget or stuff...
                        $trigger.on('click', function(){
                            if($trigger.hasClass('active')){
                                $trigger.removeClass('active');
                                floatSpaceApi.hide();
                            }else{
                                $trigger.addClass('active');
                                floatSpaceApi.show();
                            }
                        });

                    },
                    changeMode : function(oldYMode, newYMode){
                        var element = this,
                            $element = $(element),
                            $nose = $element.find('.cke_nose');

                        var xModeIsReady = function(domElem){
                            var dfd = new $.Deferred(),
                                counter = 0,
                                check = setInterval(function(){
                                    var style = domElem.getAttribute('style');
                                    if(counter > 5 || style.indexOf('left') > -1 || style.indexOf('right') > -1){
                                        dfd.resolve();
                                        clearInterval(check);
                                    }
                                    counter++;
                                }, 1000);
                            return dfd.promise();
                        };

                        if(oldYMode !== newYMode){
                            $nose.removeClass('float-space-' + oldYMode).addClass('float-space-' + newYMode);
                        }

                        $.when(xModeIsReady(element)).done(function(){
                            if(!!element.style.left && !element.style.right){
                                $nose.removeClass('float-space-right').addClass('float-space-left');
                            }else{
                                $nose.removeClass('float-space-left').addClass('float-space-right');
                            }
                        });
                    }
                }
            },
            on : {
                instanceReady : function(e){

                    var widgets = {},
                        editor = e.editor;
                    
                    /**
                     * changed callback
                     * @param {Object} editor - ckeditor instance
                     */
                    function changed(editor){

                        _detectWidgetDeletion($editable, widgets, editor);

                        //callback:
                        if(_.isFunction(options.change)){
                            options.change.call(editor, _htmlEncode(editor.getData()));
                        }

                    }

                    /**
                     * Markup change callback
                     */
                    function markupChanged(){

                        //callbacks:
                        if(_.isFunction(options.markupChange)){
                            options.markupChange.call($editable);
                        }
                        if(_.isFunction(options.change)){
                            options.change.call(editor, _htmlEncode(editor.getData()));
                        }

                    }

                    //fix ck editor combo box display issue
                    $('#cke_' + e.editor.name + ' .cke_combopanel').hide();

                    //store it in editable elt data attr
                    $editable.data('editor', editor);
                    $editable.data('editor-options', options);

                    $editable.on('change', function(){
                        changed(editor);
                    });

                    editor.on('change', function(){
                        markupChanged(editor);
                    });

                    if(options.data && options.data.container){

                        //store in data-qti-container attribute the editor instance as soon as it is ready
                        $editable.data('qti-container', options.data.container);

                        //init editable
                        widgets = _rebuildWidgets(options.data.container, $editable, {
                            restoreState : true
                        });

                        if(options.shieldInnerContent){
                            _shieldInnerContent($editable, options.data.widget);
                        }else if(options.passthroughInnerContent){
                            _passthroughInnerContent($editable);
                        }
                    }

                    _focus(editor);

                    $editable.trigger('editorready', [editor]);

                    $('.qti-item').trigger('toolbarchange');

                },
                focus : function(e){

                    //show trigger
                    $trigger.show();

                    //callback:
                    if(_.isFunction(options.focus)){
                        options.focus.call(this, _htmlEncode(this.getData()));
                    }

                    $('.qti-item').trigger('toolbarchange');


                },
                blur : function(e){
                    return false;
                },
                configLoaded : function(e){
                    //@todo : do we really have to wait here to initialize the config?
                    var toolbarType = '';
                    if(options.toolbar && _.isArray(options.toolbar)){
                        ckConfig.toolbar = options.toolbar;
                    }else{
                        toolbarType = getTooltypeFromContainer($editableContainer);
                    }
                    e.editor.config = ckConfigurator.getConfig(e.editor, toolbarType, ckConfig);
                },
                afterPaste : function(e){
                    //@todo : we may add some processing on the editor after paste
                }
            }
        };

        return CKEditor.inline($editable[0], ckConfig);
    }

    /**
     * Assess
     * @param {type} $editableContainer
     * @returns {String}
     */
    function getTooltypeFromContainer($editableContainer){

        var toolbarType = 'qtiFlow';
        // build parameter for toolbar
        if($editableContainer.hasClass('widget-blockInteraction') || $editableContainer.hasClass('widget-textBlock') || $editableContainer.hasClass('widget-rubricBlock')){
            toolbarType = 'qtiBlock';
        }else if($editableContainer.hasClass('qti-prompt-container') || $editableContainer.hasClass('widget-hottext')){
            toolbarType = 'qtiInline';
        }
        return toolbarType;
    }

    /**
     * Find an inner element by its data attribute name
     * @param {JQuery} $container
     * @param {String} dataAttribute
     */
    function _find($container, dataAttribute){

        var $collection;

        if($container.data(dataAttribute)){
            $collection = $container;
        }else{
            $collection = $container.find('[data-' + dataAttribute + '=true]');
        }
        return $collection;
    }

    /**
     * Rebuild all innerwidgets located inside a container
     *
     * @param {Object} container
     * @param {JQuery} $container
     * @param {Object} options
     */
    function _rebuildWidgets(container, $container, options){

        options = options || {};

        var widgets = {};
        
        //re-init all widgets:
        _.each(_.values(container.elements), function(elt){

            var widget = elt.data('widget'),
                currentState = widget.getCurrentState().name;
                
            widgets[elt.serial] = widget.rebuild({
                context : $container,
                ready : function(widget){
                    if(options.restoreState){
                        //restore current state
                        widget.changeState(currentState);
                    }
                }
            });
        });

        $container.trigger('widgetCreated', [widgets, container]);

        return widgets;
    }

    /**
     * Find the widget container by its serial
     *
     * @param {JQuery} $container
     * @param {String} serial
     */
    function _findWidgetContainer($container, serial){
        return $container.find('.widget-box[data-serial=' + serial + ']');
    }

    /**
     * Detect if an inner widget has been removed
     *
     * @param {JQuery} $container
     * @param {Array} widgets
     * @param {Object} editor
     * @returns {undefined}
     */
    function _detectWidgetDeletion($container, widgets, editor){

        var deleted = [];

        _.each(widgets, function(w){

            if(!w.element.data('removed')){
                var $widget = _findWidgetContainer($container, w.serial);
                if(!$widget.length){
                    deleted.push(w);
                }
            }

        });

        if(deleted.length){

            var $messageBox = deletingHelper.createInfoBox(deleted);
            $messageBox.on('confirm.deleting', function(){

                _.each(deleted, function(w){
                    w.element.remove();
                    w.destroy();
                });
            }).on('undo.deleting', function(){

                editor.undoManager.undo();
            });

        }
    }

    /**
     * @param {JQuery} $widget - the widget to be protected
     * @returns {JQuery} The added layer (shield)
     */
    function addShield($widget){
        var $shield = $('<button>', {
            'class' : 'html-editable-shield'
        });

        $widget.attr('contenteditable', false);
        $widget.append($shield);
        return $shield;
    }

    /**
     * Protect the inner widgets of a container
     *
     * @param {JQuery} $container
     * @param {Object} containerWidget
     * @returns {undefined}
     */
    function _shieldInnerContent($container, containerWidget){

        $container.find('.widget-box').each(function(){
            var $widget = $(this);

            addShield($widget).on('click', function(e){
                var innerWidget;

                //click on shield:
                //1. this.widget.changeState('sleep');
                //2. clicked widget.changeState('active');

                e.stopPropagation();

                innerWidget = $widget.data('widget');
                _activateInnerWidget(containerWidget, innerWidget);
            });
        });

    }

    /**
     * Allow the inner widgets to be selected
     *
     * @param {JQuery} $container
     * @returns {undefined}
     */
    function _passthroughInnerContent($container){

        $container.find('.widget-box').each(function(){
            //just add the shield for visual consistency
            addShield($(this));
        });

    }

    /**
     * Activate the inner widget
     *
     * @param {Object} containerWidget
     * @param {Object} innerWidget
     * @returns {undefined}
     */
    function _activateInnerWidget(containerWidget, innerWidget){

        if(containerWidget && containerWidget.element && containerWidget.element.qtiClass){

            var listenToWidgetCreation = function(){
                containerWidget.$container
                  .off('widgetCreated')
                  .one('widgetCreated', function(e, widgets){
                    var targetWidget = widgets[innerWidget.serial];
                    if(targetWidget){
                        //FIXME potential race condition ? (debounce the enclosing event handler ?)
                        _.delay(function(){
                            if(Element.isA(targetWidget.element, 'interaction')){
                                targetWidget.changeState('question');
                            } else{
                                targetWidget.changeState('active');
                            }
                        }, 100);
                    }
                });

            };
            
            if(Element.isA(containerWidget.element, '_container') && !containerWidget.element.data('stateless')){

                //only _container that are NOT stateless need to change its state to sleep before activating the new one.
                listenToWidgetCreation();
                containerWidget.changeState('sleep');

            }else if(Element.isA(containerWidget.element, 'choice')){

                listenToWidgetCreation();
                containerWidget.changeState('question');

            }else if(Element.isA(innerWidget.element, 'choice')){

                innerWidget.changeState('choice');

            }else{

                innerWidget.changeState('active');
            }

        }else{

            innerWidget.changeState('active');
        }
    }

    /**
     * Special encoding of ouput html generated from ie8 : moved to xmlRenderer
     */
    var _htmlEncode = function(encodedStr){
        return encodedStr;
    };

    /**
     * Focus the editor and set the cursor to the end
     *
     * @param {Object} editor - the ckeditor instance
     * @returns {undefined}
     */
    function _focus(editor){
        var range;
        if (editor.editable() && editor.editable().parentNode){
            editor.focus();
            range = editor.createRange();
            range.moveToElementEditablePosition(editor.editable(), true);
            editor.getSelection().selectRanges([range]);
        }
    }

    var editorFactory = {
        /**
         * Check if all data-html-editable has an editor
         *
         * @param {type} $container
         * @returns {undefined}
         */
        hasEditor : function($container){

            var hasEditor = false;

            _find($container, 'html-editable').each(function(){
                hasEditor = !!$(this).data('editor');
                return hasEditor; //continue if true, break if false
            });

            return hasEditor;
        },
        /**
         * Instanciate the editor
         *
         * @param {JQuery} $container
         * @param {Object} [editorOptions]
         * @param {String} [editorOptions.placeholder] - the place holder text
         * @param {Boolean} [editorOptions.shieldInnerContent] - define if the inner widget content should be protected or not
         * @param {Boolean} [editorOptions.passthroughInnerContent] - define if the inner widget content should be accessible directly or not
         * @param {Boolean} [editorOptions.hideTriggerOnBlur] - define if the ckeditor trigger should be hidden when the editor is blurred
         * @returns {undefined}
         */
        buildEditor : function($container, editorOptions){

            _find($container, 'html-editable-container').each(function(){

                var editor,
                    $editableContainer = $(this),
                    $editable = $editableContainer.find('[data-html-editable]');

                //need to make the element html editable to enable ck inline editing:
                $editable.attr('contenteditable', true);

                //build it
                editor = _buildEditor($editable, $editableContainer, editorOptions);
            });

        },
        /**
         * Destroy the editor
         *
         * @param {JQuery} $container
         * @returns {undefined}
         */
        destroyEditor : function($container){

            _find($container, 'html-editable-container').each(function(){

                var $editableContainer = $(this),
                    $editable = $editableContainer.find('[data-html-editable]'),
                    $trigger = getTrigger($editableContainer);

                $trigger
                    .off()
                    .removeClass('active')
                    .hide();

                $editable.removeAttr('contenteditable');
                if($editable.data('editor')){

                    var editor = $editable.data('editor'),
                        options = $editable.data('editor-options');

                    //before destroying, ensure that data is stored
                    if(_.isFunction(options.change)){
                        options.change.call(editor, _htmlEncode(editor.getData()));
                    }

                    editor.focusManager.blur(true);
                    editor.destroy();

                    $editable.removeData('editor').removeData('editor-options');
                    if($editable.data('qti-container')){
                        _rebuildWidgets($editable.data('qti-container'), $editable);
                    }
                }
            });
        },
        /**
         * Get the editor content
         *
         * @param {JQuery} $editable
         */
        getData : function($editable){
            var editor = $editable.data('editor');
            if(editor){
                return _htmlEncode(editor.getData());
            }else{
                throw 'no editor attached to the DOM element';
            }
        },
        /**
         * Focus all the editors found in the given container
         *
         * @param {JQuery} $editable
         * @returns {undefined}
         */
        focus : function($editable){
            _find($editable, 'html-editable').each(function(){
                var editor = $(this).data('editor');
                if(editor){
                    _focus(editor);
                }
            });
        }
    };

    return editorFactory;
});

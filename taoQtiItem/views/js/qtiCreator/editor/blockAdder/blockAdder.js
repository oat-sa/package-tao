define([
    'jquery',
    'lodash',
    'tpl!taoQtiItem/qtiCreator/editor/blockAdder/tpl/addColumnRow',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiCreator/model/helper/container',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'taoQtiItem/qtiCreator/editor/elementSelector/selector',
    'taoQtiItem/qtiCreator/widgets/static/text/Widget'
], function($, _, adderTpl, Element, creatorRenderer, containerHelper, contentHelper, elementSelector, TextWidget){
    'use strict';

    var _ns = '.block-adder';
    var _wrap = '<div class="colrow"></div>';
    var _placeholder = '<div class="placeholder">';

    /**
     * Init the block adder on the item editor panel
     *
     * @param {Object} item - standard qti js object
     * @param {JQuery} $editorPanel - the container the selector popup will be located in
     * @param {Array} interactions - the array of authorable interactions
     */
    function create(item, $editorPanel, interactions){

        var selector, widget;
        var $itemEditorPanel = $('#item-editor-panel');

        /**
         * Get the qti item body dom
         *
         * @returns {JQuery}
         */
        function _getItemBody(){
            return $editorPanel.find('.qti-itemBody');
        }

        /**
         * Init insertion relative to a widget container
         *
         * @param {JQuery} $widget
         */
        function _initInsertion($widget){

            var $wrap = $(_wrap);
            var $colRow = $widget.parent('.colrow');

            //trigger event to restore all currently active widget back to sleep state
            $itemEditorPanel.trigger('beforesave.qti-creator.active');

            if(!$colRow.length){
                $widget.wrap(_wrap);
                $colRow = $widget.parent('.colrow');
            }
            $colRow.after($wrap);

            //create a new selector instance
            selector = elementSelector.create($wrap, $editorPanel, interactions);

            $editorPanel.off('.element-selector').on('selected.element-selector', function(e, qtiClass){

                var $placeholder = $(_placeholder);

                //remove old widget if applicable:
                if(widget){
                    //from model
                    widget.element.remove();
                    widget = null;
                    //from dom
                    $wrap.find('.widget-box').remove();
                }

                $wrap.addClass('tmp').prepend($placeholder);
                _insertElement(qtiClass, $placeholder);
                selector.reposition();

            }).on('done.element-selector', function(){
                _done($wrap);
            }).on('cancel.element-selector', function(){
                _cancel($wrap);
            });

            //when clicking outside of the selector popup, consider it done
            $itemEditorPanel.on('click' + _ns + ' mousedown' + _ns, function(e){
                var popup = selector.getPopup()[0];
                if(popup !== e.target && !$.contains(popup, e.target)){
                    _done($wrap);
                }
            });

            //select a default element type
            selector.activateElement('_container');
            selector.activatePanel('Text Block');

            //set into the inserting state
            _getItemBody().addClass('edit-inserting');
        }

        /**
         * End the current insertion state
         *
         * @returns {undefined}
         */
        function _endInsertion(){

            //destroy selector
            selector.destroy();

            //reset references
            selector = null;
            widget = null;

            $editorPanel.off('.element-selector');
            _getItemBody().removeClass('edit-inserting');

            //need to update item body
            item.body(contentHelper.getContent(_getItemBody()));

            //unbind events
            $itemEditorPanel.off(_ns);
        }

        /**
         * Function to define behaviour when the insertion is completed
         *
         * @param {JQuery} $wrap
         * @returns {undefined}
         */
        function _done($wrap){

            //remove tmp class
            $wrap.removeClass('tmp');

            //append button
            _appendButton(widget.$container);

            //activate the new widget:
            _.defer(function(){
                if(widget.element.is('interaction')){
                    widget.changeState('question');
                }else{
                    widget.changeState('active');
                }
                _endInsertion();
            });

        }

        /**
         * Function defining the behaviour when the insertion has been aborted
         *
         * @param {JQuery} $wrap
         * @returns {undefined}
         */
        function _cancel($wrap){

            //destroy interaction + colRow
            widget.element.remove();
            $wrap.remove();

            _endInsertion();
        }

        $editorPanel.find('.widget-block, .widget-blockInteraction').each(function(){
            _appendButton($(this));
        });

        //bind add event
        $editorPanel.on('mousedown', '.add-block-element .circle', function(e){

            e.preventDefault();
            e.stopPropagation();

            var $widget = $(this).parents('.widget-box');
            _initInsertion($widget);

        }).on('ready.qti-widget', function(e, _widget){

            var qtiElement = _widget.element;

            if(qtiElement.is('blockInteraction') || qtiElement.is('_container') || qtiElement.is('customInteraction')){

                _appendButton(_widget.$container);

                if(selector && _widget.$container.parent('.colrow.tmp').length){

                    //after update when we are in the selecting mode:
                    if(qtiElement.is('customInteraction')){
                        //pci rendering is asynchornous:
                        qtiElement.onPciReady(function(){
                            selector.reposition();
                        });
                    }
                    selector.reposition();

                    //store the reference to the newly created widget
                    widget = _widget;
                }
            }

        });

    }

    /**
     * Append the "plus" button into a widget
     *
     * @param {JQuery} $widget
     * @returns {undefined}
     */
    function _appendButton($widget){

        //only append button to no-tmp widget and only add it once:
        if(!$widget.children('.add-block-element').length &&
            !$widget.parent('.colrow.tmp').length){

            var $adder = $(adderTpl());
            $widget.append($adder);
            $adder.on('click mouseenter mouseleave', function(e){
                e.stopPropagation();
                //consider it outside of the widget element
                $(this).parent().trigger('mouseleave');
            });
        }
    }

    /**
     * Create a new qti element in place of the give $placehoder
     *
     * @param {String} qtiClass
     * @param {JQuery} $placeholder
     */
    function _insertElement(qtiClass, $placeholder){

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

            var creator = creatorRenderer.get();
            creator.load(function(){

                for(var serial in newElts){

                    var elt = newElts[serial],
                        $widget,
                        widget,
                        $colParent = $placeholder.parent();

                    elt.setRenderer(this);

                    if(Element.isA(elt, '_container')){
                        //the text widget is "inner-wrapped" so need to build a temporary container:
                        $placeholder.replaceWith('<div class="text-block"></div>');
                        var $textBlock = $colParent.find('.text-block');
                        $textBlock.html(elt.render());

                        //build the widget
                        widget = TextWidget.build(elt, $textBlock, creator.getOption('textOptionForm'), {
                            ready : function(){
                                //remove the temorary container
                                this.$container.unwrap();
                            }
                        });
                        $widget = widget.$container;

                    }else{
                        elt.render($placeholder);
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

                }
            }, this.getUsedClasses());
        });
    }

    return {
        create : create
    };
});

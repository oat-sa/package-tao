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
    'jquery',
    'lodash',
    'helpers',
    'core/dataattrhandler',
    //gui components
    'taoItems/preview/preview',
    'taoQtiItem/qtiCreator/editor/preparePrint',
    //appearance editor:
    'taoQtiItem/qtiCreator/editor/styleEditor/fontSelector',
    'taoQtiItem/qtiCreator/editor/styleEditor/colorSelector',
    'taoQtiItem/qtiCreator/editor/styleEditor/fontSizeChanger',
    'taoQtiItem/qtiCreator/editor/styleEditor/itemResizer',
    'taoQtiItem/qtiCreator/editor/styleEditor/styleEditor',
    'taoQtiItem/qtiCreator/editor/styleEditor/styleSheetToggler',
    // item related
    'taoQtiItem/qtiCreator/helper/itemSerializer'
], function(
    $,
    _,
    helpers,
    dataAttrHandler,
    preview,
    preparePrint,
    fontSelector,
    colorSelector,
    fontSizeChanger,
    itemResizer,
    styleEditor,
    styleSheetToggler,
    itemSerializer
    ){

    'use strict';

    var askForSave = false,
        lastItemData;

    /**
     * Serializes an element
     * @param {Object} element
     * @returns {String}
     */
    var serializeItem = function (element) {
        return itemSerializer.serialize(element);
    };

    /**
     * Sets the value of lastItemData. Serialize the value before assign it.
     * @param {Object} element
     */
    var setLastItemData = function (element) {
        lastItemData = serializeItem(element);
    };

    /**
     * Serializes the item at the initialization level
     * @param {Object} element
     */
    var initLastItemData = function(element) {
        if (_.isUndefined(lastItemData)) {
            setLastItemData(element);
        }
    };

    /**
     * Limit the size of the editor panel. This addresses an issue in which a
     * too large image would expand the editor panel to accommodate for the size
     * of the image.
     */
    function limitItemPanelWidth () {
        var itemEditorPanel = document.getElementById('item-editor-panel'),
            width = (function() {
                var _width = $('#panel-authoring').outerWidth();
                $('.item-editor-sidebar').each(function() {
                    _width -= $(this).outerWidth();
                });
                return _width.toString();
            }()),
            prefixes = ['webkit', 'ms', ''];

        _.forEach(prefixes, function(prefix) {
            itemEditorPanel.style[prefix + (prefix ? 'Flex' : 'flex')] = '0 0 ' + width + 'px';
        });
        itemEditorPanel.style.maxWidth = width + 'px';
    }



    var initStyleEditor = function(widget, config){

        styleEditor.init(widget.element, config);

        styleSheetToggler.init(config);

        // CSS widgets
        fontSelector();
        colorSelector();
        fontSizeChanger();
        itemResizer(widget.element);

    };


    /**
     * Confirm to save the item
     */
    var _confirmPreview = function (overlay) {

        var confirmBox = $('.preview-modal-feedback'),
            cancel = confirmBox.find('.cancel'),
            save = confirmBox.find('.save'),
            close = confirmBox.find('.modal-close');

        confirmBox.modal({ width: 500 });

        save.on('click', function () {
            overlay.trigger('save.preview');
            confirmBox.modal('close');
        });

        cancel.on('click', function () {
            confirmBox.modal('close');
        });
    };


    var initPreview = function(widget){

        var previewContainer, previewUrl;

        //serialize the item at the initialization level
        initLastItemData(widget.element);

        //compare the current item with the last serialized to see if there is any change
        if (!askForSave) {
            var currentItemData = serializeItem(widget.element);
            if (lastItemData !== currentItemData || currentItemData === '') {
                lastItemData = currentItemData;
                askForSave = true;
            }
        }

        previewUrl = helpers._url('index', 'QtiPreview', 'taoQtiItem') + '?uri=' + encodeURIComponent(widget.itemUri);
        previewContainer = preview.init(previewUrl);

        // wait for confirmation to save the item
        if (askForSave) {
            _confirmPreview(previewContainer);
            previewContainer.on('save.preview', function () {
                previewContainer.off('save.preview');
                askForSave = false;
                $.when(styleEditor.save(), widget.save()).done(function () {
                    preview.show();
                });
            });
        }
        else {
            //or show the preview
            preview.show();
        }

    };

    /**
     * Initialize interface
     */
    var initGui = function(widget, config){

        lastItemData = undefined;
        askForSave = !!widget.element.data('new');//new item needs to be saved once before being able to preview it

        //serialize the item at the initialization level
        initLastItemData(widget.element);

        //get the last value by saving
        $('#save-trigger')
            .off('.qti-creator')
            .on('click.qti-creator', function() {
                //catch the last value when saving
                setLastItemData(widget.element);
            })
            .on('aftersave.qti-creator', function(event, success) {
                //disable the askForSave flag only on save success
                if (success){
                    askForSave = false;
                }
            });

        //catch style changes
        $(document)
            .off('stylechange.qti-creator')
            .on('stylechange.qti-creator', function (event, detail) {
                //we need to save before preview of style has changed (because style content is not part of the item model)
                askForSave = !detail || !detail.initializing;
            });

        updateHeight();
        limitItemPanelWidth();

        $(window)
            .off('resize.qti-editor')
            .on('resize.qti-editor', _.throttle(
                function() {
                    updateHeight();
                    limitItemPanelWidth();
                }, 50));

        initStyleEditor(widget, config);

        preparePrint();

        var $itemPanel = $('#item-editor-panel'),
            $label = $('#item-editor-label'),
            $actionGroups = $('.action-group');

        $itemPanel.addClass('has-item');
        $label.text(config.label);
        $actionGroups.show();

    };

    /**
     * Update the height of the authoring tool
     * @private
     */
    var updateHeight = function updateHeight(){
        var $itemEditorPanel = $('#item-editor-panel');
        var $itemSidebars = $('.item-editor-sidebar');
        var $contentPanel = $('#panel-authoring');
        var /*$searchBar,
            searchBarHeight,*/
            footerTop,
            contentWrapperTop,
            remainingHeight;

        if (!$contentPanel.length || !$itemEditorPanel.length) {
            return;
        }

        //$searchBar = $contentPanel.find('.search-action-bar');
        //searchBarHeight = $searchBar.outerHeight() + parseInt($searchBar.css('margin-bottom')) + parseInt($searchBar.css('margin-top'));

        footerTop = (function() {
            var $footer = $('body > footer'),
                footerTop;
            $itemSidebars.hide();
            footerTop = $footer.offset().top;
            $itemSidebars.show();
            return footerTop;
        }());
        contentWrapperTop = $contentPanel.offset().top;
        remainingHeight = footerTop - contentWrapperTop - $('.item-editor-action-bar').outerHeight();


        // in the item editor the action bars are constructed slightly differently
        $itemEditorPanel.find('#item-editor-scroll-outer').css({ minHeight: remainingHeight, maxHeight: remainingHeight, height: remainingHeight });
        $itemSidebars.css({ minHeight: remainingHeight, maxHeight: remainingHeight, height: remainingHeight });
    };

    return {
        initGui : initGui,
        initPreview: initPreview
    };

});



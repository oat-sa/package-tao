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
 * Copyright (c) 2015 (original work) Open Assessment Techniologies SA
 *
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'util/strPad',
    'json!taoItems/preview/resources/device-list.json',
    'tpl!taoItems/preview/tpl/preview',
    'ui/themes',
    'ui/themeLoader',
    'ui/modal',
    'select2',
    'jquery.cookie'
], function ($, _, __, strPad, deviceList, previewTpl, themeHandler, themeLoader) {
    'use strict';


    var overlay,
        container,
        orientation = 'landscape',
        previewType = 'standard',
        previewTypes = {
            desktop: __('Desktop preview'),
            mobile: __('Mobile preview'),
            standard: __('Actual size')
        },
        themeObj = themeHandler.get('items') || {},
        $doc = $(document),
        $window = $(window),
        $body = $(document.body),
        screenSize = {
            width: $window.innerWidth(),
            height: $window.innerHeight()
        },
        sizeSettings = {
            width: 0,
            height: 0
        },
        scaleFactor = 1,
        typeDependant,
        $feedbackBox,
        $console,
        previewContainerMaxWidth,
        itemUri,
        winScrollObj = { x:0, y:0 };


    /**
     * Create data set for device selectors
     *
     * @param type
     * @returns {Array}
     * @private
     */
    var _getDeviceSelectorData = function (type) {

        /*
         * @todo
         * The device list is currently based on the devices found on the Chrome emulator.
         * This is not ideal and should be changed in the future.
         * I have http://en.wikipedia.org/wiki/List_of_displays_by_pixel_density in mind but we
         * will need to figure what criteria to apply when generating the list.
         */
        var devices = type === 'mobile' ? deviceList.tablets : deviceList.screens,
            options = [];

        _.forEach(devices, function (value) {

            options.push({
                value: value.label,
                label: value.label,
                dataValue: [value.width, value.height].join(','),
                selected: previewType === value.label
            });
        });


        return options;
    };

    /**
     * Collect all elements that can toggle their class name between mobile-* and desktop-*
     *
     * @private
     */
    var _setupTypeDependantElements = function () {
        typeDependant = overlay.add(overlay.find('.preview-scale-container').find('[class*="' + previewType + '"]'));
    };


    /**
     * Change the class name of all type dependant elements
     *
     * @param {String} newType
     */
    var _setPreviewType = function (newType) {

        if (newType === previewType) {
            return;
        }
        var re = new RegExp(previewType, 'g');

        typeDependant.each(function (i, el) {
            el.className = el.className.replace(re, newType);
        });

        previewType = newType;
    };


    /**
     * Set orientation
     *
     * @param {String} newOrientation
     * @private
     */
    var _setOrientation = function (newOrientation) {
        if (newOrientation === orientation) {
            return;
        }

        var re = new RegExp(orientation, 'g'),
            previewFrame = $('.preview-outer-frame')[0];

        previewFrame.className = previewFrame.className.replace(re, newOrientation);

        // reset global orientation
        orientation = newOrientation;
    };

    /**
     * Scale devices down to fit screen
     * @private
     */
    var _scale = function () {

        _computeScaleFactor();

        var $scaleContainer = $('.preview-scale-container'),
            _scaleFactor = previewType === 'standard' ? 1 : scaleFactor,
            containerScaledWidth = $scaleContainer.width() * _scaleFactor,
            left = (screenSize.width - containerScaledWidth) / 2;

        $scaleContainer.css({
            left: left > 0 ? left : 0,
            '-webkit-transform': 'scale(' + _scaleFactor + ',' + _scaleFactor + ')',
            '-ms-transform': 'scale(' + _scaleFactor + ',' + _scaleFactor + ')',
            'transform': 'scale(' + _scaleFactor + ',' + _scaleFactor + ')',
            '-webkit-transform-origin': '0 0',
            '-ms-transform-origin': '0 0',
            'transform-origin': '0 0'
        });
    };

    /**
     * position the preview depending on the height of the toolbar
     *
     * @private
     */
    var _positionPreview = function () {
        var topBarHeight = $(".preview-utility-bar").outerHeight();

        overlay[0].style.top = topBarHeight + 'px';
        overlay[0].style.maxHeight = 'calc(100vh - ' + topBarHeight + 'px - ' + $console.outerHeight() + 'px )';
    };


    /**
     * Compute scale factor based on screen size and device size
     *
     * @private
     */
    var _computeScaleFactor = function () {

        var scaleValues = {
            x: 1,
            y: 1
        };

        // 150/200 = device frames plus toolbar plus console plus some margin

        var requiredSize = {
            width: sizeSettings.width + 150,
            height: sizeSettings.height + 275
        };

        if (requiredSize.width > screenSize.width) {
            scaleValues.x = screenSize.width / requiredSize.width;
        }

        if (requiredSize.height > screenSize.height) {
            scaleValues.y = screenSize.height / requiredSize.height;
        }

        scaleFactor = Math.min(scaleValues.x, scaleValues.y);
    };

    /**
     * Add functionality to device selectors
     *
     * @private
     */
    var _setupDeviceSelectors = function () {


        var previewDeviceSelectors = overlay.find('.preview-device-selector');

        previewDeviceSelectors.on('change', function () {
            var elem = $(this),
                option = this.nodeName.toLowerCase() === 'select' ? this.options[this.selectedIndex] : this,
                type = elem.data('target'),
                val = $(option).data('value').split(','),
                i = val.length,
                container = overlay.find('.' + type + '-preview-container');


            while (i--) {
                val[i] = parseFloat(val[i]);
            }

            if (type === 'mobile' && orientation === 'portrait') {
                sizeSettings = {
                    width: val[1],
                    height: val[0]
                };
            }
            else {
                sizeSettings = {
                    width: val[0],
                    height: val[1]
                };
            }

            if (sizeSettings.width === container.width() && sizeSettings.height === container.height()) {
                return false;
            }

            _setPreviewType(type);
            container.css(sizeSettings);
            _scale();
            _adaptFrameSize();
        });

        previewDeviceSelectors.each(function () {
            if (this.nodeName.toLowerCase() === 'select') {
                $(this).select2({
                    minimumResultsForSearch: -1
                });
            }
        });
    };

    /**
     * Setup orientation selector. There is no actual orientation switch for desktop but it could
     * be added easily if required.
     * @private
     */
    var _setupOrientationSelectors = function () {

        $('select.orientation-selector').on('change',function () {

            var type = $(this).data('target'),
                previewFrame = $('.' + type + '-preview-frame'),
                container = previewFrame.find('.' + type + '-preview-container'),
                newOrientation = $(this).val();

            if (newOrientation === orientation) {
                return false;
            }

            sizeSettings = {
                height: container.width(),
                width: container.height()
            };

            container.css(sizeSettings);

            _setOrientation(newOrientation);

            _scale();
            _adaptFrameSize();


        }).select2({
            minimumResultsForSearch: -1
        });
    };

    /**
     * Close preview
     *
     * @returns {*|HTMLElement}
     */
    var _setupClosers = function () {
        var $closer = overlay.find('.preview-closer'),
            $feedbackCloser = $feedbackBox.find('.close-trigger'),
            $hideForever = $feedbackBox.find('a');
        var $iframe = $('#preview-iframe');

        $closer.on('click', function () {
            $doc.trigger('itemunload.preview');
//            commonRenderer.setContext($('.item-editor-item'));

            window.scrollTo(winScrollObj.x, winScrollObj.y);

            overlay.hide();
            $body.removeClass('preview-mode');

            //empty the iframe
            $iframe.off('load').attr('src', 'about:blank');
        });

        $doc.keyup(function (e) {
            if (e.keyCode === 27) {
                $closer.trigger('click');
            }
        });

        $feedbackCloser.on('click', function () {
            $feedbackBox.hide();
            $body.addClass('no-preview-feedback');
            _positionPreview();
            _scale();
        });

        $hideForever.on('click', function (e) {
            e.preventDefault();
            $.cookie('hidePreviewFeedback', true, { expires: 1000, path: '/' });
            $feedbackCloser.trigger('click');
        });
    };

    /**
     * Build options for themes
     *
     * @todo is there a way to know the selected theme?
     *
     * @returns {Array}
     * @private
     */
    var _getThemes = function() {

        var activeTheme = themeLoader(themeObj).getActiveTheme();

        var options = [];
        _(themeObj.available).forEach(function (data) {
            options.push({
                value: data.id,
                label: data.name,
                selected: data.id === activeTheme
            });
        });
        return options;
    };

    /**
     * Build options for preview types
     *
     * @returns {Array}
     * @private
     */
    var _getPreviewTypes = function () {
        var options = [];
        _(previewTypes).forEach(function (_previewLabel, _previewType) {
            options.push({
                value: _previewType,
                label: _previewLabel,
                selected: previewType === _previewType
            });
        });
        return options;
    };

    /**
     * Select preview type
     *
     * @private
     */
    var _setupViewSelector = function () {

        $('select.preview-type-selector').on('change',function () {
            var _previewType = $(this),
                value = _previewType.val();

            _setPreviewType(value);

            $('.' + value + '-device-selector').trigger('change');
        }).select2({
            minimumResultsForSearch: -1
        });

    };


    /**
     * Select preview theme
     *
     * @private
     */
    var _setupThemeSelector = function () {
        $('select.preview-theme-selector').on('change',function () {
            var iframe = document.getElementById('preview-container');
            var iframeDoc = iframe.contentWindow || iframe.contentDocument;
            iframeDoc.$('.qti-item').trigger('themechange', [$(this).val()]);

        }).select2({
            minimumResultsForSearch: -1
        });

    };

    var _adaptFrameSize = function () {

        var $previewContainer = $('.preview-container'),
            $iframe = (function () {
                var _iframe = $previewContainer.find('iframe');
                _iframe.height('');
                return _iframe;
            }()),
            contentHeight = $iframe.contents().outerHeight(),
            containerHeight = $previewContainer.innerHeight();

        if (previewType !== 'standard') {
            if (contentHeight < containerHeight) {
                contentHeight = containerHeight;
            }
        }
        else {
            $previewContainer.height(contentHeight + 10);
        }

        $iframe.height(contentHeight);
    };


    /**
     * Set the size for the standard preview
     * @param height
     * @private
     */
    var _updateStandardPreviewSize = function (height) {
        var $selector = $('.standard-device-selector'),
            values = ($selector.val() ? $selector.val().split(',') : '') || [$window.width().toString()],
            valueStr = values.join(',');

        values[1] = height || values[1] || '1200';

        $selector.val(valueStr).data('value', valueStr);
    };


    /**
     * Console
     *
     * @private
     */
    var _initConsole = function () {
        var $body = $console.find('.preview-console-body'),
            $listing = $body.find('ul'),
            $closer = $console.find('.preview-console-closer');

        $console.off('updateConsole').on('updateConsole', function (event, type, message) {
            var timer = new Date(),
                logTime = [
                    strPad(timer.getHours(), 2, '0', 'STR_PAD_LEFT'),
                    strPad(timer.getMinutes(), 2, '0', 'STR_PAD_LEFT'),
                    strPad(timer.getSeconds(), 2, '0', 'STR_PAD_LEFT')
                ].join(':'),
                msgStr = '<span class="log-time">' + strPad(logTime, 12, ' ') + '</span> ' +
                    '<span class="log-type">' + strPad(type, 18, ' ') + '</span> ' +
                    '<span class="log-message">' + strPad(message, 18, ' ') + '</span>';
            $listing.append('<li><pre>' + msgStr + '</pre></li>');
            if (!$body.is(':visible')) {
                $body.slideDown('slow', function () {
                    $closer.show();
                    $listing.scrollTop(10000);
                });
            }
            else {
                $listing.scrollTop(10000);
            }
        });

        $closer.on('click', function () {
            $body.slideUp('slow', function () {
                $closer.hide();
            });
        });
    };



    /**
     * Display the preview
     */
    var show = function () {

        $.ajax({
            url: itemUri,
            dataType: 'html'
        }).done(function (data) {

            winScrollObj = { x: window.scrollX, y: window.scrollY };
            window.scrollTo(0,0);

            $body.addClass('preview-mode');

            // $.show() does not work from the item manager
            // this is either a miracle or a jquery bug
            // overlay.hide().show();
            overlay[0].style.display = 'block';

            overlay.find('select:visible').not('.preview-theme-selector').trigger('change');
            _scale();
            _positionPreview();

            $('.preview-item-container').html(data);
        });


    };

    /**
     * Setting up screen size according preview viewport size
     * @private
     */
    function _setupScreenSize() {
        screenSize = {
            width: $('.preview-overlay').innerWidth(),
            height: $('.preview-overlay').innerHeight()
        };
    }

    /**
     * Create preview
     *
     * @param {String} _itemUri
     */
    var init = function (_itemUri) {

        if(!_itemUri || _itemUri.length === 0){
            throw new TypeError('Wrong URI');
        }

        $('.preview-overlay').remove();
        container = null;
        overlay = $(previewTpl({
            mobileDevices: _getDeviceSelectorData('mobile'),
            desktopDevices: _getDeviceSelectorData('desktop'),
            previewTypes: _getPreviewTypes(),
            previewType: previewType,
            themes: _getThemes(),
            hasThemes: _.size(themeObj.available) > 1
        }));

        $body.append(overlay);

        previewContainerMaxWidth = parseInt($('.preview-container').css('max-width'), 10);

        $feedbackBox = overlay.find('.preview-message-box');
        if ($.cookie('hidePreviewFeedback')) {
            $feedbackBox.hide();
            $body.addClass('no-preview-feedback');
        }

        $console = overlay.find('#preview-console');

        _initConsole();
        _updateStandardPreviewSize();
        _setupTypeDependantElements();
        _setupDeviceSelectors();
        _setupOrientationSelectors();
        _setupViewSelector();
        _setupThemeSelector();
        _setupClosers();
        _setupScreenSize();
        _computeScaleFactor();
        _setPreviewType(previewType);
        _setOrientation(orientation);

        $window.on('resize orientationchange', function () {
            _setupScreenSize();
            _updateStandardPreviewSize();
            _computeScaleFactor();
            _scale();
        });

        itemUri = _itemUri;

        return overlay;
    };

    return {
        init: init,
        show: show
    };
});

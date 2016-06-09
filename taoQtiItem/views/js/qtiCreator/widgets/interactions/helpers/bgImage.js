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

/**
 *
 * @author dieter <dieter@taotesting.com>
 */
define([
    'util/url',
    'ui/mediasizer'
], function (url) {
    'use strict';

    /**
     * @exports
     */

    /**
     * Handle images that are larger than the canvas
     *
     * @param widget
     */
    function handleOversizedImages(widget) {

        var $svg = widget.$original.find('.svggroup svg');
        var viewBox;
        var $bgImage = $svg.find('image');
        var trueSize = $bgImage[0].getBoundingClientRect();
        var imgWidth = parseInt(trueSize.width, 10);
        var imgHeight = parseInt(trueSize.height, 10);

        if(imgWidth >= Number(widget.element.object.attr('width'))) {
            return;
        }

        // update model
        widget.element.object.attr('width', imgWidth);
        widget.element.object.attr('height', imgHeight);

        // change image attributes
        $bgImage[0].setAttribute('width', imgWidth);
        $bgImage[0].setAttribute('height', imgHeight);

        // change svg view box
        viewBox = $svg[0].getAttribute('viewBox').split(' ');
        viewBox[2] = imgWidth;
        viewBox[3] = imgHeight;
        $svg[0].setAttribute('viewBox', viewBox.join(' '));

        $svg[0].setAttribute('width', imgWidth);
        $svg[0].setAttribute('height', imgHeight);

        $svg.parents('.main-image-box').css({ height: imgHeight, width: imgWidth });

        widget.$original.trigger('resize.qti-widget.' + widget.serial, [imgWidth, imgHeight]);
    }


    /**
     * Setup the background image
     *
     * @param widget
     */
    function setupImage(widget) {

        var $bgImage = widget.$original.find('.svggroup svg image');

        if (widget.$original.hasClass('responsive')) {
            return;
        }

        if (!!$bgImage.length) {
            // handle images larger than the canvas
            handleOversizedImages(widget);

            // setup media sizer
            setupMediaSizer(widget);
        }
    }

    /**
     * Setup media sizer when item has a fixed size
     *
     * @param widget
     * @returns {*}
     */
    function setupMediaSizer(widget) {

        var $mediaSizer = widget.$form.find('.media-sizer-panel');

        $mediaSizer.empty().mediasizer({
            target: widget.$original.find('.svggroup svg image'),
            showResponsiveToggle: false,
            showSync: false,
            responsive: false,
            parentSelector: widget.$original.attr('id'),
            applyToMedium: false,
            maxWidth: parseInt(widget.element.object.attr('width'), 10)
        });

        $mediaSizer.on('sizechange.mediasizer', function (e, params) {
            var width = parseInt(params.width, 10);
            var height = parseInt(params.height, 10);

            widget.element.object.attr('width', width);
            widget.element.object.attr('height', height);

            widget.$original.trigger('resize.qti-widget.' + widget.serial, [width, height]);
        });
        return $mediaSizer;
    }


    /**
     * Object with callbacks that are common to all graphic interactions
     *
     * @param widget
     * @param formElement
     * @param callbacks, existing callbacks if any
     */
    function setChangeCallbacks(widget, formElement, callbacks) {

        callbacks = callbacks || {};
        callbacks.data = function (interaction, value) {
            interaction.object.attr('data', url.encodeAsXmlAttr(value));
            widget.rebuild({
                ready: function (widget) {
                    widget.changeState('question');
                }
            });
        };
        callbacks.width = function (interaction, value) {
            interaction.object.attr('width', parseInt(value, 10));
        };
        callbacks.height = function (interaction, value) {
            interaction.object.attr('height', parseInt(value, 10));
        };
        callbacks.type = function (interaction, value) {
            if (!value || value === '') {
                interaction.object.removeAttr('type');
            }
            else {
                interaction.object.attr('type', value);
            }
        };

        formElement.setChangeCallbacks(widget.$form, widget.element, callbacks, { validateOnInit: false });
    }


    return {
        setupImage: setupImage,
        setChangeCallbacks: setChangeCallbacks
    };
});

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
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'context',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/uploadInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'ui/progressbar',
    'ui/previewer',
    'ui/modal',
    'ui/waitForMedia',
    'filereader'
], function ($, _, __, context, tpl, containerHelper, instructionMgr) {
    'use strict';

    //FIXME this response is global to the app, it must be linked to the interaction!
    var _response = {"base": null};

    var _initialInstructions = __('Browse your computer and select the appropriate file.');

    var _readyInstructions = __('The selected file is ready to be sent.');

    var _handleSelectedFiles = function (interaction, file) {
        instructionMgr.removeInstructions(interaction);
        instructionMgr.appendInstruction(interaction, _initialInstructions);

        var $container = containerHelper.get(interaction);

        // Show information about the processed file to the candidate.
        var filename = file.name;
        var filesize = file.size;
        var filetype = file.type;

        $container.find('.file-name').empty()
            .append(filename);

        // Let's read the file to get its base64 encoded content.
        var reader = new FileReader();

        // Update file processing progress.

        reader.onload = function (e) {
            instructionMgr.removeInstructions(interaction);
            instructionMgr.appendInstruction(interaction, _readyInstructions, function () {
                this.setLevel('success');
            });
            instructionMgr.validateInstructions(interaction);

            $container.find('.progressbar').progressbar('value', 100);

            var base64Data = e.target.result;
            var commaPosition = base64Data.indexOf(',');

            // Store the base64 encoded data for later use.
            var base64Raw = base64Data.substring(commaPosition + 1);
            _response = {"base": {"file": {"data": base64Raw, "mime": filetype, "name": filename}}};

            var visibleFileUploadPreview = getCustomData(interaction);

            var $previewArea = $container.find('.file-upload-preview');

            $previewArea
                .toggleClass('visible-file-upload-preview runtime-visible-file-upload-preview', visibleFileUploadPreview.isPreviewable)
                .previewer({
                    url: reader.result,
                    name: filename,
                    type: filetype.substr(0, filetype.indexOf('/'))
                });

            // we wait for the image to be completely loaded
            $previewArea.waitForMedia(function(){
                var $originalImg = $previewArea.find('img'),
                    $largeDisplay = $('.file-upload-preview-popup'),
                    $item = $('.qti-item'),
                    itemWidth = $item.width(),
                    winWidth = $(window).width() - 80,
                    fullHeight = $('body').height(),
                    imgNaturalWidth,
                    isOversized,
                    modalWidth;

                if(!$originalImg.length) {
                    return;
                }

                imgNaturalWidth = $originalImg[0].naturalWidth;
                isOversized = imgNaturalWidth > itemWidth;
                modalWidth = Math.min(winWidth, imgNaturalWidth);

                $previewArea.toggleClass('clickable', isOversized);

                if(!isOversized) {
                    return;
                }

                $previewArea.on('click', function(){

                    $('.upload-ia-modal-bg').remove();

                    // remove any previous unnecessary content before inserting the preview image
                    var $modalBody = $largeDisplay.find('.modal-body');
                    $modalBody.empty().append($originalImg.clone());

                    $largeDisplay
                        .on('opened.modal', function(){

                            // prevents the rest of the page from scrolling when modal is open
                            $('.tao-item-scope.tao-preview-scope').css('overflow', 'hidden');

                            $largeDisplay.css({
                                width: modalWidth,
                                height: fullHeight,
                                left: (modalWidth - itemWidth -40) / -2
                            });

                        })
                        .on('closed.modal', function(){
                            // make the page scrollable again
                            $('.tao-item-scope.tao-preview-scope').css('overflow', 'auto');

                        })
                        .modal({modalOverlayClass: 'modal-bg upload-ia-modal-bg'});

                });
            });

        };

        reader.onloadstart = function (e) {
            instructionMgr.removeInstructions(interaction);
            $container.find('.progressbar').progressbar('value', 0);
        };

        reader.onprogress = function (e) {
            var percentProgress = Math.ceil(Math.round(e.loaded) / Math.round(e.total) * 100);
            $container.find('.progressbar').progressbar('value', percentProgress);
        };

        reader.readAsDataURL(file);

    };

    var _resetGui = function (interaction) {
        var $container = containerHelper.get(interaction);
        $container.find('.file-name').text(__('No file selected'));
        $container.find('.btn-info').text(__('Browse...'));
        $container.find('.file-upload-preview').toggleClass(
            'visible-file-upload-preview',
            interaction.attr('type') && interaction.attr('type').indexOf('image') === 0
        );
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     *
     * @param {object} interaction
     */
    var render = function (interaction, options) {
        var $container = containerHelper.get(interaction);
        _resetGui(interaction);

        instructionMgr.appendInstruction(interaction, _initialInstructions);

        var changeListener = function (e) {
            var file = e.target.files[0];

            // Are you really sure something was selected
            // by the user... huh? :)
            if (typeof(file) !== 'undefined') {
                _handleSelectedFiles(interaction, file);
            }
        };

        var $input = $container.find('input');

        $container.find('.progressbar').progressbar();

        if (window.File && window.FileReader && window.FileList) {
            // Yep ! :D
            $input.bind('change', changeListener);
        }
        else {
            // Nope... :/
            $input.fileReader({
                id: 'fileReaderSWFObject',
                //FIXME this is not going to work outside of TAO
                filereader: context.taobase_www + 'js/lib/polyfill/filereader.swf',
                callback: function () {
                    $input.bind('change', changeListener);
                }
            });
        }

        // IE Specific hack, prevents button to slightly move on click
        $input.bind('mousedown', function (e) {
            e.preventDefault();
            $(this).blur();
            return false;
        });
    };

    var resetResponse = function (interaction) {

        var $container = containerHelper.get(interaction);
        _resetGui(interaction);
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
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function (interaction, response) {
        var $container = containerHelper.get(interaction);

        if (response.base !== null) {
            var filename = (typeof response.base.file.name !== 'undefined') ? response.base.file.name :
                'previously-uploaded-file';
            $container.find('.file-name').empty()
                .text(filename);
        }

        _response = response;
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
    var getResponse = function (interaction) {
        return _response;
    };

    var destroy = function (interaction) {

        //remove event
        $(document).off('.commonRenderer');
        containerHelper.get(interaction).off('.commonRenderer');

        //remove instructions
        instructionMgr.removeInstructions(interaction);

        //remove all references to a cache container
        containerHelper.reset(interaction);
    };

    /**
     * Set the interaction state. It could be done anytime with any state.
     *
     * @param {Object} interaction - the interaction instance
     * @param {Object} state - the interaction state
     */
    var setState = function setState(interaction, state) {
        if (_.isObject(state)) {
            if (state.response) {
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
    var getState = function getState(interaction) {
        var $container;
        var state = {};
        var response = interaction.getResponse();

        if (response) {
            state.response = response;
        }
        return state;
    };

    /**
     * Set additional data to the template (data that are not really part of the model).
     * @param {Object} interaction - the interaction
     * @param {Object} [data] - interaction custom data
     * @returns {Object} custom data
     * @TODO isPreviwable could be nicely implemented using tao/views/js/core/mimetype.js
     * This way we could cover a lot more types. How could this be matched with the preview templates
     * in tao/views/js/ui/previewer.js
     */
    var getCustomData = function (interaction, data) {
        return _.merge(data || {}, {
            isPreviewable: interaction.attr('type') && interaction.attr('type').indexOf('image') === 0
        });
    };

    return {
        qtiClass: 'uploadInteraction',
        template: tpl,
        render: render,
        getContainer: containerHelper.get,
        setResponse: setResponse,
        getResponse: getResponse,
        resetResponse: resetResponse,
        destroy: destroy,
        setState: setState,
        getState: getState,
        getData: getCustomData,

        // Exposed private methods for qtiCreator
        resetGui: _resetGui
    };

});

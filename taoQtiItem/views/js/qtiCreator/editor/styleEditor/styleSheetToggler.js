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
    'taoQtiItem/qtiCreator/editor/styleEditor/styleEditor',
    'i18n',
    'helpers',
    'lodash',
    'taoQtiItem/qtiCreator/model/Stylesheet',
    'tpl!taoQtiItem/qtiCreator/tpl/notifications/genericFeedbackPopup',
    'ui/resourcemgr'
], function ($, styleEditor, __, helpers, _, Stylesheet, genericFeedbackPopup) {
    'use strict';

    var $doc = $(document);

    var styleSheetToggler = (function () {

        var init = function (itemConfig) {

            var cssToggler = $('#style-sheet-toggler'),
                uploader = $('#stylesheet-uploader'),
                customCssToggler = $('[data-custom-css]'),
                getContext = function (trigger) {
                    trigger = $(trigger);
                    var li = trigger.closest('li'),
                        stylesheetObj = li.data('stylesheetObj') || new Stylesheet({href : li.data('css-res')}),
                        input = li.find('.style-sheet-label-editor'),
                        labelBox = input.prev('.file-label'),
                        label = input.val();

                    return {
                        li: li,
                        input: input,
                        label: label,
                        labelBox: labelBox,
                        isCustomCss: !!li.data('custom-css'),
                        isDisabled: li.find('.icon-preview').hasClass('disabled'),
                        stylesheetObj: stylesheetObj,
                        cssUri: stylesheetObj.attr('href')
                    };
                };



            /**
             * Upload custom stylesheets
             */
            uploader.on('click', function () {

                uploader.resourcemgr({
                    appendContainer: '#mediaManager',
                    path: '/',
                    root: 'local',
                    browseUrl: helpers._url('files', 'ItemContent', 'taoItems'),
                    uploadUrl: helpers._url('upload', 'ItemContent', 'taoItems'),
                    deleteUrl: helpers._url('delete', 'ItemContent', 'taoItems'),
                    downloadUrl: helpers._url('download', 'ItemContent', 'taoItems'),
                    fileExistsUrl : helpers._url('fileExists', 'ItemContent', 'taoItems'),
                    params: {
                        uri: itemConfig.uri,
                        lang: itemConfig.lang,
                        filters: 'text/css'
                    },
                    pathParam: 'path',
                    select: function (e, files) {
                        var i, l = files.length;
                        for (i = 0; i < l; i++) {
                            styleEditor.addStylesheet(files[i].file);
                        }
                    }
                });
            });


            /**
             * Confirm to save the item
             */
            var deleteStylesheet = function(trigger) {
                var context = getContext(trigger),
                    attr = context.isDisabled ? 'disabled-href' : 'href',
                    cssLinks = $('head link');


                styleEditor.getItem().removeStyleSheet(context.stylesheetObj);

                cssLinks.filter('[' + attr + '*="' + context.cssUri + '"]').remove();
                context.li.remove();

                $('.feedback-info').hide();
                _createInfoBox({
                    message: __('Style Sheet <b>%s</b> removed<br> Click <i>Add Style Sheet</i> to re-apply.').replace('%s', context.label),
                    type: 'info'
                });

                $doc.trigger('customcssloaded.styleeditor', [styleEditor.getStyle()]);
            };


            /**
             * Modify stylesheet title (enable)
             */
            var initLabelEditor = function (trigger) {
                var context = getContext(trigger);
                context.labelBox.hide();
                context.input.show();
            };

            /**
             * Download current stylesheet
             *
             * @param trigger
             */
            var downloadStylesheet = function(trigger) {
                styleEditor.download(getContext(trigger).cssUri);
            };

            /**
             * Modify stylesheet title (save modification)
             */
            var saveLabel = function (trigger) {
                var context = getContext(trigger),
                    title = $.trim(context.input.val());

                if (!title) {
                    context.stylesheetObj.attr('title', '');
                    return false;
                }

                context.stylesheetObj.attr('title', title);
                context.input.hide();
                context.labelBox.html(title).show();
            };

            /**
             * Dis/enable style sheets
             */
            var handleAvailability = function (trigger) {
                var context = getContext(trigger),
                    link,
                    attrTo = 'disabled-href',
                    attrFrom = 'href';

                // custom styles are handled in a style element, not in a link
                if (context.isCustomCss) {
                    if (context.isDisabled) {
                        styleEditor.create();
                        customCssToggler.removeClass('not-available');
                    }
                    else {
                        styleEditor.erase();
                        customCssToggler.addClass('not-available');
                    }
                }
                // all other styles are handled via their link element
                else {
                    if (context.isDisabled) {
                        attrTo = 'href';
                        attrFrom = 'disabled-href';
                    }

                    link = $('link[' + attrFrom + '$="' + context.cssUri + '"]');
                    link.attr(attrTo, link.attr(attrFrom)).removeAttr(attrFrom);
                }

                // add some visual feed back to the triggers
                $(trigger).toggleClass('disabled');
            };

            /**
             * Distribute click events
             */
            cssToggler.on('click', function (e) {
                var target = e.target,
                    className = target.className;

                // distribute click actions
                if (className.indexOf('icon-bin') > -1) {
                    deleteStylesheet(e.target);
                }
                else if (className.indexOf('file-label') > -1) {
                    initLabelEditor(e.target);
                }
                else if (className.indexOf('icon-preview') > -1) {
                    handleAvailability(e.target);
                }
                else if(className.indexOf('icon-download') > -1) {
                    downloadStylesheet(e.target);
                }
            });


            /**
             * Handle renaming on enter
             */
            cssToggler.on('keydown', 'input', function (e) {
                if (e.keyCode === 13) {
                    $(e.target).trigger('blur');
                }
            });

            /**
             * Handle renaming on blur
             */
            cssToggler.on('blur', 'input', function (e) {
                saveLabel(e.target);
            });


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

            setTimeout(function() {
                closeTrigger.trigger('click');
            }, 4523);

            return $messageBox;
        };

        return {
            init: init
        };

    })();

    return styleSheetToggler;
});


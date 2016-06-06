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
    'jquery',
    'lodash',
    'helpers',
    'i18n',
    'urlParser',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/cssToggler',
    'jquery.fileDownload'
], function (
    $,
    _,
    helpers,
    __,
    UrlParser,
    cssTpl
    ) {
    'use strict';

    var itemConfig;

    /**
     * generate Ajax URI
     * @param action
     * @returns {*}
     */
    var _getUri = function(action) {
        return helpers._url(action, 'QtiCssAuthoring', 'taoQtiItem');
    };

    /**
     * Extract the file name from a path
     * @param path
     * @returns {*}
     * @private
     */
    var _basename = function(path) {
        return path.substring(path.lastIndexOf('/') + 1);
    };

    /**
     * Manage style rules as CSS rather than style attributes.
     * Must be used by all widgets that change the style of an item.
     */
    var styleEditor = (function ($, doc) {

        // stylesheet as object
        var style = {},
            // DOM element to hold the style
            $styleElem = (function () {
                var styleElem = $('#item-editor-user-styles');
                if(!styleElem.length) {
                    styleElem = $('<style>', { id : 'item-editor-user-styles' } );
                    $('head').append(styleElem);
                }
                else {
                    styleElem.empty();
                }
                return styleElem;
            }()),
            currentItem,
            common = {
                title: __('Disable this stylesheet temporarily'),
                deleteTxt: __('Remove this stylesheet'),
                editLabelTxt: __('Edit stylesheet label'),
                downloadTxt: __('Download this stylesheet'),
                preparingMessageHtml: __('Preparing CSS, please wait…'),
                failMessageHtml: __('There was a problem downloading your CSS, please try again.'),
                isInValidLocalTxt: __('This stylesheet has not been found on the server. you may want to delete this reference')
            },
            customStylesheet = '';


        /**
         * Create CSS and add it to DOM
         *
         * @param dontAppend whether or not to append the stylesheet to the DOM. This is used by the iframe preview
         */
        var create = function(dontAppend) {

            var key1, // first level key, could be selector or media query
                key2, // second level key, could be css property or selector
                mSelector, // selector inside a media query
                mProp, // property inside a media query
                css = '';

            if(_.isEmpty(style)){
                return erase();
            }

            // rebuild CSS
            for (key1 in style) {
                if (!style.hasOwnProperty(key1)) {
                    continue;
                }

                css += key1 + '{';
                for (key2 in style[key1]) {
                    if (!style[key1].hasOwnProperty(key2)) {
                        continue;
                    }
                    // in the case of a surrounding media query
                    if (_.isPlainObject(style[key1][key2])) {
                        for (mSelector in style[key1][key2]) {
                            css += key2 + '{';
                            for (mProp in style[key1][key2]) {
                                css += mProp + ':' + style[key1][key2][mSelector] + ';';
                            }
                            css += '}';
                        }
                    }
                    // regular selectors
                    else {
                        css += key2 + ':' + style[key1][key2] + ';';
                    }
                }
                css += '}\n';
            }

            if(!dontAppend) {
                $styleElem.text(css);
            }
            return css;

        };

        /**
         * Apply rule to CSS
         *
         * @param {{string}} selector
         * @param {{string}} property
         * @param {{string}} value
         */
        var apply = function (selector, property, value) {
            style[selector] = style[selector] || {};

            // delete this rule
            if (!value) {
                delete(style[selector][property]);
                if(_.size(style[selector]) === 0) {
                    delete(style[selector]);
                }
            }
            // add this rule
            else {
                style[selector][property] = value;
            }

            // apply rule
            create();

            /**
             * Fires a change notification on the item style
             * @event taoQtiItem/qtiCreator/editor/styleEditor/styleEditor#stylechange.qti-creator
             * @property {Object} [detail] An object providing some additional detail on the event
             * @property {Boolean} [detail.initializing] Tells if the stylechange occurs at init time
             */
            $(document).trigger('stylechange.qti-creator');
        };


        /**
         * Delete all custom styles
         */
        var erase = function() {
            style = {};
            $styleElem.text('');
            return false;
        };

        /**
         * Save the resulting CSS to a file
         */
        var save = function () {
            verifyInit();
            return $.post(_getUri('save'), _.extend({}, itemConfig,
                {
                    cssJson: JSON.stringify(style),
                    stylesheetUri: customStylesheet.attr('href')
                }
            ));
        };


        /**
         * Download CSS as file
         */
        var download = function(uri) {
            verifyInit();
            $.fileDownload(_getUri('download'), {
                preparingMessageHtml: common.preparingMessageHtml,
                failMessageHtml: common.failMessageHtml,
                successCallback: function () { },
                httpMethod: 'POST',
                data: _.extend({}, itemConfig, { stylesheetUri: uri })
            });
        };


        /**
         * Has the class been initialized
         *
         * @returns {boolean}
         */
        var verifyInit = function() {
            if(!itemConfig) {
                throw new Error('Missing itemConfig, did you call styleEditor.init()?');
            }
            return true;
        };

        /**
         * Add a single stylesheet, the custom stylesheet will be loaded as object
         *
         * @param stylesheet
         * @returns {*} promise
         */
        var addStylesheet = function(stylesheet) {

            var fileName,
                link,
                stylesheets = [],
                listEntry,
                parser,
                loadStylesheet = function(link, stylesheet, isLocal, isValid) {

                    // in the given scenario we cannot test whether a remote stylesheet really exists
                    // this would require to pipe all remote css via php curl
                    var isInvalidLocal = isLocal && !isValid,
                        tplData = {
                            path: stylesheet.attr('href'),
                            label: (stylesheet.attr('title') || fileName),
                            title: common.title,
                            deleteTxt: common.deleteTxt,
                            downloadTxt: common.downloadTxt,
                            editLabelTxt: isInvalidLocal ? common.isInValidLocalTxt : common.editLabelTxt
                        };

                    stylesheets.push(tplData);

                    // create list entry
                    listEntry = $(cssTpl({ stylesheets: stylesheets }));

                    listEntry.data('stylesheetObj', stylesheet);

                    // initialize download button
                    $('#style-sheet-toggler').append(listEntry);

                    if(isInvalidLocal) {
                        listEntry.addClass('not-available');
                        listEntry.find('[data-role="css-download"], .style-sheet-toggler').css('visibility', 'hidden');
                        return;
                    }

                    $styleElem.before(link);

                    // time difference between loading the css file and applying the styles
                    setTimeout(function() {
                        var isInit = false;

                        $(doc).trigger('customcssloaded.styleeditor', [style]);
                        $(window).trigger('resize');
                        if (currentItem.pendingStylesheetsInit) {
                            isInit = true;
                            currentItem.pendingStylesheetsInit --;
                        }

                        /**
                         * Fires a change notification on the item style
                         * @event taoQtiItem/qtiCreator/editor/styleEditor/styleEditor#stylechange.qti-creator
                         * @property {Object} [detail] An object providing some additional detail on the event
                         * @property {Boolean} [detail.initializing] Tells if the stylechange occurs at init time
                         */
                        $(doc).trigger('stylechange.qti-creator', [{initializing: isInit}]);
                    }, isLocal ? 500 : 3500);

                };

            // argument is uri
            if(_.isString(stylesheet)) {
                stylesheet = currentItem.createStyleSheet(stylesheet);
            }


            fileName = _basename(stylesheet.attr('href'));
            // link with cache buster
            link = (function() {
                var _link = $(stylesheet.render()),
                    _href = _link.attr('href'),
                    _sep  = _href.indexOf('?') > -1 ? '&' : '?';
                _link.attr('href', _href + _sep + (new Date().getTime()).toString());
                return _link;
            }());

            // cache css before applying allows for a pretty good guess
            // when the stylesheet is loaded and the buttons in the style editor
            // can be changed
            parser = new UrlParser(link.attr('href'));
            if (parser.checkCORS()) {
            $.when($.ajax(link.attr('href'))).then(function() {
                    loadStylesheet(link, stylesheet, true, true);
                },
                    // add file to list even on failure to be able to remove it from the item
                    function() {
                        loadStylesheet(link, stylesheet, true, false);
            });
            }
            // otherwise load it with a big timeout and hope for the best
            // unfortunately this dirty way is the only possibility in cross domain environments
            else {
                loadStylesheet(link, stylesheet, false);
            }

        };


        /**
         * Add style sheets to toggler
         * @param item
         */
        var addItemStylesheets = function() {

            var currentStylesheet;
            currentItem.pendingStylesheetsInit = _.size(currentItem.stylesheets);

            for(var key in currentItem.stylesheets) {
                if(!currentItem.stylesheets.hasOwnProperty(key)) {
                    continue;
                }

                currentStylesheet = currentItem.stylesheets[key];

                if('tao-user-styles.css' === _basename(currentStylesheet.attr('href'))) {
                    customStylesheet = currentStylesheet;
                    continue;
                }

                // add those that are loaded synchronously
                addStylesheet(currentItem.stylesheets[key]);
            }

            // if no custom css had been found, add empty stylesheet anyway
            if(!customStylesheet) {
                customStylesheet = currentItem.createStyleSheet('style/custom/tao-user-styles.css');
            }
        };

        /**
         * Remove orphaned stylesheets. These would be present if previously another item has been edited
         */
        var removeOrphanedStylesheets = function() {
            $('link[data-serial]').remove();
        };

        /**
         * retrieve the current item
         *
         * @returns {*}
         */
        var getItem = function() {
            return currentItem;
        };


        /**
         * Initialize class
         * @param config
         */
        var init = function(item, config) {

            // promise
            currentItem = item;

            //prepare config object (don't pass all of them, otherwise, $.param will break)
            itemConfig = {
                uri : config.uri,
                lang : config.lang,
                baseUrl : config.baseUrl
            };

            removeOrphanedStylesheets();

            // this creates at the same time customStylesheet in case it doesn't exist yet
            addItemStylesheets();

            var resizerTarget = $('#item-editor-item-resizer').data('target'),
                href = customStylesheet.attr('href');

            currentItem.data('responsive', true);

            $.when(
                $.getJSON (
                    _getUri('load'),
                    _.extend({}, itemConfig, { stylesheetUri: href })
                )
            ).then(function(_style) {
                // copy style to global style
                style = _style;

                // apply rules
                create();

                // reset meta in case the width is set in the custom stylesheet
                if(style.length){
                    currentItem.data('responsive', !!(style[resizerTarget] && style[resizerTarget].width));
                }

                // inform editors about custom sheet
                $(doc).trigger('customcssloaded.styleeditor', [style]);

            });

        };

        var getStyle = function() {
            return style;
        };

        // expose public functions
        return {
            apply: apply,
            save: save,
            download: download,
            erase: erase,
            init: init,
            create: create,
            getItem: getItem,
            getStyle: getStyle,
            addStylesheet: addStylesheet
        };
    }($, document));

    return styleEditor;
});

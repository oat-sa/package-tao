/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'core/mimetype',
    'core/pluginifier',
    'iframeNotifier',
    'mediaElement'
],
function($, _, __, mimeType, Pluginifier, iframeNotifier) {
    'use strict';

    var ns = 'previewer';
    var dataNs = 'ui.' + ns;

    //the plugin defaults
    var defaults = {
        containerClass: 'previewer'
    };

    var previewGenerator = {
        placeHolder: _.template("<p class='nopreview' data-type='${type}'>${desc}</p>"),
        youtubeTemplate: _.template("<video preload='none'><source type='video/youtube' src=${jsonurl}/></video>"),
        videoTemplate: _.template("<video src=${jsonurl} type='${mime}' preload='none'></video>"),
        audioTemplate: _.template("<audio src=${jsonurl} type='${mime}'></audio>"),
        imageTemplate: _.template("<img src=${jsonurl} alt='${name}' />"),
        pdfTemplate: _.template("<object data=${jsonurl} type='application/pdf'><a href=${jsonurl} target='_blank'>${name}</a></object>"),
        flashTemplate: _.template("<object data=${jsonurl} type='application/x-shockwave-flash'><param name='movie' value=${jsonurl}></param></object>"),
        mathmlTemplate: _.template("<iframe src=${jsonurl}></iframe>"),
        xmlTemplate: _.template("<pre>${xml}</pre>"),
        htmlTemplate: _.template("<iframe src=${jsonurl}></iframe>"),
        /**
         * Generates the preview tags for a type
         * @memberOf previewGenerator
         * @param {String} type - the file type
         * @param {Object} data - the preview data (url, desc, name)
         * @returns {String} the tags
         */
        generate: function generate(type, data) {
            var tmpl = this[type + 'Template'];
            data.jsonurl = JSON.stringify(data.url);
            if (_.isFunction(tmpl)) {
                return tmpl(data);
            }
        }
    };

    /**
     * @exports ui/previewer
     */
    var previewer = {
        /**
         * Initialize the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').previewer({ url : 'test.mp4', type : 'video/mp4' });
         * @public
         *
         * @constructor
         * @param {Object} [options] - the plugin options
         * @returns {jQueryElement} for chaining
         */
        init: function(options) {
            var self = previewer;


            //get options using default
            options = _.defaults(options || {}, defaults);

            return this.each(function() {
                var $elt = $(this);
                if (!$elt.data(dataNs)) {

                    if (!$elt.hasClass(options.containerClass)) {
                        $elt.addClass(options.containerClass);
                    }

                    $elt.data(dataNs, options);
                    self._update($elt);

                    /**
                     * The plugin has been created.
                     * @event previewer#create.previewer
                     */
                    $elt.trigger('create.' + ns);
                } else {
                    $elt.previewer('update', options);
                }
            });
        },
        /**
         * Update the preview
         * @example $('selector').previewer('update', {url: 'foo.mp3', type : 'audio/mp3'});
         * @public
         * @param {Object} data - the new options for the preview
         * @returns {jQueryElement} for chaining
         */
        update: function(data) {
            return this.each(function() {
                var $elt = $(this);
                var options = $elt.data(dataNs);
                $elt.data(dataNs, _.merge(options, data));
                previewer._update($elt);
            });
        },
        /**
         * Update the preview
         * @private
         * @param {jQueryElement} $elt - the current element
         */
        _update: function($elt) {
            var self = previewer;

            if (self.meSkipUpdate === true) {
                self.meSkipUpdate = false;
                return;
            }


            var $content, mep;
            var options = $elt.data(dataNs);
            if (options) {
                var type = options.type || mimeType.getFileType({mime: options.mime, name: options.url});
                var content;
                if (options.url) {

                    if (!options.name) {
                        options.name = options.url.substring(options.url.lastIndexOf("/") + 1, options.url.lastIndexOf("."));
                    }
                    content = previewGenerator.generate(type, options);
                }
                if (!content) {
                    content = previewGenerator.placeHolder(_.merge({desc: __('No preview available')}, options));
                }
                $content = $(content);

                $content.on('load', function() {
                    iframeNotifier.parent('itemcontentchange');
                });

                if (options.width) {
                    $content.attr('width', options.width);
                }
                if (options.height) {
                    $content.attr('height', options.height);
                }

                $elt.empty().html($content);
                if (type === 'audio' || type === 'video') {
                    if (options.url) {
                        $content.mediaelementplayer({
                            pauseOtherPlayers: false,
                            audioWidth: options.width || 290,
                            audioHeight: options.height || 50,
                            videoWidth: options.width || 290,
                            videoHeight: options.height || 300,
                            success: function(me, medom) {
                                me.load();

                                //TODO all this code works only in the resource manager and may have impact on players elsewhere...

                                // stop video and free the socket on escape keypress(modal window hides)
                                $('body').off('keydown.mediaelement');
                                $('body').on('keydown.mediaelement', function(event) {
                                    if (event.keyCode === 27 && self.oldMediaElement !== undefined) {
                                        self.oldMediaElement.setSrc('');
                                    }
                                });

                                // stop the video and free the socket on file select from the action icons
                                $('#mediaManager .actions a:nth-child(1)').off('mousedown.mediaelement');
                                $('#mediaManager .actions a:nth-child(1)').on('mousedown.mediaelement', function(event) {
                                    self.meSkipUpdate = true;
                                    self.oldMediaElement.setSrc('');
                                });

                                // stop video, free the socket and remove player interface on video deletion
                                $('#mediaManager .actions a:nth-child(3)').off('mousedown.mediaelementdel');
                                $('#mediaManager .actions a:nth-child(3)').on('mousedown.mediaelementdel', function() {
                                    if (self.oldMediaElement !== undefined) {
                                        self.oldMediaElement.setSrc('');
                                        self.meSkipUpdate = true;
                                        if (self.oldMediaElementDom !== undefined) {
                                            $(self.oldMediaElementDom).closest('.mejs-container').remove();
                                        }
                                    }
                                });

                                // stop video and free the socket on all other cases when video is selected or temporary hidden or modal window is closed
                                var meSelector = '#mediaManager .icon-close, #mediaManager .upload-switcher, #mediaManager .select-action, #mediaManager .files li>span';
                                $(meSelector).off('mousedown.mediaelement');
                                $(meSelector).on('mousedown.mediaelement', function(event) {
                                    event.stopPropagation();

                                    // when we switch between list and upload views, we want to keep the player interface, so we use dontDestroy to indicate that
                                    var dontDestroy = false;
                                    if ($(event.target).children().first().hasClass('icon-undo')) {
                                        self.oldMediaElement.setSrc(self.oldMediaElementSrc);
                                        self.oldMediaElement.load();
                                        self.oldMediaElement.play();
                                        self.oldMediaElement.pause();
                                        dontDestroy = true;
                                    }

                                    if (self.oldMediaElement !== undefined && dontDestroy === false) {
                                        self.oldMediaElement.setSrc('');
                                    }
                                });
                                self.oldMediaElement = me;
                                self.oldMediaElementSrc = me.src;
                                self.oldMediaElementDom = medom;
                            }
                        });
                    }
                }

                /**
                 * The plugin has been created.
                 * @event previewer#update.previewer
                 */
                $elt.trigger('update.' + ns);
            }
        },
        oldMediaElement: undefined,
        oldMediaElementSrc: undefined,
        oldMediaElementDom: undefined,
        meSkipUpdate: false,
        /**
         * Destroy completely the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').previewer('destroy');
         * @public
         */
        destroy: function() {
            this.each(function() {
                var $elt = $(this);
                var options = $elt.data(dataNs);

                /**
                 * The plugin has been destroyed.
                 * @event previewer#destroy.previewer
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
    };

    //Register the incrementer to behave as a jQuery plugin.
    Pluginifier.register(ns, previewer);

    /**
     * The only exposed function is used to start listening on data-attr
     *
     * @public
     * @example define(['ui/previewer'], function(previewer){ previewer($('rootContainer')); });
     * @param {jQueryElement} $container - the root context to listen in
     */
    return function listenDataAttr($container) {

        $container.find('[data-preview]').each(function() {
            var $elt = $(this);
            $elt.previewer({
                url: $elt.data('preview'),
                type: $elt.data('preview-type'),
                mime: $elt.data('preview-mime'),
                width: $elt.width(),
                height: $elt.height()
            });
        });
    };
});


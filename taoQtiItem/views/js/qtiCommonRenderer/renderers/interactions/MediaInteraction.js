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
 * @author Martin for OAT S.A. <code@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/mediaInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'mediaElement'
], function($, _, __, tpl, pciResponse, containerHelper, MediaElementPlayer) {
    'use strict';

    /**
     * Get the media type (audio, video or video/youtube) regarding it's mime type.
     * @param {String} mimetype - the mime type
     * @returns {String} the catrgory/type
     */
    var getMediaType = function(mimetype) {
        var type = '';
        if (mimetype !== '') {
            if (mimetype.indexOf('youtube') !== -1) {
                type = 'video/youtube';
            } else if (mimetype.indexOf('video') === 0 || mimetype.indexOf('application/ogg')) {
                type = 'video';
            } else if (mimetype.indexOf('audio') === 0) {
                type = 'audio';
            }
        }
        return type;
    };

    /**
     * Resize video player elements to fit container size
     * @param {Object} mediaElement - player instance
     * @param {jQueryElement} $container   - container element to adapt
     */
    var resizeVideo = function (mediaElement, $container) {
        var height = $container.find('.media-container').height(),
            width =  $container.find('.media-container').width();
        mediaElement.player.setPlayerSize(width, height);
        mediaElement.player.media.setVideoSize(width, height);
    };

    //some default values
    var defaults = {
        type   : 'video/mp4',
        video : {
            width: 480,
            height: 270
        },
        audio : {
            width: 400,
            height: 30
        }
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10391
     *
     * @param {object} interaction
     * @fires playerready when the player is sucessfully loaded and configured
     */
    var render = function render(interaction) {
        var self = this;
        var $meTag,
            mediaOptions;
        var $container          = containerHelper.get(interaction);
        var $item               = $container.parents('.qti-item');
        var media               = interaction.object;
        var mediaType           = getMediaType(media.attr('type') || defaults.type);
        var enablePause         = $container.hasClass('pause');
        var maxPlays            = parseInt(interaction.attr('maxPlays'), 10) || 0;
        var url                 = media.attr('data') ? self.resolveUrl(media.attr('data')) : '';

        var pauseFromClick      = false;

        var features = enablePause ? ['playpause', 'progress', 'current', 'duration', 'volume'] : ['playpause', 'current', 'duration', 'volume'];

        //intialize the player if not yet done
        var initMediaPlayer = function initMediaPlayer(){
            if(!interaction.mediaElement && $meTag && $meTag.length){
                new MediaElementPlayer($meTag, mediaOptions);
            }
        };

        //check if the media can be played (using timesPlayed and maxPlays)
        var canBePlayed = function canBePlayed(){
            var current = parseInt($container.data('timesPlayed'), 10);
            return maxPlays === 0 || maxPlays > current;
        };

        if(_.size(media.attributes) === 0){
            //TODO move to afterCreate
            media.attr('type', defaults.type);
            media.attr('width', $container.innerWidth());
            media.attr('height', defaults.video.height);
            media.attr('data', '');
        }

        //set up player options
        mediaOptions = {
            defaultVideoWidth       : defaults.video.width,
            defaultVideoHeight      : defaults.video.height,
            videoWidth              : media.attr('width'),
            videoHeight             : media.attr('height'),
            audioWidth              : media.attr('width') || defaults.audio.width,
            audioHeight             : media.attr('height') || defaults.audio.height,
            features                : features,
            startVolume             : 1,
            loop                    : !!interaction.attr('loop'),
            enableAutosize          : true,
            alwaysShowControls      : true,
            iPadUseNativeControls   : false,
            iPhoneUseNativeControls : false,
            AndroidUseNativeControls: false,
            alwaysShowHours         : false,
            enableKeyboard          : false,
            pauseOtherPlayers       : false,

            //the player is loaded
            success: function(mediaElement, playerDom) {

                var playFromPauseEvent = false;
                var stillNeedToCallPlay = true;
                var $meContainer    = $(playerDom).closest('.mejs-container');
                var $layers         = $('.mejs-layers', $meContainer);
                var $playpauseBtn   = $('.mejs-playpause-button', $meContainer);
                var $bigPlayBtn     = $('.mejs-overlay-play', $meContainer);
                var $controls       = $('.mejs-controls', $meContainer);
                var controlsHeight  = $controls.outerHeight();
                /** Resize video player internal timer to prevent multiply execution */
                var rTimer;

                interaction.mediaElement = mediaElement;

                //resize the iframe of the youtube plugin
                if (mediaType === 'video/youtube') {
                    _.defer(function(){
                        $('iframe.me-plugin', $container).css('min-height', media.attr('height'));
                    });
                }

                //set up the number of times played
                if (!$container.data('timesPlayed')) {
                    $container.data('timesPlayed', 0);
                }

                //controls the autoplaying
                if (interaction.attr('autostart') && canBePlayed()) {

                    if (mediaType !== 'video/youtube') {
                        mediaElement.load();
                        mediaElement.play();
                    }

                    mediaElement.addEventListener('canplay', function() {
                        if (stillNeedToCallPlay) {
                            mediaElement.play();
                        }
                    }, false);
                }


                mediaElement.addEventListener('play', function(event) {
                    stillNeedToCallPlay = false;

                    if (playFromPauseEvent === true) {

                        playFromPauseEvent = false;

                    } else {
                        //if no pause we detach the controls or add an overlay on iframes to prevent pause from click
                        if(!enablePause){
                            $bigPlayBtn.detach();
                            $playpauseBtn.detach();

                            if(mediaType === 'video/youtube' || mediaElement.pluginType === 'flash') {
                                $('<div class="overlay"></div>')
                                    .width(mediaOptions.videoWidth)
                                    .height(mediaOptions.videoHeight - controlsHeight)
                                    .appendTo($layers);
                            }
                        }
                    }
                }, false);

                mediaElement.addEventListener('ended', function(event) {
                    $container.data('timesPlayed', $container.data('timesPlayed') + 1);
                    containerHelper.triggerResponseChangeEvent(interaction);

                    if (canBePlayed() && !enablePause){

                        //re attach the controls and remove the overlay
                        $controls.prepend($playpauseBtn);
                        $layers.append($bigPlayBtn);
                        $('.overlay', $layers).remove();

                    } else if(!canBePlayed()) {

                        //we detach the controls or add an overlay on iframes to prevent a new play
                        $bigPlayBtn.detach();
                        $playpauseBtn.detach();

                        if(mediaType === 'video/youtube' || mediaElement.pluginType === 'flash') {
                            $('<div class="overlay"></div>')
                                .width(mediaOptions.videoWidth)
                                .height(mediaOptions.videoHeight - controlsHeight)
                                .appendTo($layers);
                        }

                        //no other way to stop it if in loop
                        if(!!interaction.attr('loop')){
                            if(!interaction.mediaElement.pluginApi){
                                interaction.mediaElement.setSrc('');
                            } else {
                                $(playerDom).remove();
                            }
                        }

                    }

                }, false);

                //prevents to pause the player
                $playpauseBtn.on('click.qti-element', function(e) {
                        pauseFromClick = true;
                        if(!enablePause){
                            e.preventDefault();
                            e.stopPropagation();                        }
                    });

                mediaElement.addEventListener('pause', function(event) {
                    // there is a "pause" event fired at the end of a movie and we need to differentiate it from pause event caused by a click
                    if (pauseFromClick) {
                        playFromPauseEvent = true;
                        pauseFromClick = false;
                        //mediaElement.play();
                    }
                });

                resizeVideo(mediaElement, $container);

                var delayedResize = function () {
                    clearTimeout(rTimer);
                    rTimer = setTimeout(function () {
                        resizeVideo(mediaElement, $container);
                    }, 200);
                };

                $(window).off('resize.video')
                    .on('resize.video', delayedResize);

                $item.off('resize.gridEdit')
                    .on('resize.gridEdit', delayedResize);

                /**
                 * @event playerready
                 */
                $container.trigger('playerready');


            },

            error: function(playerDom) {
                $(playerDom).closest('div.mejs-container').find('.me-cannotplay').remove();
            }
        };

        //create the HTML tags
        $meTag = $(_buildMedia(media, url, mediaType)).appendTo($('.media-container', $container));

        //prevent contextmenu and control click on the player to prevent unwanted pause.
        $meTag
            .on('contextmenu', function(e) {
                e.preventDefault();
            })
            .on('click.qit-element', function(e) {
                pauseFromClick = true;
                if(!enablePause){
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });

        //initialize the component

       $container.on('responseSet', function() {
            initMediaPlayer();
       });

        //gives a small chance to the responseSet event before initializing the player
        initMediaPlayer();
    };

    /**
     * Build the HTML5 tags for a media
     * @private
     * @param {Object} media - interaction.object
     * @param {String} url - the resolved url
     * @param {String} type  - the media type in video, audio and video/youtube
     * @returns {String} the html5 tags
     */
    var _buildMedia = function _buildMedia(media, url, type){
        var element;
        var attrs;

        //inline an object to html attributes
        var inlineAttrs = function inlineAttrs(attrs){
            return _.reduce(attrs, function(res, value, key){
                res += key + '="' + value + '" ';
                return res;
            }, '');
        };

        if(media){

            attrs = {
                width:   media.attr('width')     + 'px',
                height:  media.attr('height')    + 'px',
                preload: 'none'
            };

            if (!/^http(s)?:\/\//.test(media.attr('data'))){
                attrs.type = media.attr('type');
            }
            if (type === 'video/youtube') {
                element =   '<video ' + inlineAttrs(attrs) + '> ' +
                                ' <source type="video/youtube" src="' + url + '" /> ' +
                            '</video>';
            } else {
                attrs.src = url;
                if(!type){
                    type = 'video';
                }
                element =   '<' + type + ' ' + inlineAttrs(attrs) + '></' + type + '>';
            }
        }
        return element;
    };

    /**
     * Destroy the current interaction
     * @param {Object} interaction
     */
    var destroy = function(interaction) {
        var $container = containerHelper.get(interaction);

        if(interaction.mediaElement){
            //needed to release socket
            if(!interaction.mediaElement.pluginApi){
                interaction.mediaElement.setSrc('');
            }
            interaction.mediaElement = undefined;
        }

        $('.instruction-container', $container).empty();
        $('.media-container', $container).empty();

        $container.removeData('timesPlayed');


        $(window).off('resize.video');

        //remove all references to a cache container
        containerHelper.reset(interaction);
    };

    /**
     * Get the responses from the interaction
     * @private
     * @param {Object} interaction
     * @returns {Array} of points
     */
    var _getRawResponse = function _getRawResponse(interaction) {
        return [containerHelper.get(interaction).data('timesPlayed') || 0];
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
     * Special value: the empty object value {} resets the interaction responses
     *
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response) {
        if (response) {
            try {
                //try to unserialize the pci response
                var responseValues;
                responseValues = pciResponse.unserialize(response, interaction);
                containerHelper.get(interaction).data('timesPlayed', responseValues[0]);
            } catch (e) {
                // something went wrong
            }
        }
    };

    /**
     * Reset the current responses of the rendered interaction.
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     *
     * Special value: the empty object value {} resets the interaction responses
     *
     * @param {object} interaction
     * @param {object} response
     */
    var resetResponse = function resetResponse(interaction) {
        containerHelper.get(interaction).data('timesPlayed', 0);
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
    var getResponse = function(interaction) {
        return  pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    /**
     * Set the interaction state. It could be done anytime with any state.
     *
     * @param {Object} interaction - the interaction instance
     * @param {Object} state - the interaction state
     */
    var setState  = function setState(interaction, state){
        if(_.isObject(state)){
            if(state.response){
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
    var getState = function getState(interaction){
        var $container;
        var state =  {};
        var response =  interaction.getResponse();

        if(response){
            state.response = response;
        }
        return state;
    };


    /**
     * Expose the common renderer for the interaction
     * @exports qtiCommonRenderer/renderers/interactions/MediaInteraction
     */
    return {
        qtiClass        : 'mediaInteraction',
        template        : tpl,
        render          : render,
        getContainer    : containerHelper.get,
        setResponse     : setResponse,
        getResponse     : getResponse,
        resetResponse   : resetResponse,
        destroy         : destroy,
        setState        : setState,
        getState        : getState
    };
});

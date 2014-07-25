/**
 * @author
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/mediaInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'mediaElement'
], function($, _, __, tpl, pciResponse, Helper, MediaElementPlayer) {

    var getMediaType = function(media) {
        var type = '';
        var mimetype = media.type;
        if (mimetype !== '') {
            if (mimetype.indexOf('youtube') !== -1) {
                type = 'video/youtube';
            } else if (mimetype.indexOf('video') === 0) {
                type = 'video';
            } else if (mimetype.indexOf('audio') === 0) {
                type = 'audio';
            }
        }
        return type;
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10391
     * 
     * @param {object} interaction
     */
    var render = function render(interaction) {
        var $container = Helper.getContainer(interaction);
        
        var mediaInteractionObjectToReturn;
        var isCreator = !!$container.data('creator');
        if (isCreator) {
            //use this defaults when creating new empty item in the creator
            var mediaDefaults = {
                data: '',
                type: 'video/mp4',
                width: 480,
                height: 270
            };
            _.defaults(interaction.object.attributes, mediaDefaults);
        }

        var media = interaction.object.attributes;
        var mimeType = media.type;
        var baseUrl = this.getOption('baseUrl') || '';
        var mediaType = getMediaType(media);
        var playFromPauseEvent = false;
        var pauseFromClick = false;

        var theFeatures = [];
        if (isCreator) {
            theFeatures = ['playpause', 'progress', 'current', 'duration', 'tracks', 'volume', 'fullscreen'];
        } else {
            if (mediaType === 'audio') {
                theFeatures = ['playpause', 'current', 'duration', 'volume'];
            } else {
                theFeatures = ['current', 'duration', 'volume'];
            }
        }

        var mediaOptions = {
            defaultVideoWidth: 480,
            defaultVideoHeight: 270,
            videoWidth: media.width,
            videoHeight: media.height,
            audioWidth: media.width ? media.width : 400,
            audioHeight: 30,
            features: theFeatures,
            startVolume: 1,
            loop: interaction.attributes.loop ? interaction.attributes.loop : false,
            enableAutosize: true,
            alwaysShowControls: true,
            iPadUseNativeControls: false,
            iPhoneUseNativeControls: false,
            AndroidUseNativeControls: false,
            alwaysShowHours: false,
            enableKeyboard: false,
            pauseOtherPlayers: false,
            success: function(mediaElement, playerDom) {
                
                mediaInteractionObjectToReturn = mediaElement;
                var audioPlayPauseControls = $(playerDom).closest('div.mejs-container').find('.mejs-playpause-button');

                $(audioPlayPauseControls).on('click', function(event) {
                    if (!isCreator)  {
                        pauseFromClick = true;
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    }
                });

                var bigPlayButtonLayerDetached = null;
                var flashOverlayDiv = null;

                if ($container.data('timesPlayed') === undefined) {
                    $container.data('timesPlayed', 0);
                }

                var stillNeedToCallPlay = true;

                if (interaction.attributes.autostart && ((interaction.attributes.maxPlays === 0) || $container.data('timesPlayed') < interaction.attributes.maxPlays)) {

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


                mediaElement.addEventListener('ended', function(event) {
                    $container.data('timesPlayed', $container.data('timesPlayed') + 1);
                    Helper.triggerResponseChangeEvent(interaction);
                    if ((interaction.attr('maxPlays') === 0) || $container.data('timesPlayed') < interaction.attr('maxPlays')) {
                        if (mediaType == 'audio') {
                            //
                        } else if (mediaType === 'video' && mediaElement.pluginType !== 'flash') {
                            if (!isCreator) {
                                var PlayButtonPlaceholder = $(playerDom).closest('div.mejs-container').find('.mejs-layers');
                                PlayButtonPlaceholder.append(bigPlayButtonLayerDetached);
                            }

                        } else if (mediaType === 'video/youtube' || mediaElement.pluginType === 'flash') {
                            if (!isCreator) {
                                flashOverlayDiv.remove();
                            }
                        }
                    }
                }, false);


                mediaElement.addEventListener('play', function(event) {
                    stillNeedToCallPlay = false;
                    if (playFromPauseEvent === true) {
                        playFromPauseEvent = false;
                    } else {
                        if ((interaction.attributes.maxPlays !== 0) && $container.data('timesPlayed') >= interaction.attributes.maxPlays) {
                            if (!isCreator) {
                                mediaElement.pause();
                                mediaElement.setSrc('');
                                if (mediaType === "video/youtube") {
                                    $(playerDom).empty();
                                }
                            }
                        } else {
                            if (mediaType === 'audio') {
                                //
                            } else if (mediaType === 'video' && mediaElement.pluginType !== 'flash') {
                                if (!isCreator) {
                                    bigPlayButtonLayerDetached = $(playerDom).closest('div.mejs-container').find('.mejs-overlay-play').detach();
                                }
                            } else if (mediaType === 'video/youtube' || mediaElement.pluginType === 'flash') {
                                if (!isCreator) {
                                    var controlsHeight = $(playerDom).closest('div.mejs-container').find('div.mejs-controls').outerHeight();
                                    $(playerDom).closest('div.mejs-container').find('.mejs-layers').append('<div class="flashOverlayDiv" style="background:#000; width: ' + mediaOptions.videoWidth + 'px; height: ' + (mediaOptions.videoHeight - controlsHeight) + 'px; z-iindex: 99; position:relative;"></div>');
                                    flashOverlayDiv = $(playerDom).closest('div.mejs-container').find('.mejs-layers').find('.flashOverlayDiv');
                                    flashOverlayDiv.css({'opacity': 0}); // need to have the background set to something and then set it to transparent with jquery because of... IE8 of course :)
                                }
                            }
                        }
                    }
                }, false);

                mediaElement.addEventListener('pause', function(event) {
                    // there is a "pause" event fired at the end of a movie and we need to differentiate it from pause event caused by a click
                    if (!isCreator) {
                        if (pauseFromClick) {
                            playFromPauseEvent = true;
                            pauseFromClick = false;
                            mediaElement.play();
                        }
                    }
                });
            },
            error: function(playerDom) {
                $(playerDom).closest('div.mejs-container').find('.me-cannotplay').remove();
            }
        };


        var meHtmlContainer = $container.find('.media-container');

        if (mediaOptions.videoWidth === undefined) {
            mediaOptions.videoWidth = mediaOptions.defaultVideoWidth;
        }
        if (mediaOptions.videoHeight === undefined) {
            mediaOptions.videoHeight = mediaOptions.defaultVideoHeight;
        }

        var mediaFullUrl = media.data;
        var mediaIsServedByTAOsPHP = false;

        if (mediaType === 'video' || mediaType === 'audio') {
            mediaFullUrl = media.data.replace(/^\//, '');
            if(!/^http(s)?:\/\//.test(mediaFullUrl)){
                mediaFullUrl = baseUrl + mediaFullUrl;
                mediaIsServedByTAOsPHP = true;
            }
        }

        var meTagTypeAddition = mediaIsServedByTAOsPHP ? ' type="' + mimeType + '" ' : ' ';

        var $meTag;
        if (mediaType === 'video') {
            $meTag = $('<video src="' + mediaFullUrl + '" width="' + mediaOptions.videoWidth + 'px" height="' + mediaOptions.videoHeight + 'px" ' + meTagTypeAddition + ' preload="none"></video>').appendTo(meHtmlContainer);
        } else if (mediaType === 'video/youtube') {
            $meTag = $('<video width="' + mediaOptions.videoWidth + 'px" height="' + mediaOptions.videoHeight + 'px" preload="none"> ' +
                    ' <source type="video/youtube" src="' + mediaFullUrl + '" /> ' +
                    '</video>').appendTo(meHtmlContainer);
        } else if (mediaType === 'audio') {
            $meTag = $('<audio src="' + mediaFullUrl + '" width="' + mediaOptions.audioWidth + 'px" ' + meTagTypeAddition + ' preload="none"></audio>').appendTo(meHtmlContainer);
        }

        if (!isCreator) {
            $meTag.on('contextmenu', function(event) {
                event.preventDefault();
            }).on('click', function(event) {
                pauseFromClick = true;
                event.preventDefault();
                event.stopPropagation();
                return false;
            });
        }

        _.defer(function(){ 
            new MediaElementPlayer($meTag, mediaOptions);
        });
        //TODO need to enable it oonly once the responseSet has been called, but the current structure doesn't allow it. 
       //$container.on('responseSet', function(e, interaction, response) {
                //new MediaElementPlayer($meTag, mediaOptions);
            //});
        return mediaInteractionObjectToReturn;
    };

    var _destroy = function(interaction) {
        var $container = Helper.getContainer(interaction);
        $('.instruction-container', $container).empty();
        $('.media-container', $container).empty();
        $container.removeData('timesPlayed');
    };

    /**
     * Get the responses from the interaction
     * @private 
     * @param {Object} interaction
     * @returns {Array} of points
     */
    var _getRawResponse = function _getRawResponse(interaction) {
        return [Helper.getContainer(interaction).data('timesPlayed')];
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
                Helper.getContainer(interaction).data('timesPlayed', responseValues[0]);
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
        Helper.getContainer(interaction).data('timesPlayed', 0);
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
     * Expose the common renderer for the interaction
     * @exports qtiCommonRenderer/renderers/interactions/mediaInteraction
     */
    return {
        qtiClass: 'mediaInteraction',
        template: tpl,
        render: render,
        getContainer: Helper.getContainer,
        setResponse: setResponse,
        getResponse: getResponse,
        resetResponse: resetResponse,
        destroy: _destroy
    };
});

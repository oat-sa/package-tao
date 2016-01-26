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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/object',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline',
    'lodash',
    'util/image',
    'helpers',
    'ui/resourcemgr',
    'nouislider'
], function(stateFactory, Active, formTpl, formElement, inlineHelper, _, imageUtil, helpers){
    'use strict';

    var ObjectStateActive = stateFactory.extend(Active, function(){

        this.widget.changeState('sleep');

    }, function(){

        this.widget.$form.empty();
    });

    var _containClass = function(allClassStr, className){
        var regex = new RegExp('(?:^|\\s)' + className + '(?:\\s|$)', '');
        return allClassStr && regex.test(allClassStr);
    };

    /**
     * Greatly throttled callback function
     *
     * @param {jQuery} $media
     * @param {string} propertyName
     * @returns {function}
     */
    var _getImgSizeChangeCallback = function($media, propertyName){

        var _setAttr = _.debounce(function(media, value, name){
            media.attr(name, value);
        }, 1000);

        return _.throttle(function(media, value, name){

            if(value){
                $media[propertyName](value);
                _setAttr(media, value, name);
            }else{
                $media[propertyName]('auto');
                media.removeAttr(propertyName);
            }
            $media.trigger('contentChange.qti-widget');

        }, 100);

    };

    ObjectStateActive.prototype.initForm = function(){

        var _widget = this.widget,
            $media = _widget.$original,
            $form = _widget.$form,
            media = _widget.element,
            baseUrl = _widget.options.baseUrl,
            responsive = true;

        $form.html(formTpl({
            baseUrl : baseUrl || '',
            data : media.attr('data'),
            height : media.attr('height'),
            width : media.attr('width'),
            responsive : responsive
        }));

        //init slider and set align value before ...
        _initAdvanced(_widget);
        _initSlider(_widget);
        _initAlign(_widget);
        _initUpload(_widget);

        //... init standard ui widget
        formElement.initWidget($form);

        //init data change callbacks
        formElement.setChangeCallbacks($form, media, {
            data : function(media, value){

                media.attr('data', value);

                if(!value.match(/^http/i)){
                    value = baseUrl + '/' + value;
                }
                $media.attr('data', value);

                inlineHelper.togglePlaceholder(_widget);
                _initSlider(_widget);
                _initAdvanced(_widget);
            },
            alt : formElement.getAttributeChangeCallback(),
            longdesc : formElement.getAttributeChangeCallback(),
            align : function(media, value){
                inlineHelper.positionFloat(_widget, value);
            },
            height : _getImgSizeChangeCallback($media, 'height'),
            width : _getImgSizeChangeCallback($media, 'width')
        });
    };

    var _initAlign = function(widget){

        var align = 'default';

        //init float positioning:
        if(widget.element.hasClass('rgt')){
            align = 'right';
        }else if(widget.element.hasClass('lft')){
            align = 'left';
        }

        inlineHelper.positionFloat(widget, align);
        widget.$form.find('select[name=align]').val(align);
    };

    var _initSlider = function(widget){

        var $container = widget.$container,
            $form = widget.$form,
            $slider = $form.find('.media-resizer-slider'),
            media = widget.element,
            $media = $container.find('media'),
            $height = $form.find('[name=height]'),
            $width = $form.find('[name=width]'),
            original = {
            h : media.attr('height') || $media.height(),
            w : media.attr('width') || $media.width()
        };

        $slider.noUiSlider({
            range : {
                min : 10,
                max : 200
            },
            start : 100
        }, $slider.hasClass('noUi-target'));

        $slider.off('slide').on('slide', _.throttle(function(e, value){

            var ratio = (value / 100),
                w = parseInt(ratio * original.w),
                h = parseInt(ratio * original.h);

            $width.val(w).change();
            $height.val(h).change();
        }, 100));
    };

    var _initAdvanced = function(widget){

        var $form = widget.$form,
            data = widget.element.attr('data');

        if(data){
            $form.find('[data-role=advanced]').show();
        }else{
            $form.find('[data-role=advanced]').hide();
        }
    };


    var _initUpload = function(widget){

        var $form = widget.$form,
            options = widget.options,
            $uploadTrigger = $form.find('[data-role="upload-trigger"]'),
            $data = $form.find('input[name=data]'),
            $width = $form.find('input[name=width]'),
            $height = $form.find('input[name=height]');

        $uploadTrigger.on('click', function(){
            $uploadTrigger.resourcemgr({
                appendContainer : options.mediaManager.appendContainer,
                mediaSourcesUrl : options.mediaManager.mediaSourcesUrl,
                browseUrl : options.mediaManager.browseUrl,
                uploadUrl : options.mediaManager.uploadUrl,
                deleteUrl : options.mediaManager.deleteUrl,
                downloadUrl : options.mediaManager.downloadUrl,
                fileExistsUrl : options.mediaManager.fileExistsUrl,
                params : {
                    uri : options.uri,
                    lang : options.lang,
                    filters : 'audio/ogg,audio/mpeg,video/mpeg,video/mp4,video/ogg,video/ogv,video/webm'
                },
                pathParam : 'path',
                select : function(e, files){
                    var i, l = files.length;
                    for(i = 0; i < l; i++){
                        console.log('file',files[i].file);
                        imageUtil.getSize(options.baseUrl + files[i].file, function(size){
                            if(size && size.width >= 0){
                                $width.val(size.width).trigger('change');
                                $height.val(size.height).trigger('change');
                            }
                            $data.val(files[i].file).trigger('change');
                        });
                        break;
                    }
                }
            });
        });

    };

    return ObjectStateActive;
});

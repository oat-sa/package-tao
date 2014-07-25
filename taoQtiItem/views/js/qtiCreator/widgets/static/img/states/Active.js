define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/img',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline',
    'taoQtiItem/qtiItem/helper/util',
    'lodash',
    'util/image',
    'ui/resourcemgr',
    'nouislider'
], function($, stateFactory, Active, formTpl, formElement, inlineHelper, itemUtil, _, imageUtil){

    var ImgStateActive = stateFactory.extend(Active, function(){

        this.initForm();

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
     * @param {jQuery} $img
     * @param {string} propertyName
     * @returns {function}
     */
    var _getImgSizeChangeCallback = function($img, propertyName){

        var _setAttr = _.debounce(function(img, value, name){
            img.attr(name, value);
        }, 1000);

        return _.throttle(function(img, value, name){
            
            if(value){
                $img[propertyName](value);
                _setAttr(img, value, name);
            }else{
                $img[propertyName]('auto');
                img.removeAttr(propertyName);
            }
            $img.trigger('contentChange.qti-widget');
            
        }, 100);
    };

    /**
     * Extract a default label from a file/path name
     * @param {String} fileName - the file/path
     * @returns {String} a label
     */
    var _extractLabel = function extractLabel(fileName){
        return fileName
                .replace(/\.[^.]+$/, '')
                .replace(/^(.*)\//, '')
                .replace(/\W/, ' ')
                .substr(0, 255);
    };

    ImgStateActive.prototype.initForm = function(){
        
        var _widget = this.widget,
            $img = _widget.$original,
            $form = _widget.$form,
            img = _widget.element,
            baseUrl = _widget.options.baseUrl,
            responsive = true;
        
        $form.html(formTpl({
            baseUrl     : baseUrl || '',
            src         : img.attr('src'),
            alt         : img.attr('alt'),
            height      : img.attr('height'),
            width       : img.attr('width'),
            responsive  : responsive
        }));

        //init slider and set align value before ...
        _initAdvanced(_widget);
        _initSlider(_widget);
        _initAlign(_widget);
        _initUpload(_widget);

        //... init standard ui widget
        formElement.initWidget($form);

        //init data change callbacks
        formElement.setChangeCallbacks($form, img, {
            src : function(img, value){
                
                img.attr('src', value);

                $img.attr('src', itemUtil.fullpath(value, baseUrl));
                $img.trigger('contentChange.qti-widget').change();
                
                inlineHelper.togglePlaceholder(_widget);
                _initSlider(_widget);
                _initAdvanced(_widget);
            },
            alt : function(img, value){
                img.attr('alt', value);
            },
            longdesc : formElement.getAttributeChangeCallback(),
            align : function(img, value){
                inlineHelper.positionFloat(_widget, value);
            },
            height : _getImgSizeChangeCallback($img, 'height'),
            width : _getImgSizeChangeCallback($img, 'width')
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
            $slider = $form.find('.img-resizer-slider'),
            img = widget.element,
            $img = $container.find('img'),
            $height = $form.find('[name=height]'),
            $width = $form.find('[name=width]'),
            original = {
                h : img.attr('height') || $img.height(),
                w : img.attr('width') || $img.width()
            };

        $slider.noUiSlider({
            range : {
                min : 10,
                max : 200
            },
            start : 100
        }, $slider.hasClass('noUi-target'));

        $slider.off('slide').on('slide', _.throttle(function(e, value){
            if(!original.w){
               original.w = parseInt(img.attr('width'), 10); 
            }
            if(!original.h){
               original.h = parseInt(img.attr('height'), 10); 
            }
            var ratio = (value / 100),
                w = parseInt(ratio * original.w),
                h = parseInt(ratio * original.h);

            $width.val(w).change();
            $height.val(h).change();
        }, 100));
    };

    var _initAdvanced = function(widget){

        var $form = widget.$form,
            src = widget.element.attr('src');

        if(src){
            $form.find('[data-role=advanced]').show();
        }else{
            $form.find('[data-role=advanced]').hide();
        }
    };


    var _initUpload = function(widget){

        var $form = widget.$form,
            options = widget.options,
            img = widget.element,
            $uploadTrigger = $form.find('[data-role="upload-trigger"]'),
            $src = $form.find('input[name=src]'),
            $label = $form.find('input[name=alt]'),
            $width = $form.find('input[name=width]'),
            $height = $form.find('input[name=height]');

        $uploadTrigger.on('click', function(){
            $uploadTrigger.resourcemgr({
                appendContainer : options.mediaManager.appendContainer,
                root : '/',
                browseUrl : options.mediaManager.browseUrl,
                uploadUrl : options.mediaManager.uploadUrl,
                deleteUrl : options.mediaManager.deleteUrl,
                downloadUrl : options.mediaManager.downloadUrl,
                params : {
                    uri : options.uri,
                    lang : options.lang,
                    filters : 'image/jpeg,image/png,image/gif'
                },
                pathParam : 'path',
                select : function(e, files){
                    var file, label;
                    if(files && files.length){
                        file = files[0].file;
                        imageUtil.getSize(options.baseUrl + file, function(size){
                            if(size && size.width >= 0){
                                //update manually the object, to prevent the throttling used by the slider
                                img.attr('width', parseInt(size.width, 10));
                                img.attr('height', parseInt(size.height, 10));
                                $width.val(size.width);
                                $height.val(size.height);
                            }
                            if($.trim($label.val()) === ''){
                                label = _extractLabel(file);   
                                img.attr('alt', label);
                                $label.val(label).trigger('change');
                            }
                            _.defer(function(){
                                $src.val(file).trigger('change');
                            });
                        });
                    }
                }
            });
        });

    };

    return ImgStateActive;
});

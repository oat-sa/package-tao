define([
    'jquery',
    'lodash',
    'i18n',
    'util/image',
    'ui/resourcemgr'
], function($, _, __, imageUtil){

    return function($form, options){

        var $upload = $('[data-role="upload-trigger"]', $form),
            $src = $('input[name=data]', $form),
            $width = $('input[name=width]', $form),
            $height = $('input[name=height]', $form),
            $type = $('input[name=type]', $form),
            title = options.title ? options.title : __('Please select a background picture for your interaction from the resource manager. You can add new files from your computer with the button "Add file(s)".');

        var _openResourceMgr = function(){
            $upload.resourcemgr({
                title : title,
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
                    var selected;
                    if(files.length > 0){
                        selected = files[0];
                        imageUtil.getSize(options.baseUrl + files[0].file, function(size){
                            if(size && size.width >= 0){
                                $width.val(size.width).trigger('change');
                                $height.val(size.height).trigger('change');
                            }
                            $type.val(selected.mime).trigger('change');
                            _.defer(function(){
                                $src.val(selected.file).trigger('change');
                            });
                        });
                    }
                },
                open : function(){
                    //hide tooltip if displayed
                    if($src.hasClass('tooltipstered')){
                        $src.blur().tooltipster('hide');
                    }
                },
                close : function(){
                    //triggers validation : 
                    $src.blur();
                }
            });
        };

        $upload.on('click', _openResourceMgr);

        //if empty, open file manager immediately
        if(!$src.val()){
            _openResourceMgr();
        }
    };

});

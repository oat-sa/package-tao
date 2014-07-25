define([
    'jquery',
    'lodash',
    'util/image',
    'ui/resourcemgr'
], function($, _, imageUtil){

    return function($form, options){

        var $upload  = $('[data-role="upload-trigger"]', $form);
        var $src     = $('input[name=data]', $form);
        var $width   = $('input[name=width]', $form);
        var $height  = $('input[name=height]', $form);
        var $type    = $('input[name=type]', $form);

        $upload.on('click', function(){
            $upload.resourcemgr({
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
                }
            });
        });
    };

});

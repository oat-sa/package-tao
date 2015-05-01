define([
    'jquery',
    'lodash',
    'i18n',
    'util/image',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/resourceManager',
    'ui/resourcemgr'
], function($, _, __, imageUtil, resourceManager){

    return function($form, options){

        var _ns = '.imageSelector',
            $upload = $('[data-role="upload-trigger"]', $form),
            $src = $('input[name=data]', $form),
            $width = $('input[name=width]', $form),
            $height = $('input[name=height]', $form),
            $type = $('input[name=type]', $form);
            options.title = options.title
                ? options.title
                : __('Please select a background picture for your interaction from the resource manager.\
                      You can add new files from your computer with the button "Add file(s)".');

        /**
         * Configure and launch the pre-configured instance of the resource manager
         *
         * @private
         */
        var _openResourceMgr = function(){
            $upload.on('selected.upload', function(e, args) {
                if(args.size && args.size.width >= 0){
                    $width.val(args.size.width).trigger('change');
                    $height.val(args.size.height).trigger('change');
                }
                $type.val(args.selected.mime).trigger('change');
                _.defer(function(){
                    $src.val(args.selected.file).trigger('change');
                });

            })
                .on('open', function(){
                    $src.trigger('open.'+_ns);
                    //hide tooltip if displayed
                    if($src.hasClass('tooltipstered')){
                        $src.blur().tooltipster('hide');
                    }
                })
                .on('close', function(){
                    $src.trigger('close.'+_ns);
                });


            resourceManager($upload, options);

        };

        $upload.on('click', _openResourceMgr);

        //if empty, open file manager immediately
        if(!$src.val()){
            _openResourceMgr();
        }
    };

});

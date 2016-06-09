define([
    'jquery',
    'lodash',
    'util/bytes',
    'context',
    'ui/previewer'
], function($, _, bytes,context){
    'use strict';

    var ns = 'resourcemgr';

    return function(options){
        
        var $container      = options.$target;
        var $filePreview    = $('.file-preview', $container);
        var $previewer      = $('.previewer', $container);
        var $propType       = $('.prop-type', $filePreview); 
        var $propSize       = $('.prop-size', $filePreview); 
        var $propUrl        = $('.prop-url', $filePreview);
        var $link           = $('a',$propUrl);
        var $selectButton   = $('.select-action', $filePreview);
        var currentSelection= [];


        $container.on('fileselect.' + ns, function(e, file){

            if(file && file.file){
                startPreview(file);
                currentSelection = file;
            } else {
                stopPreview();
            }
        });

        $selectButton.on('click', function(e){
            e.preventDefault();


            var data = _.pick(currentSelection, ['file', 'type', 'mime', 'size', 'alt']);
            if(context.mediaSources && context.mediaSources.length === 0 && data.file.indexOf('local/') > -1){
                data.file = data.file.substring(6);
            }

            $container.trigger('select.' + ns, [[data]]);
        });

        function startPreview(file){
            $previewer.previewer(file);
            $propType.text(file.type + ' (' + file.mime + ')'); 
            $propSize.text(bytes.hrSize(file.size));
            $link.attr('href', file.download).attr('download', file.file);
            if($link.hasClass('hidden')){
                $link.removeClass('hidden');
            }
            $selectButton.removeAttr('disabled');
        }

        function stopPreview(){
            $previewer.previewer('update', {url : false});
            $propType.empty();
            $propSize.empty();
            $('a',$propUrl).addClass('hidden');
            $selectButton.attr('disabled', 'disabled');
        }
    };
});

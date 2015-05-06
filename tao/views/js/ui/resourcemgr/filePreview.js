define([
    'jquery',
    'lodash',
    'util/bytes',
    'ui/previewer'
], function($, _, bytes){
    'use strict';

    var ns = 'resourcemgr';

    return function(options){
        
        var $container      = options.$target;
        var $filePreview    = $('.file-preview', $container);
        var $previewer      = $('.previewer', $container);
        var $propType       = $('.prop-type', $filePreview); 
        var $propSize       = $('.prop-size', $filePreview); 
        var $propUrl        = $('.prop-url', $filePreview); 
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

        $container.on('filedelete.' + ns, function(){
            stopPreview();
        });

        $selectButton.on('click', function(e){
            e.preventDefault();
            $container.trigger('select.' + ns, [[_.pick(currentSelection, ['file', 'type', 'mime', 'size'])]]);
        });

        function startPreview(file){
            $previewer.previewer(file);
            $propType.text(file.type + ' (' + file.mime + ')'); 
            $propSize.text(bytes.hrSize(file.size)); 
            $propUrl.html('<a href="' + file.url + '">' + file.file + '</a>'); 
            $selectButton.removeAttr('disabled');
        }

        function stopPreview(){
            $previewer.previewer('update', {url : false});
            $propType.empty(); 
            $propSize.empty(); 
            $propUrl.empty(); 
            $selectButton.attr('disabled', 'disabled');
        }
    };
});

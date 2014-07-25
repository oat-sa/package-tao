define([
    'jquery',
    'lodash',
    'i18n',
    'core/mimetype',
    'tpl!ui/resourcemgr/tpl/fileSelect',
    'ui/uploader' 
], function($, _, __, mimeType, fileSelectTpl, uploader){
    'use strict';

    var ns = 'resourcemgr';

    function shortenPath(path){
        var tokens = path.replace(/\/$/, '').split('/');
        var size = tokens.length - 1;
        return _.map(tokens, function(token, index){
            return (token && index < size) ? token[0] : token;
        }).join('/');
    }

    function isTextLarger($element, text){
        var $dummy = $element
                        .clone()
                        .detach()
                        .css({
                            position: 'absolute',
                            visibility: 'hidden',
                            'text-overflow' : 'clip',
                            width: 'auto'
                        })
                        .text(text)
                        .insertAfter($element);
        var textSize = $dummy.width();
        $dummy.remove();

        return textSize > $element.width();
    }
   
    return function(options){
        var root            = options.root || '/';
        var $container      = options.$target;
        var $fileSelector   = $('.file-selector', $container); 
        var $fileContainer  = $('.files', $fileSelector);
        var $placeholder    = $('.empty', $fileSelector);
        var $uploadContainer= $('.uploader', $fileSelector);
        var parentSelector  = '#' + $container.attr('id') + ' .file-selector'; 
        var $pathTitle      = $fileSelector.find('h1 > .title');

        //set up the uploader 
        setUpUploader(root);

        //update current folder
        $container.on('folderselect.' + ns , function(e, fullPath, data, active){    
            var files;           
            //update title
            $pathTitle.text(isTextLarger($pathTitle, fullPath) ? shortenPath(fullPath) : fullPath); 

            //update content here
            if(_.isArray(data)){
                files = _.filter(data, function(item){
                    return !!item.name;
                }).map(function(file){
                    file.type = mimeType.getFileType(file);
                    file.path = (fullPath + '/' + file.name).replace('//', '/');
                    file.downloadUrl = options.downloadUrl + '?' +  $.param(options.params) + '&' + options.pathParam + '=' + file.path;
                    return file; 
                });
            
                updateFiles(fullPath, files);

                if(active){
                    $('li[data-file="' + active.path + '"]').trigger('click');
                } 
            }
        });

        $(window).on('resize.resourcemgr', _.throttle(updateSize, 10));

        //listen for file activation
        $(parentSelector)
            .off('click', '.files li')
            .on ('click', '.files li', function(e){
            
            var $selected   = $(this); 
            var $files      = $('.files > li', $fileSelector);
            var data        = _.clone($selected.data()); 

            if(!$.contains($selected.find('.actions')[0], e.target)){
                e.preventDefault();
            }

            $files.removeClass('active');
            $selected.addClass('active');
        
            $container.trigger('fileselect.' + ns, [data]); 
        });

        //select a file
        $(parentSelector)
            .off('click', '.files li a.select')
            .on ('click', '.files li a.select', function(e){
            e.preventDefault();
            $container.trigger('select.' + ns, [[_.pick($(this).parents('li').data(), ['file', 'type', 'mime', 'size'])]]);
        });

        //delete a file
        $fileContainer.on('delete.deleter', function(e, $target){
            var path, params = {};
            if(e.namespace === 'deleter' && $target.length){
                path = $target.data('file');
                $(this).one('deleted.deleter', function(){
                    params[options.pathParam] = path;
                    $.getJSON(options.deleteUrl, _.merge(params, options.params));
                    $container.trigger('filedelete.' + ns, [path]); 
                });
            }
        });
       

        function setUpUploader(currentPath){
            var $uploader =  $('.file-upload', $fileSelector);
            var $switcher = $('.upload-switcher a', $fileSelector);
            var $uploadPath = $('.current-path', $uploadContainer);

            $uploader.on('upload.uploader', function(e, file, result){
                setTimeout(function(){
                    switchUpload();
                    $container.trigger('filenew.' + ns, [result, currentPath]);
                }, 300);
            });
            $uploader.on('fail.uploader', function(){
                //TODO use a feedback popup
                window.alert('Unable to upload file');
                $uploader.uploader('reset');
            });

            $uploader.uploader({
                upload      : true,
                uploadUrl   : options.uploadUrl + '?' +  $.param(options.params) + '&' + options.pathParam + '=' + currentPath,
                fileSelect  : function(file){
                    //check the mime-type
                    if(options.params.filters){
                        var filters = options.params.filters.split(',');
                        if(!_.contains(filters, file.type)){
                            //TODO use a feedback popup
                            window.alert('Unauthorized file type');
                            return false;
                        }
                    }

                    //check if the file name isn't already used
                    var fileNames = [];
                    $fileContainer.find('li > .desc').each(function(){
                        fileNames.push($(this).text().toLowerCase());
                    });
                    if(_.contains(fileNames, file.name.toLowerCase())){
                        //TODO use a feedback popup
                        if(!window.confirm('Do you want to override ' + file.name + '?')){
                            return false;
                        }   
                    }
                    return file;
                } 
            });

            $container.on('folderselect.' + ns , function(e, fullPath, data){    
                currentPath = fullPath;
            
                $uploadPath.text(currentPath);
                $uploader.uploader('options', {
                    uploadUrl : options.uploadUrl + '?' +  $.param(options.params) + '&' + options.pathParam + '=' + currentPath
                });
            });

            //siwtch to upload mode
            $switcher.click(function(e){
                e.preventDefault();
                switchUpload();
            }); 
            
            var switchUpload = function switchUpload(){
                if($fileContainer.css('display') === 'none'){
                    $uploadContainer.hide();
                    $fileContainer.show();
                    $switcher.html('<span class="icon-add"></span>' + __('Upload'));
                } else {
                    $fileContainer.hide();
                    $uploadContainer.show();
                    $switcher.html('<span class="icon-undo"></span>' + __('Files'));
                    $uploader.uploader('reset');
                }
            };
        }
        
        function updateFiles(path, files){
            $fileContainer.empty();
            if(files.length){
                $placeholder.hide();
                $fileContainer.append(fileSelectTpl({
                    files : files
                }));
                
                updateSize();
            } else {
                $placeholder.show();
            }
        }

        function updateSize(){
            var listWidth = $fileContainer.innerWidth();
            $('li', $fileContainer).each(function(){
                var $item = $(this);
                var actionsWidth = $('.actions', $item).outerWidth(true);
                $('.desc', $item).width(listWidth - (actionsWidth + 40));   //40 is for the image in :before 
            });
        }
    };
});

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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 *
 * @author Bertrand <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'async',
    'i18n',
    'core/mimetype',
    'tpl!ui/resourcemgr/tpl/fileSelect',
    'ui/feedback', 
    'ui/uploader',
    'context'
], function($, _, async, __, mimeType, fileSelectTpl, feedback, uploader, context){
    'use strict';

    var ns = 'resourcemgr';

    function shortenPath(path){
        var tokens = path.replace(/\/$/, '').split('/');
        var start = tokens.length - 3;
        var end = tokens.length - 1;
        var title = _.map(tokens, function(token, index){
            return (index > start && token) ? ((index < end) ? token[0] : token) : undefined;
        });
        title = title.filter(Boolean);
        return title.join('/');
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
        var disableUpload  = options.disableUpload || false;
        var $container      = options.$target;
        var $fileSelector   = $('.file-selector', $container);
        var $fileContainer  = $('.files', $fileSelector);
        var $placeholder    = $('.empty', $fileSelector);
        var $uploader       = $('.file-upload-container', $fileSelector);
        var parentSelector  = '#' + $container.attr('id') + ' .file-selector'; 
        var $pathTitle      = $fileSelector.find('h1 > .title');
        var $browserTitle   = $('.file-browser > h1', $container);

        //set up the uploader
        if(disableUpload){
            var $switcher = $('.upload-switcher', $fileSelector);
            $switcher.remove();
        }
        else{
            setUpUploader(root);
        }
        //update current folder
        $container.on('folderselect.' + ns , function(e, fullPath, data, activePath){    
            var files;
            //update title

            $pathTitle.text(isTextLarger($pathTitle, fullPath) ? shortenPath(fullPath) : fullPath);

            //update content here
            if(_.isArray(data)){
                files = _.filter(data, function(item){
                    return !!item.uri;
                }).map(function(file){
                    file.type = mimeType.getFileType(file);
                    if(file.identifier === undefined){
                        file.display = (fullPath + '/' + file.name).replace('//', '/');
                    }
                    else{
                        file.display = (file.identifier + file.name);
                    }

                    file.viewUrl = options.downloadUrl + '?' +  $.param(options.params) + '&' + options.pathParam + '=' + encodeURIComponent(file.uri);
                    file.downloadUrl = file.viewUrl + '&svgzsupport=true';
                    return file;
                });
            
                updateFiles(fullPath, files);
                
                if(activePath){
                    $('li[data-file="' + activePath + '"]').trigger('click');
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

            $files.removeClass('active');
            $selected.addClass('active');
            $container.trigger('fileselect.' + ns, [data]);
        });

        //select a file
        $(parentSelector)
            .off('click', '.files li a.select')
            .on ('click', '.files li a.select', function(e){
            e.preventDefault();
            var data = _.pick($(this).parents('li').data(), ['file', 'type', 'mime', 'size', 'alt']);
            if(context.mediaSources && context.mediaSources.length === 0 && data.file.indexOf('local/') > -1){
                data.file = data.file.substring(6);
            }
            $container.trigger('select.' + ns, [[data]]);
        });

        //delete a file
        $fileContainer.on('delete.deleter', function(e, $target){
            var path, params = {};
            if(e.namespace === 'deleter' && $target.length){
                path = $target.data('file');
                params[options.pathParam] = path;
                $.getJSON(options.deleteUrl, _.merge(params, options.params), function(response){
                    if(response.deleted){
                        $container.trigger('filedelete.' + ns, [path]);
                    }
                });
            }
        });
       

        function setUpUploader(currentPath){
            var errors = [];
            var $switcher = $('.upload-switcher a', $fileSelector);

            $uploader.on('upload.uploader', function(e, file, result){
                var path = $('[data-display="'+currentPath+'"]').data('path') || $('[data-display="/'+currentPath+'"]').data('path');
                if(typeof path === 'undefined' || path === ''){
                    path = currentPath;
                }
                $container.trigger('filenew.' + ns, [result, path]);
            });
            $uploader.on('fail.uploader', function(e, file, err){
                errors.push(__('Unable to upload file %s : %s', file.name, err.message));
            });

            $uploader.on('end.uploader', function(){
                if(errors.length === 0){
                    _.delay(switchUpload, 500);
                } else {
                    feedback().error("<ul><li>" + errors.join('</li><li>') + "</li></ul>", {encodeHtml: false}); 
                }
                //reset errors
                errors = [];
            });

            $uploader.uploader({
                upload      : true,
                multiple    : true,
                uploadUrl   : options.uploadUrl + '?' +  $.param(options.params) + '&' + options.pathParam + '=' + currentPath,
                fileSelect  : function(files, done){
                    var givenLength = files.length;
                    var fileNames = [];
                    $fileContainer.find('li > .desc').each(function(){
                        fileNames.push($(this).text().toLowerCase());
                    });

                    //check the mime-type
                    if(options.params.filters){
                        var filters = [],
                            i;

                        if (!_.isString(options.params.filters)) {
                            for(i in options.params.filters){
                                filters.push(options.params.filters[i]['mime']);
                            }
                        } else {
                            filters = options.params.filters.split(',');
                        }
                        //TODO check stars
                        files = _.filter(files, function(file){
                            // Under rare circumstances a browser may report the mime type
                            // with quotes (e.g. "application/foo" instead of application/foo)
                            var checkType = file.type.replace(/^["']+|['"]+$/g, '');
                            return _.contains(filters, checkType);
                        });
                         
                        if(files.length !== givenLength){

                            //TODO use a feedback popup
                            feedback().error('Unauthorized files have been removed');
                        }
                    }

                    async.filter(files, function(file, cb){
                        var result = true;
                
                        //try to call a server side service to check whether the selected files exists or not.       
                        if(options.fileExistsUrl){
                            var pathParam = currentPath + '/' + file.name;
                            pathParam.replace('//','/');
                            $.getJSON(options.fileExistsUrl + '?' +  $.param(options.params) + '&' + options.pathParam + '=' + pathParam, function(response){
                                if(response && response.exists === true){
                                    result = window.confirm('Do you want to override ' + file.name + '?');
                                }
                                cb(result);
                            });
                        } else{
                            //fallback on client side check 
                            if(_.contains(fileNames, file.name.toLowerCase())){
                                result = window.confirm('Do you want to override ' + file.name + '?');
                            }
                            cb(result);
                        }
                    }, done);
                } 
            });

            $container.on('folderselect.' + ns , function(e, fullPath, data, uri){
                currentPath = uri;
                $uploader.uploader('options', {
                    uploadUrl : options.uploadUrl + '?' +  $.param(options.params) + '&' + options.pathParam + '=' + currentPath + '&relPath=' + currentPath
                });
            });

            //siwtch to upload mode
            $switcher.click(function(e){
                e.preventDefault();
                switchUpload();
            }); 
            
            var switchUpload = function switchUpload(){
                if($fileContainer.css('display') === 'none'){
                    $uploader.hide();
                    $fileContainer.show();
                    // Note: show() would display as inline, not inline-block!
                    $switcher.filter('.upload').css( {display: 'inline-block' } );
                    $switcher.filter('.listing').hide();
                    $browserTitle.text(__('Browse folders:'));
                } else {
                    $fileContainer.hide();
                    $placeholder.hide();
                    $uploader.show();
                    $switcher.filter('.upload').hide();
                    $switcher.filter('.listing').css( {display: 'inline-block' } );
                    $browserTitle.text(__('Upload into:'));
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
            } else if ($fileContainer.css('display') !== 'none'){
                $placeholder.show();
            }
        }

        function updateSize(){
            var listWidth = $fileContainer.innerWidth();
            $('li', $fileContainer).each(function(){
                var $item = $(this);
                var actionsWidth = $('.actions', $item).outerWidth(true);
                $('.desc', $item).width(listWidth - (actionsWidth + 60));   //40 is for the image in :before  and the possible scroll bar
            });
        }
    };
});

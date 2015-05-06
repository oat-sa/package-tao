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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
define(['jquery', 'i18n', 'context', 'filemanager/jqueryFileTree/jqueryFileTree'], function($, __, context){
        
        function selectUrl(){
                var urlData = $("#file-url").text();
                var mediaData = $("#file-url").data('media');
                window.top.opener.$(window.opener.document).trigger('fmSelect', [urlData, mediaData]);
        }

        function goToRoot(event){
                window.location = context.root_url + "filemanager/Browser/index";
                event.preventDefault();
        }

        function newFolder(){
                var parentDir = $("#dir-uri").text();
                var newDir = prompt(__("Enter a name for the new directory."));
                if(newDir){
                        $.ajax({
                                url: context.root_url + "filemanager/Browser/addFolder",
                                type: "POST",
                                data: {
                                        parent: parentDir,
                                        folder: newDir
                                },
                                dataType: 'json',
                                success: function(response){
                                        if(response.added){
                                                initFileTree(parentDir.replace(/\/$/, ''));
                                        }
                                }
                        });
                }
        }

        function download(event){
                window.location = context.root_url + 'filemanager/Browser/download?file='+encodeURIComponent($("#file-uri").text());
                event.preventDefault();
        }

        function removeFile(event){
                if(confirm(__('Please confirm file deletion.'))){
                        $.ajax({
                                url: context.root_url + "filemanager/Browser/delete",
                                type: "POST",
                                data: {
                                        file: $("#file-uri").text()
                                },
                                dataType: 'json',
                                success: function(response){
                                        if(response.deleted){
                                                initFileTree();
                                        }
                                }
                        });
                }
                event.preventDefault();
        }
        function removeFolder(event){
                if(confirm(__("Please confirm directory deletion.\nBe careful, it will remove the entire content of the directory!"))){
                        $.ajax({
                                url: context.root_url + "filemanager/Browser/delete",
                                type: "POST",
                                data: {
                                        folder: $("#dir-uri").text()
                                },
                                dataType: 'json',
                                success: function(response){
                                        if(response.deleted){
                                                initFileTree();
                                        }
                                }
                        });
                }
                event.preventDefault();
        }

        /**
         * Highlight a given file/directory in the tree.
         * If no file parameter is given, this function will
         * only unhighlight the currently highlighted item.
         * 
         * @param {String} The file/directory to be hilighted.
         */
        function highlight(file){
                //remove present hilights.
                $(".jqueryFileTree a").removeClass('active');

                if (typeof(file) !== 'undefined'){
                        $(".jqueryFileTree").find('a[rel="' + file + '"]')
                                                                .addClass('active');	
                }
        }

        function initFileTree(toOpen){
                $('#file-container').fileTree({
                                root: '/',
                                open: toOpen,
                                script: context.root_url + "filemanager/Browser/fileData",
                                folderEvent: 'click',
                                multiFolder: false
                        },

                        /**
                         * by clikcing on a file in the file tree
                         * @param {String} file the file path
                         */
                        function(file) {
                                highlight(file);

                                $("#file-preview").html("<img src='"+ context.base_www +"img/throbber.gif'  alt='loading' />");
                                $.post(context.root_url + "filemanager/Browser/getInfo", {file: file}, function(response){
                                        if(response.type){
                                                $("#file-url").data('media',{
                                                        type : response.type,
                                                        width: response.width || '',
                                                        height: response.height || ''
                                                });

                                                //enable the selection only once the data are received
                                                $("a.link.select").click(function(e){
                                                    e.preventDefault();    
                                                    selectUrl();
                                                });

                                                //actions' images
                                                if ($("a.link.select, a.link.download, a.link.delete").hasClass('disabled')){
                                                        $("a.link.select, a.link.download, a.link.delete").removeClass('disabled');
                                                } 

                                                //actions' links
                                                $("a.link.download").bind('click', download);
                                                $("a.link.delete").unbind('click', removeFolder)

												//url box
												$("#file-url").html( response.url);
												$("#file-uri").html( file );
					
												if (typeof(response.dir) != 'undefined'){
													$("#dir-uri").html(response.dir);
												}


                                                if (typeof(response.dir) !== 'undefined'){
                                                        $("#dir-uri").html(response.dir);
                                                }


                                                $("#file-preview").load(context.root_url + "filemanager/Browser/preview",{file: file});
                                        }
                                }, "json");
                        },

                        /**
                         * by clikcing on a dir in the file tree
                         * @param {String} dir the directory path
                         */
                        function(dir) {
                                highlight();
                                $('#file-url, #file-uri').empty();

                                //disable buttons that have no effects on a directory.
                                $("a.link.select, a.link.download").toggleClass("disabled", true);
                                //enable buttons that have effects on a directory.
                                $("a.link.new-dir, a.link.root, a.link.delete").toggleClass("disabled", false);

                                //events.
                                $("a.link.select, a.link.download, a.link.delete, a.link.root")
                                        .off('click')
                                        .on('click', function(e){ 
                                            e.preventDefault(); 
                                            return false; 
                                        });
                                $("a.link.delete").on('click', removeFolder);
                                $("a.link.root").on('click', goToRoot);

                                //set current dir
                                $("#dir-uri").html(dir);
                                $("#media_folder").val(dir);
                                $("#file-preview").html('');
                        }
                );
        }
        
        
        return {
            start : function(options){
                
                initFileTree(options.openFolder);

                $("a.link.disabled").live('click', function(event){ event.preventDefault(); });
                $("a.link.new-dir").click(function(e){
                    e.preventDefault();
                    newFolder();
                });

                $("#media_file").change(function(){
                    $("#media_name").val(this.value.replace(/\\/g,'/').replace( /.*\//, ''));
                });
            }
        };
});
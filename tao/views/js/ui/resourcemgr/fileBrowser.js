define(['jquery', 'lodash'], function($, _) {
    'use strict';

    var ns = 'resourcemgr';

    return function(options){

        var root            = options.root || '/';
        var $container      = options.$target;
        var parentSelector  = '#' + $container.attr('id') + ' .file-browser'; 
        var $fileBrowser    = $('.file-browser', $container);  
        var $folderContainer= $('.folders', $fileBrowser);
        var fileTree        = {};

        //create the tree node for the ROOT folder by default
        $folderContainer.append('<li class="active"><a class="root-folder" data-path="/" href="#">' + root + '</a></li>');

        //load the content of the ROOT
        getFolderContent(fileTree, root, function(content){
    
            var $rootNode = $('.root-folder', $folderContainer);
             //create an inner list and append found elements
             var $innerList = $('<ul></ul>').insertAfter($rootNode);
             if(content.children){
                $rootNode.addClass('opened');
             }  
             updateFolders(content, $innerList); 

             //internal event to set the file-selector content
             $container.trigger('folderselect.' + ns , [root, content.children]);
        });

        // by clicking on the tree (using a live binding  because content is not complete yet)
        $(parentSelector)
            .off('click', '.folders a') 
            .on ('click', '.folders a', function(e){
            e.preventDefault();

            var $selected = $(this); 
            var $folders = $('.folders li', $fileBrowser);
            var fullPath = $selected.data('path');
            var subTree = getByPath(fileTree, fullPath);

            //toggle active element
            $folders.removeClass('active');
            $selected.parent('li').addClass('active');
                        
            //get the folder content
            getFolderContent(subTree, fullPath, function(content){

                if(content){
                     //either create the inner list of the content is new or just show it 
                     var $innerList = $selected.siblings('ul');
                     if(!$innerList.length && content.children && _.find(content.children, 'path') && !content.empty){
                        $innerList = $('<ul></ul>').insertAfter($selected);     
                        updateFolders(content, $innerList);
                        $selected.addClass('opened');

                     } else if($innerList.length){
                        if($innerList.css('display') === 'none'){
                            $innerList.show();
                            $selected.addClass('opened');
                        } else {
                            $innerList.hide();
                            $selected.removeClass('opened');
                        } 
                     }
                    
                     //internal event to set the file-selector content
                     $container.trigger('folderselect.' + ns , [fullPath, content.children]);
                }
            });
        });

        $container.on('filenew.' + ns, function(e, file, path){
            var subTree = getByPath(fileTree, path);
            if(subTree){
                if(!subTree.children){
                    subTree.children = [];
                }
                if(!_.find(subTree.children, {name : file.name})){
                    subTree.children.push(file);
                    $container.trigger('folderselect.' + ns , [path, subTree.children, file]);
                }
            }
        });

        $container.on('filedelete.' + ns, function(e, path){
            removeFromPath(fileTree, path);
        });
   
        /**
         * Get the content of a folder, either in the model or load it
         * @param {Object} tree - the tree model
         * @param {String} path - the folder path (relative to the root)
         * @param {Function} cb- called back with the content in 1st parameter
         */
        function getFolderContent(tree, path, cb){
            var content = getByPath(tree, path);
            if(!content || (!content.children && !content.empty)){
                loadContent(path).done(function(data){
                    if(!tree.path){
                        tree = _.merge(tree, data);
                    } else if (data.children) {
                        if(!_.find(content.children, 'path')){
                            tree.empty = true;
                        }
                        setToPath(tree, path, data.children);
                    } else {
                        tree.empty = true;
                    }
                    cb(data);
                });
            } else {
                cb(content);
            }
        } 

        /**
         * Get a subTree from a path
         * @param {Object} tree - the tree model
         * @param {String} path - the path (relative to the root)
         * @returns {Object} the subtree that matches the path
         */
        function getByPath(tree, path){
            var match;
            if(tree){
                if(tree.path === path){
                    match = tree;
                } else if(tree.children){
                   _.forEach(tree.children, function(child){
                        match = getByPath(child, path);
                        if(match){
                            return false;
                        }
                   });
                }
            }
            return match;
        }

        /**
         * Merge data into at into the subtree
         * @param {Object} tree - the tree model
         * @param {String} path - the path (relative to the root)
         * @param {Object} data - the sbutree to merge at path level
         * @returns {Boolean}  true if done
         */
        function setToPath(tree, path, data){
            var done = false;
            if(tree){
                if(tree.path === path){
                    tree.children = data;
                } else if(tree.children){
                   _.forEach(tree.children, function(child){
                        done = setToPath(child, path, data);
                        if(done){
                            return false;
                        }
                    });
                }
            }
            return done;
        }

        function removeFromPath(tree, path){
            var done = false;
            var removed = [];
            if(tree && tree.children){
                removed = _.remove(tree.children, function(child){
                    return child.path === path || (child.name && tree.path + child.name === path);
                });
                done = removed.length > 0;
                if(!done){
                   _.forEach(tree.children, function(child){
                        done = removeFromPath(child, path);
                        if(done){
                            return false;
                        }
                    });
                }
            }
            return done;
        }

        /**
         * Get the content of a folder
         * @param {String} path - the folder path
         * @returns {jQuery.Deferred} the defferred object to run done/complete/fail 
         */
        function loadContent(path){
            var parameters = {};
            parameters[options.pathParam] = path;
            return $.getJSON(options.browseUrl, _.merge(parameters, options.params));
        }

        /**
         * Update the HTML Tree
         * @param {Object} data - the tree data
         * @param {jQueryElement} $parent - the parent node to append the data
         * @param {Boolean} [recurse] - internal recursive condition
         */
        function updateFolders(data, $parent, recurse){
           var $item;
           if(recurse && data && data.path){
                $item = $('<li><a data-path="' + data.path + '" href="#">' + data.path.split('/').pop() + '</a></li>').appendTo($parent);
           }
           if(data && data.children && _.isArray(data.children) && !data.empty){
                _.forEach(data.children, function(child){
                    updateFolders(child, $parent, true);
                });
           } 
        }
    };
});

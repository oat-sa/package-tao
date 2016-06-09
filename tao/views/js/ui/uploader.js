/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'async',
    'core/pluginifier',
    'context',
    'util/bytes',
    'tpl!ui/uploader/uploader',
    'tpl!ui/uploader/fileEntry',
    'ui/filesender',
    'ui/progressbar'
], function($, _, __, async, Pluginifier, context, bytes, uploaderTpl, fileEntryTpl){
    'use strict';

    var ns = 'uploader';
    var dataNs = 'ui.' + ns;

    //the plugin defaults
    var defaults = {
        upload              : true,
        read                : false,
        multiple            : false,
        uploadQueueSize     : 3,
        inputName           : 'content',
        showResetButton     : true,
        showUploadButton    : true,
        browseBtnClass      : 'btn-browse',
        uploadBtnClass      : 'btn-upload',
        resetBtnClass       : 'btn-reset',
        fileNameClass       : 'file-name',
        dropZoneClass       : 'file-drop',
        progressBarClass    : 'progressbar',
        dragOverClass       : 'drag-hover',
        formAttributes      : {
            'class' : 'uploader uploaderContainer'
        },
        defaultErrMsg       : __('Unable to upload file'),
        uploadBtnText       : __('Upload'),

        /**
         * Make files available before file selection. It can be used to filter.
         * @callback fileSelect
         * @param {Array<File>} files - the selected files
         * @param {Function} [done] - callback with filtered files
         * @returns {undefined|Array<File>} the files to be selected
         */
        fileSelect : function(files, done){
            if(_.isFunction(done)){
                return done(files);
            }
            return files;
        }
    };

    //feature tests
    var tests = {
        filereader  : typeof FileReader !== 'undefined',
        dnd         : 'draggable' in document.createElement('span')
    };

    /**
     * Define a jQuery component to help you to manage file(s) upload/reading.
     * @exports ui/uploader
     */
    var uploader = {

        /**
         * Initialize the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').uploader();
         *
         * @constructor
         * @param {Object} [options] - the plugin options
         * @param {Boolean} [options.upload =  true] - if we upload the file once selected
         * @param {String} [options.uploadUrl] - the URL where the files will be posted
         * @param {jQueryElement} [options.$form] - a form to be used instead
         * @param {Boolean} [options.read =  false] - if we can read the file once selected
         * @param {Boolean} [options.multiple =  false] - enable to select more multiple files (may be not supported by old browsers)
         * @param {Number} [options.uploadQueueSize =  3] - max parallel uploads (applies only in multiple mode)
         * @param {String} [options.browseBtnClass = btn-browse] - the class to identify the browse button
         * @param {String} [options.uploadBtnClass = btn-upload] - the class to identify the upload button
         * @param {String} [options.resetBtnClass = btn-reset] - the class to identify the reset button
         * @param {String} [options.fileNameClass = file-name] - the class of the elt where the file name is set
         * @param {String} [options.dropZoneClass = file-drop] - the class of the drop file elt
         * @param {String} [options.progressBarClass = progressbar] - the class to identify the progress bar
         * @param {String} [options.dragOverClass = drag-hover] - the class to set to the drop zone when dragging over
         * @param {Function} [options.fileSelect] - called back before selection with files in params and returns the files to select; filter use case
         * @param {Object} [options.formAttributes] - object with all the attributes you want to be on the form element
         * @param {String} [options.defaultErrMsg] - localized error message when something goes wrong
         * @param {String} [options.uploadBtnText] - text on upload button
         * @returns {jQueryElement} for chainingV
         */
        init : function(options){
            var self = uploader;

            //get options using default
            options = _.defaults(options || {}, defaults);

            return this.each(function(){
                var $elt = $(this),
                    $builtInForm;

                if(!$elt.data(dataNs)){

                    $elt.html(uploaderTpl(options));

                    // form could be inside $elt ...
                    $builtInForm = options.$form && options.$form.length ? options.$form : $elt.find('form');

                    // ... if not it could be a wrapper
                    if(!$builtInForm.length) {
                        $builtInForm = $elt.closest('form');
                    }

                    // ... if no form is present wrap $elt in one
                    if(!$builtInForm.length) {
                        $elt.wrap($('<form>', options.formAttributes));
                        $builtInForm = $elt.parent();
                    }


                    //retrieve elements
                    options.$input          = $('input[type=file]', $elt);
                    options.$browseBtn      = $('.' + options.browseBtnClass, $elt);
                    options.$fileName       = $('.' + options.fileNameClass, $elt);
                    options.$dropZone       = $('.' + options.dropZoneClass, $elt);
                    options.$progressBar    = $('.' + options.progressBarClass, $elt);
                    options.$form           = $builtInForm;
                    options.$uploadBtn      = $('.' + options.uploadBtnClass, $elt);
                    options.$resetBtn       = $('.' + options.resetBtnClass, $elt);

                    options.useDropZone     = tests.dnd;

                    options.dropZonePlaceholder = options.$dropZone.html();
                    options.fileNamePlaceholder = options.$fileName.text();

                    options.files = [];

                    $elt.data(dataNs, options);

                    self._reset($elt);

                    var inputHandler = function (e) {
                        // _.values also get the length property of the FileList object,
                        // so we go for a plain old loop.
                        var finalFiles = [];
                        _.forEach(e.target.files, function(file) {
                            finalFiles.push(file);
                        });

                        self._selectFiles($elt, finalFiles);
                    };

                    var dragOverHandler = function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        options.$dropZone.addClass(options.dragOverClass);
                    };
                    var dragOutHandler = function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        options.$dropZone.removeClass(options.dragOverClass);
                    };


                    //manage input selection
                    if(options.read && !tests.filereader) {
                        // Nope... :/
                        require(['filereader'], function(){
                            options.$input.fileReader({
                                id: 'fileReaderSWFObject',
                                filereader: context.taobase_www + 'js/lib/polyfill/filereader.swf',
                                callback: function() {
                                    options.$input.on('change', inputHandler);
                                }
                            });
                        });
                    } else {
                        options.$input.on('change', inputHandler);
                    }

                    // IE Specific hack. It prevents the browseBtn to slightly
                    // move on click. Special thanks to Dieter Raber, OAT S.A.
                    options.$input.on('mousedown', function(e){
                        e.preventDefault();
                        $(this).blur();
                        return false;
                    });

                    //manage drag and drop selection
                    if(options.useDropZone){

                        //prevent drag and drop outside the zone to loose the current context
                        $(document)
                            .off('drop.' +ns)
                            .on('drop.' + ns, function(e){
                                e.stopImmediatePropagation();
                                e.preventDefault();
                                return false;
                            });
                        $(document)
                            .off('dragover.' + ns)
                            .on('dragover.' + ns, function(e){
                                e.stopImmediatePropagation();
                                e.preventDefault();
                                return false;
                            });
                        options.$dropZone
                            .on('dragover', dragOverHandler)
                            .on('dragend', dragOutHandler)
                            .on('dragleave', dragOutHandler)
                            .on('drop', function(e){
                                var files = [];
                                dragOutHandler(e);

                                if(e.target.files){
                                    files = _.values(e.target.files);
                                } else if ( e.originalEvent.files){
                                    files = _.values(e.originalEvent.files);
                                } else if ( e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files){
                                    files = _.values(e.originalEvent.dataTransfer.files);
                                }

                                if(files && files.length){
                                    var append = options.$dropZone.children('ul').length > 0;
                                    if(!options.multiple){
                                        files = [files[0]];
                                        append = false;
                                    }

                                    self._selectFiles($elt, files, append);
                                }
                                return false;
                            });
                    } else {
                        options.$dropZone.hide();
                    }

                    //getting files
                    $elt.on('fileselect.' + ns, function(){
                        if(options.files.length === 0){
                            self._reset($elt);
                        }

                        if(options.upload){
                           options.$uploadBtn
                                .off('click')
                                .on('click', function(e){
                                    e.preventDefault();
                                    self._upload($elt, options.files);
                                }).removeProp('disabled');
                        }

                        if(options.read){
                            self._read($elt, options.files);
                        }

                        options.$resetBtn
                            .off('click')
                            .on('click', function(e){
                                e.preventDefault();
                                self._reset($elt);
                            }).removeProp('disabled');
                    });

                    /**
                     * The plugin has been created.
                     * @event uploader#create.uploader
                     */
                    $elt.trigger('create.' + ns);
                }
            });
        },

       /**
        * Select files to upload/read.
        *
        * Called the jQuery way once registered by the Pluginifier:
        * @example $('selector').uploader('selectFiles', files);
        *
        * @param {jQueryElement} $elt - plugin's element
        * @param {Array<File>} files - the selected files
        * @param {Boolean} [append = false] - in append mode the files are added instead of replaced
        * @fires uploader#fileselect.uploader
        */
        _selectFiles : function _selectFiles($elt, files, append){
            var self = this;
            var listContent;
            var options = $elt.data(dataNs);

            //update the file name field with the current number of files selected
            var updateFileName = function updateFileName(){
                var length = options.files.length;
                options.$fileName
                    .text(length + ' ' + (length > 1 ? __('files selected') : __('file selected')))
                    .removeClass('placeholder');
            };

            if(files.length <= 0 && !append){

                //empty file list, so we reset the plugin
                self._reset($elt);

            }
            if(files.length > 0){

                //execute the fileSelect function to filter files before selection
                options.fileSelect.call($elt, files, function(filteredFiles){

                    if(append){
                        options.files = options.files.concat(filteredFiles);
                    } else {
                        options.files = filteredFiles;
                    }

                    if(options.useDropZone){

                        updateFileName();

                        listContent = _.reduce(filteredFiles, function(acc, file){
                            return acc + fileEntryTpl({
                                name : file.name,
                                size : bytes.hrSize(file.size)
                            });
                        }, '');

                        if(append){
                            options.$dropZone
                                .children('ul').append(listContent);
                        } else {
                            options.$dropZone
                                .html('<ul>' + listContent + '</ul>');
                        }

                        options.$dropZone
                            .off('delete.delter', 'li')
                            .on('delete.deleter', 'li', function(e){

                                var name = $(e.target).data('file-name');

                                options.$dropZone
                                   .off('deleted.deleter')
                                   .one('deleted.deleter', function(){
                                        options.files =  _.reject(options.files, {name : name});
                                        if(options.files.length === 0){
                                            self._reset($elt);
                                        } else {
                                            updateFileName();
                                        }
                                    });
                            });
                    } else {
                        //legacy mode, no dnd support
                        options.files = options.files.slice(0, 1);
                        options.$fileName
                            .text(files[0].name)
                            .removeClass('placeholder');
                    }

                    /**
                     * Files has been selected
                     * @event uploader#fileselect.uploader
                     */
                    $elt.trigger('fileselect.' + ns);
                });
            }
        },

       /**
        * Get the selected files.
        *
        * Called the jQuery way once registered by the Pluginifier:
        * @example var files = $('selector').uploader('files');
        *
        * @param {jQueryElement} $elt - plugin's element
        * @returns {Array<File>} the selected files
        */
        _files : function($elt){
            var files   = [];
            var options = $elt.data(dataNs);
            if(options){
                files = options.files;
            }
            return files;
        },

       /**
        * Reset the component
        *
        * Called the jQuery way once registered by the Pluginifier:
        * @example $('selector').uploader('reset');
        *
        * @param {jQueryElement} $elt - plugin's element
        * @fires uploader#reset.uploader
        */
        _reset : function($elt){
            var options = $elt.data(dataNs);

            options.$fileName
                .text(options.fileNamePlaceholder)
                .addClass('placeholder');

            options.$dropZone.empty().html(options.dropZonePlaceholder);

            options.$uploadBtn.prop('disabled', true);
            options.$resetBtn.prop('disabled', true);

            if(options.$progressBar){
                options.$progressBar
                    .removeClass('success')
                    .progressbar('destroy')
                    .progressbar({value :  0});
            }
            /**
             * The plugin has been created.
             * @event uploader#reset.uploader
             */
            $elt.trigger('reset.' + ns);
        },

       /**
        * Upload the selected file
        *
        * Called the jQuery way once registered by the Pluginifier:
        * @example $('selector').uploader('upload');
        *
        * @param {jQueryElement} $elt - plugin's element
        * @fires uploader#upload.uploader
        * @fires uploader#fail.uploader
        * @fires uploader#end.uploader
        */
        _upload : function($elt){
            var length,
                $fileEntries,
                entryHeight,
                errors = [],
                q;

            var options = $elt.data(dataNs);

            if(options && options.files.length){

                length          = options.files.length;
                $fileEntries    = $('ul', options.$dropZone);
                entryHeight     = $('li:first', $fileEntries).outerHeight();

                //create an async queue to start uploads
                q = async.queue(function (file, done) {
                    var $fileEntry  = $('li[data-file-name="' + file.name + '"]', $fileEntries);
                    var $status     = $('.status', $fileEntry);
                    var index       = $fileEntries.children().index($fileEntry);

                    //update the scroll into the element
                    options.$dropZone.stop(true, true).animate({ scrollTop : index * entryHeight }, 25);

                    $status.removeClass('success')
                            .removeClass('error')
                            .addClass('sending');

                    //send (upload) the file
                    options.$form.sendfile({
                        url : options.uploadUrl,
                        file : file,
                        loaded : function(result){
                            $status.removeClass('sending')
                                    .removeClass('error')
                                    .addClass('success');
                            done(null, result);
                        },
                        failed : function(message){
                            message = message || options.defaultErrMsg;
                            $status.removeClass('sending')
                                    .removeClass('success')
                                    .addClass('error')
                                    .attr('title', message);
                            done(new Error(message));
                        }
                    });

                }, options.uploadQueueSize || 1);

                //disable buttons
                options.$uploadBtn.prop('disabled', true);
                options.$resetBtn.prop('disabled', true);

                options.$progressBar.progressbar('value', 0);

                //start pushing uploads into the queue
                _.forEach(options.files, function(file, index){
                    _.delay(function(){
                        q.push(file, function(err, result){
                            var complete =  ((index + 1) / length) * 100;

                            if(err){
                                errors.push(err);

                                /**
                                 * The file fails to upload
                                 * @event uploader#fail.uploader
                                 * @param {Object} file - the uploaded file
                                 * @param {Object} err - the error
                                 */
                                $elt.trigger('fail.'+ns, [file, err]);

                            } else {

                                /**
                                 * A file is uploaded
                                 * @event uploader#upload.uploader
                                 * @param {Object} file - the uploaded file
                                 * @param {Object} result - the upload response
                                 */
                                $elt.trigger('upload.'+ns, [file, result]);
                            }

                            //update progress bar regarding the number of files uploaded
                            options.$progressBar.progressbar('value', complete);

                            if(complete >= 100){
                                if(errors.length === length){
                                    options.$progressBar.addClass('error');
                                } else if (errors.length > 0){
                                    options.$progressBar.addClass('warning');
                                } else {
                                    options.$progressBar.addClass('success');
                                }

                                /**
                                 * The upload sequence is complete
                                 * @event uploader#end.uploader
                                 */
                                $elt.trigger('end.'+ns);
                            }
                        });
                    } , 50);
                });
            }
        },

       /**
        * Read the selected file.
        *
        * TODO update files status and progress bar by file
        *
        * Called the jQuery way once registered by the Pluginifier:
        * @example $('selector').uploader('read');
        *
        * @param {jQueryElement} $elt - plugin's element
        * @fires uploader#readstart.uploader
        * @fires uploader#readend.uploader
        */
        _read : function($elt){
            var options = $elt.data(dataNs);

            if(options && options.files.length){

                _.forEach(options.files, function(file){
                    // Show information about the processed file to the candidate.
                    var filename = file.name;
                    var filesize = file.size;
                    var filetype = file.type;

                    // Let's read the file to get its base64 encoded content.
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        options.$progressBar.progressbar('value', 100);

                        /**
                         * The read is fininshed
                         * @event uploader#readend.uploader
                         * @param {Object} file - the reading file
                         * @param {Object} result - the content
                         */
                        $elt.trigger('readend.'+ns, [file, e.target.result]);
                    };

                    reader.onloadstart = function (e) {
                        options.$progressBar.progressbar('value', 0);

                        /**
                         * The reading starts
                         * @event uploader#readstart.uploader
                         * @param {Object} file - the reading file
                         */
                        $elt.trigger('readstart.'+ns, [file]);
                    };

                    if(options.$progressBar.length){
                        reader.onprogress = function (e) {
                            var percentProgress = Math.ceil(Math.round(e.loaded) / Math.round(e.total) * 100);
                            options.$progressBar.progressbar('value', percentProgress);
                        };
                    }
                    reader.readAsDataURL(file);
                });
            }
        },

        /**
         * Destroy completely the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier:
         * @example $('selector').uploader('destroy');
         *
         * @fires uploader#destroy.uploader
         */
        destroy : function(){
            this.each(function(){
                var $elt = $(this);

                $(document)
                    .off('drop.' +ns)
                    .off('dragover.' + ns);

                $elt.empty();

                /**
                 * The plugin has been destroyed.
                 * @event uploader#destroy.uploader
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
    };

    //Register the incrementer to behave as a jQuery plugin.
    Pluginifier.register(ns, uploader, { expose : ['reset', 'selectFiles', 'upload', 'read'] });

});

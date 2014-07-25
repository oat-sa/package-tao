HtmlEditor = Class.extend({
    init : function(type, serial, $editor){
        this.type = type;
        this.serial = serial;
        this.$editor = $editor;
        this.elements = [];
    },
    bindElementLinkListener : function(){
        this.bindMathLinkListener();
        this.bindObjectLinkListener();
    },
    getDeletedElements : function(one){
        var deleted = [];
        var body = this.$editor.val(); //TODO: improve with the use of regular expressions:
        for(var s in this.elements){
            if(body.indexOf(s) < 0){
                deleted.push(s);
                if(one){
                    return deleted;
                }
            }
        }
        return deleted;
    },
    deleteElements : function(serials){

        if(!serials || serials.length <= 0){
            return false;
        }

        var htmlEditor = this;

        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/deleteElements",
            data : {
                'type' : this.type,
                'serial' : this.serial,
                'elements' : serials
            },
            dataType : 'json',
            success : function(r){

                if(r.deleted){
                    for(var i in serials){
                        var s = serials[i];
                        delete htmlEditor.elements[s];
                        //delete:
                        var elts = qtiEdit.getEltInFrame('div#' + s, htmlEditor.getEditorDocument());
                        if(!elts.length){
                            elts = qtiEdit.getEltInFrame('span#' + s, htmlEditor.getEditorDocument());
                        }
                        if(elts.length){
                            elts[0].remove();
                        }
                    }

                    //save item data, i.e. validate the changes operated on the item data:
                    htmlEditor.saveData();

                }else{

                    for(var i in serials){
                        htmlEditor.elements[serials[i]] = serials[i];
                    }

                }

            }
        });
    },
    saveData : function(){
        var body = util.htmlEncode(this.getContent());
        qtiEdit.saveData(this.type, this.serial, body);
    },
    initEditor : function(){
        var iFrameClass = this.iFrameClass ? this.iFrameClass : 'wysiwyg-htmlarea';
        if(this.$editor.parents('form#InteractionForm').length){
            iFrameClass += ' wysiwyg-interaction';
        }

        this.$editor.wysiwyg({
            css : qtiEdit.getFrameCSS(),
            iFrameClass : iFrameClass,
            controls : this.getControls(),
            events : this.getEvents()
        });
    },
    getEditorDocument : function(){
        return this.$editor.wysiwyg('document');
    },
    getContent : function(){
        this.$editor.wysiwyg('saveContent');
        return this.$editor.val();
    },
    getControls : function(){
        return {
            strikeThrough : {visible : false}, //not allowed in QTI html
            underline : {visible : false},
            insertTable : {visible : false},
            justifyLeft : getCustomControl('justifyLeft'),
            justifyCenter : getCustomControl('justifyCenter'),
            justifyRight : getCustomControl('justifyRight'),
            justifyFull : getCustomControl('justifyFull'),
            indent : getCustomControl('indent'),
            outdent : {visible : true},
            subscript : {visible : true},
            superscript : {visible : true},
            undo : {visible : !util.msie()},
            redo : {visible : !util.msie()},
            insertOrderedList : {visible : true},
            insertUnorderedList : {visible : true},
            insertHorizontalRule : {visible : false},
            cut : {visible : false},
            copy : {visible : false},
            paste : {visible : false},
            html : getCustomControl('customHTML'),
            h1 : {visible : false},
            h2 : {visible : false},
            h3 : {visible : false},
            h4 : {visible : false},
            h5 : {visible : false},
            h6 : {visible : false},
            addChoiceInteraction : {visible : false},
            addAssociateInteraction : {visible : false},
            addOrderInteraction : {visible : false},
            addMatchInteraction : {visible : false},
            addInlineChoiceInteraction : {visible : false},
            addTextEntryInteraction : {visible : false},
            addExtendedTextInteraction : {visible : false},
            addHotTextInteraction : {visible : false},
            addGapMatchInteraction : {visible : false},
            addHotspotInteraction : {visible : false},
            addGraphicOrderInteraction : {visible : false},
            addGraphicAssociateInteraction : {visible : false},
            addGraphicGapMatchInteraction : {visible : false},
            addSelectPointInteraction : {visible : false},
            addPositionObjectInteraction : {visible : false},
            addSliderInteraction : {visible : false},
            addUploadInteraction : {visible : false},
            addEndAttemptInteraction : {visible : false},
            addMediaInteraction : {visible : false},
            addObject : getCustomControl('addObject', {htmlEditor : this}),
            addMath : getCustomControl('addMath', {htmlEditor : this}),
            insertImage : getCustomControl('insertImage', {}),
            createHotText : {visible : false},
            createGap : {visible : false},
            saveItemData : {visible : false},
            saveInteractionData : {visible : false}
        }
    },
    getEvents : function(){
        var htmlEditor = this;
        return {
            keyup : function(e){
                if(htmlEditor.getDeletedElements(true).length > 0){
                    var elements = htmlEditor.getDeletedElements();

                    if(util.msie()){//undo unavailable in msie
                        htmlEditor.deleteElements(elements);
                    }else{
                        var eltsStr = '';
                        var elementsCount = util.count(elements);
                        if(elementsCount){
                            eltsStr += elementsCount + ' element' + (elementsCount > 1 ? 's' : '');
                        }
                        util.confirmBox(__('QTI Element Deletion'), __('Please confirm deletion of QTI elements from the item.') + ' (' + eltsStr + ')', {
                            'Cancel' : function(){
                                htmlEditor.$editor.wysiwyg('undo');
                                $(this).dialog('close');
                            },
                            'Delete' : function(){
                                htmlEditor.deleteElements(elements);
                                $(this).dialog('close');
                            }
                        });
                    }
                    return false;
                }
                return true;
            },
            frameReady : function(editor){
                editor.setContent(qtiEdit.buildQtiElementPlaceholders(editor.getContent()));
                //the binding require the modified html data to be ready
                htmlEditor.bindElementLinkListener();
            },
            unsetHTMLview : function(){
                htmlEditor.bindElementLinkListener();
            },
            beforeSetContent : function(content){
                return qtiEdit.buildQtiElementPlaceholders(content);
            },
            beforeSaveContent : function(content){
                return qtiEdit.restoreQtiElementPlaceholders(content);
            },
            afterGetContent : function(content){
                return qtiEdit.restoreQtiElementPlaceholders(content);
            }
        };
    },
    rebuildQtiElementPlaceholders : function(callbacks){
        var between = (typeof callbacks.between === 'function') ? callbacks.between : null;

        var content = this.getContent();

        if(between){
            content = between(content);
        }

        this.$editor.wysiwyg('setContent', content);
        this.bindElementLinkListener();
    },
    bindMathLinkListener : function(){

        var htmlEditor = this;
        var objects = qtiEdit.getEltInFrame('.qti_math_link', this.getEditorDocument());

        for(var i in objects){

            var object = objects[i];
            var serial = $(object).attr('id');
            this.elements[serial] = serial;

            $(object).off('click').on('click', {'serial' : serial}, function(e){
                e.preventDefault();
                new MathEditor(htmlEditor, e.data.serial);
            });

            qtiEdit.makeNoEditable(object);

            //Append the delete button:
            var objectContainer = object.parent('.qti_math_box');
            objectContainer.bind('dragover drop', function(e){
                e.preventDefault();
                return false;
            });
            qtiEdit.makeNoEditable(objectContainer);

            var $deleteButton = $('<span class="qti_object_delete"></span>').appendTo(objectContainer);
            $deleteButton.attr('title', __('Delete Math'));
            $deleteButton.hide();
            $deleteButton.bind('click', {'serial' : serial}, function(e){
                e.preventDefault();
                util.confirmBox(__('Math Element Deletion'), __('Please confirm math element deletion'), {
                    'Delete Object' : function(){
                        htmlEditor.deleteElements([e.data.serial]);
                        $(this).dialog('close');
                    }
                });
                return false;
            });
            $deleteButton.bind('mousedown contextmenu', function(e){
                e.preventDefault();
            });

            qtiEdit.makeNoEditable($deleteButton);

            object.parent().hover(function(){
                $(this).children('.qti_object_delete').show();
                if($(this).hasClass('qti_object_block')){
                    $(this).css('padding-right', '20px');
                }
            }, function(){
                $(this).children('.qti_object_delete').hide();
                if($(this).hasClass('qti_object_block')){
                    $(this).css('padding-right', 0);
                }
            });
        }
    },
    bindObjectLinkListener : function(){

        var htmlEditor = this;

        var objects = qtiEdit.getEltInFrame('.qti_object_link', this.getEditorDocument());
        for(var i in objects){

            var object = objects[i];
            var objectSerial = $(object).attr('id');
            this.elements[objectSerial] = objectSerial;


            $(object).off('click').on('click', {'serial' : objectSerial}, function(event){
                event.preventDefault();
                qtiEdit.ajaxRequest({
                    type : "POST",
                    url : root_url + "taoQTI/QtiAuthoring/editObject",
                    dataType : 'json',
                    data : {
                        type : htmlEditor.type,
                        serial : htmlEditor.serial,
                        objectSerial : event.data.serial
                    },
                    success : function(data){

                        var objectEditor = {};

                        $('<div id="editObjectForm" title="' + data.title + '">' + data.html + '</div>').dialog({
                            modal : true,
                            width : 600,
                            height : 700,
                            buttons : [
                                {
                                    text : __('Save'),
                                    click : function(){
                                        var $form = $(this);
                                        var $eltData = $form.find('input#data');
                                        var $eltWidth = $form.find('input#width');
                                        var $eltHeight = $form.find('input#height');
                                        objectEditor.save($eltData.val(), $eltHeight.val(), $eltWidth.val());
                                    }
                                },
                                {
                                    text : __('Save & Close'),
                                    click : function(){
                                        var $form = $(this);
                                        var $eltData = $form.find('input#data');
                                        var $eltWidth = $form.find('input#width');
                                        var $eltHeight = $form.find('input#height');
                                        var $modal = $(this);
                                        objectEditor.save($eltData.val(), $eltHeight.val(), $eltWidth.val(), function(){
                                            $modal.dialog('close');
                                        });
                                    }
                                },
                                {
                                    text : __('Close'),
                                    click : function(){
                                        $(this).dialog('close');
                                    }
                                }
                            ],
                            close:function(){
                                $(this).empty();
                            },
                            open : function(){

                                var $form = $(this);

                                var $eltData = $form.find('input#data');
                                var $eltWidth = $form.find('input#width');
                                var $eltHeight = $form.find('input#height');

                                //programatically add the tips:
                                $eltData.parent('div').prepend('<p class="qti-form-tip">' + __('Please use mp4 (H.264+AAC) for video and mp3 for audio for maximum cross-browser compability.') + '<br/>(' + __('tips: youtube video works, e.g: http://youtu.be/YJWSVUPSQqw') + ')</p>');
                                $eltWidth.parent('div').append('<span class="qti-form-tip">(' + __('optional') + ')</p>');
                                $eltHeight.parent('div').append('<span class="qti-form-tip">(' + __('optional') + ')</p>');

                                objectEditor.buildPreviewer = function(options){
                                    qtiEdit.buildMediaPreviewer($form.find('div[id=formInteraction_object_preview]'), options);
                                }

                                objectEditor.save = function(newMediaPath, height, width, callback){
                                    qtiEdit.ajaxRequest({
                                        type : "POST",
                                        url : root_url + "taoQTI/QtiAuthoring/editObject",
                                        dataType : 'json',
                                        data : {
                                            type : htmlEditor.type,
                                            serial : htmlEditor.serial,
                                            objectSerial : data.objectSerial,
                                            data : newMediaPath,
                                            height : height,
                                            width : width
                                        },
                                        success : function(data){
                                            if(data.saved){
                                                if(typeof(callback) === 'function'){
                                                    callback();
                                                }else{
                                                    objectEditor.buildPreviewer(data.media);
                                                    $form.find('div#formObject_errors').text('').hide();
                                                }
                                            }else{
                                                $form.find('div#formObject_errors').text(data.errorMessage).show();
                                            }
                                        }
                                    });
                                }

                                $eltData.fmbind({
                                    type : 'image',
                                    showselect : true
                                }, function(elt, newMediaPath, mediaData){

                                    var height = '';
                                    var width = '';
                                    if(mediaData){
                                        if(mediaData.height){
                                            height = mediaData.height;
                                        }
                                        if(mediaData.width){
                                            width = mediaData.width;
                                        }
                                    }

                                    $eltData.val(newMediaPath);
                                    $eltWidth.val(width);
                                    $eltHeight.val(height);

                                    objectEditor.save(newMediaPath, height, width);
                                });

                                if(data.media.data && data.media.type){
                                    objectEditor.buildPreviewer(data.media);
                                }

                            }
                        });

                    }
                });
            });

            qtiEdit.makeNoEditable(object);

            //Append the delete button:
            var objectContainer = object.parent('.qti_object_box');
            objectContainer.bind('dragover drop', function(e){
                e.preventDefault();
                return false;
            });
            qtiEdit.makeNoEditable(objectContainer);

            var $deleteButton = $('<span class="qti_object_delete"></span>').appendTo(objectContainer);
            $deleteButton.attr('title', __('Delete object'));
            $deleteButton.hide();
            $deleteButton.bind('click', {'objectSerial' : objectSerial}, function(e){
                e.preventDefault();
                util.confirmBox(__('Object Deletion'), __('Please confirm object deletion'), {
                    'Delete Object' : function(){
                        htmlEditor.deleteElements([e.data.objectSerial]);
                        $(this).dialog('close');
                    }
                });
                return false;
            });
            $deleteButton.bind('mousedown contextmenu', function(e){
                e.preventDefault();
            });

            qtiEdit.makeNoEditable($deleteButton);

            object.parent().hover(function(){
                $(this).children('.qti_object_delete').show();
            }, function(){
                $(this).children('.qti_object_delete').hide();
            });
        }
    }
});
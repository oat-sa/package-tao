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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
interactionClass.instances = [];

function interactionClass(interactionSerial, relatedItemSerial, options){

    if(!interactionSerial){
        throw 'no interaction serial found';
    }
    if(!relatedItemSerial){
        throw 'no related item serial found';
    }

    var defaultResponseFormContainer = {
        //responseMappingOptionsFormContainer : '#qtiAuthoring_mapping_container',
        responseGrid : 'qtiAuthoring_response_grid'
    };

    var settings = {};

    $.extend(settings, defaultResponseFormContainer, options);
    this.settings = settings;
    //this.responseMappingOptionsFormContainer = settings.responseMappingOptionsFormContainer;
    this.responseGrid = settings.responseGrid;

    this.interactionSerial = interactionSerial;
    this.relatedItemSerial = relatedItemSerial;

    //var responseMappingMode = false;//default:false
    //this.setResponseMappingMode(responseMappingMode);
    this.choiceAutoSave = true;

    this.$form = $('#InteractionForm');
    if(!this.$form.length){
        throw 'cannot find the interaction form';
    }

    this.type = 'unknown';
    this.choices = [];
    this.modifiedInteraction = false;
    this.modifiedChoices = [];

    this.initInteractionFormSubmitter();

    var __this = this;
    var initChoicesAndResponse = function(){
        //always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode) deprecated!!!
        // this.loadResponseMappingForm();

        //load choices form if necessary:
        if(settings.choicesFormContainer){
            __this.choicesFormContainer = settings.choicesFormContainer;
            __this.loadChoicesForm(__this.choicesFormContainer);
        }else{
            //immediately set the form change listener (no need to wait for the choice forms)
            __this.setFormChangeListener();

            //and load the response form and grid:
            require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                new responseClass(__this.responseGrid, __this);
            });
        }
    };

    //append file manager button
    this.fmbind();

    if(settings.shapeEditorOptions && settings.shapeEditorOptions.backgroundImagePath){
        var backgroundImagePath = settings.shapeEditorOptions['backgroundImagePath'];
        delete settings.shapeEditorOptions['backgroundImagePath'];
        settings.shapeEditorOptions.onReady = function(){
            initChoicesAndResponse();
        };
        this.buildShapeEditor(backgroundImagePath, settings.shapeEditorOptions);
    }else if(settings.mediaPreviewerOptions && settings.mediaPreviewerOptions.data){
        this.buildMediaPreviewer(settings.mediaPreviewerOptions);
        this.setFormChangeListener();
    }else{
        initChoicesAndResponse();
    }

    interactionClass.instances[interactionSerial] = this;
    this.getRelatedItem().currentInteraction = this;
}

interactionClass.prototype.setType = function(type){
    this.type = type.toLowerCase();
}

interactionClass.prototype.initInteractionFormSubmitter = function(){

    var _this = this;
    $("a.interaction-form-submitter").off('.qtiAuthoring').on('click.qtiAuthoring', function(){
        _this.saveInteraction();
        _this.saveModifiedChoices();
        return false;
    });

    $('#qtiAuthoring_item_editor_button').off('.qtiAuthoring').on('click.qtiAuthoring', function(){

        var $elt = $(this);
        var switchTabFunction = function(){
            if($myTab)
                $myTab.tabs("select", 0);
            $elt.hide();
            $('#qtiAuthoring_menu_interactions').show();
            $('#qtiAuthoring_interaction_container').empty();
        }

        var relatedItem = _this.getRelatedItem();
        if(relatedItem){
            relatedItem.saveCurrentInteraction(switchTabFunction);
        }else{
            switchTabFunction();
        }

        return false;

    }).show();

    $('#qtiAuthoring_menu_interactions').hide();
}

interactionClass.prototype.saveModifiedChoices = function(){
    for(var choiceSerial in this.modifiedChoices){
        var $choiceForm = $('#' + choiceSerial);

        //serialize+submit:
        if($choiceForm.length){
            this.saveChoice($choiceForm);
        }
    }
}

interactionClass.prototype.saveAll = function(callback, reloadResponse){

    if(this.response && this.response.modifiedResponseOptions){
        this.response.saveResponse();
    }

    this.saveModifiedChoices();

    if(reloadResponse == 'undefined')
        reloadResponse = true;
    if(this.modifiedChoices.length || this.modifiedInteraction){
        this.saveInteraction({
            'reloadResponse' : reloadResponse
        });
    }

    var timer = null;
    var stopTimer = function(){
        window.clearInterval(timer);
    }
    var successFunction = function(){
        if(typeof callback == 'function'){
            callback.apply();
        }
        stopTimer();
    }
    //check every half a second if all choices have been saved:
    var i = 1;
    var interaction = this;
    timer = window.setInterval(function(){
        if(!interaction.modifiedChoices.length && !interaction.modifiedInteraction){
            if(!interaction.response || interaction.response && !interaction.response.modifiedResponseOptions){
                successFunction();
            }
        }
        i++;
        if(i > 30){
            //save failed
            stopTimer();
        }
    }, 100);
}

interactionClass.prototype.saveInteraction = function(userOptions){

    //@todo: check unicity of the id?
    if(!this.$form.length){
        throw 'cannot save the interaction from the interaction forms because it does not exist';
    }

    var interactionProperties = this.$form.serializeObject();

    //filter the prompt html area field:
    if(interactionProperties.prompt){
        interactionProperties.prompt = util.htmlEncode(interactionProperties.prompt);
    }

    //check if it is required to save data (hotText and gapMatch interactions):
    if(this.interactionDataContainer){
        if($(this.interactionDataContainer).length && this.interactionEditor.length){
            //there is a wysiwyg editor that contains the interaciton data:
            interactionProperties.interactionData = util.htmlEncode(this.interactionEditor.wysiwyg('getContent'));
        }
    }

    var defaultOptions = {
        'async' : true,
        'reloadResponse' : false
    };
    var options = $.extend(defaultOptions, userOptions);

    var interaction = this;

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/saveInteraction",
        data : interactionProperties,
        // async: options.async,
        dataType : 'json',
        success : function(r){
            if(r.saved){
                qtiEdit.createInfoMessage(__('Modification(s) on interaction has been updated'));
                interaction.setModifiedInteraction(false);

                if(r.reloadResponse && options.reloadResponse){
                    require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                        new responseClass(interaction.responseGrid, interaction);
                    });
                }else{
                    //setMaxChoices to response
                    var maxChoices = interactionProperties.maxAssociations || interactionProperties.maxChoices;
                    if(typeof maxChoices != 'undefined' && interaction.response){
                        interaction.response.setMaxChoices(maxChoices);
                    }
                }

                if(r.newGraphicObject){
                    if(r.newGraphicObject.data){
                        if(interaction.shapeEditor){
                            interaction.shapeEditor.setBackground(r.newGraphicObject.data, r.newGraphicObject.width, r.newGraphicObject.height);
                        }else{
                            interaction.buildShapeEditor(r.newGraphicObject.data, {width : r.newGraphicObject.width, height : r.newGraphicObject.height});
                            interaction.setShapeEditListener();
                        }
                    }else{
                        if(r.newGraphicObject.errorMessage){
                            interaction.setModifiedInteraction(true);
                            helpers.createErrorMessage(__('Error in background file:') + '<br/>' + r.newGraphicObject.errorMessage);
                        }
                    }
                }else if(r.newMediaObject){
                    if(r.newMediaObject.errorMessage){
                        interaction.setModifiedInteraction(true);
                        helpers.createErrorMessage(__('Error in media file file:') + '<br/>' + r.newMediaObject.errorMessage);
                    }else{
                        interaction.buildMediaPreviewer(r.newMediaObject);
                    }

                }

            }else{
                //reload interaction form:
                //need for validation on server side
            }
        }
    });

}

interactionClass.prototype.saveChoice = function($choiceFormContainer){

    var _this = this;
    var $choiceForm = null;

    if($choiceFormContainer.length){

        if($choiceFormContainer[0].nodeName.toLowerCase() == 'form'){
            $choiceForm = $choiceFormContainer;
        }else{
            $choiceForm = $choiceFormContainer.find('form');
        }
    }

    if($choiceForm){
        if($choiceForm.length){

            var choiceProperties = $choiceForm.serializeObject();
            if(choiceProperties.data){
                var choiceData = choiceProperties.data;//keep un altered data string
                choiceProperties.data = util.htmlEncode(choiceProperties.data);
            }
            choiceProperties.interactionSerial = this.interactionSerial;

            var interaction = this;
            qtiEdit.ajaxRequest({
                type : "POST",
                url : root_url + "taoQTI/QtiAuthoring/saveChoice",
                data : choiceProperties,
                dataType : 'json',
                success : function(r){

                    if(!r.saved){
                        helpers.createErrorMessage(__('The choice cannot be saved'));
                    }else{
                        qtiEdit.createInfoMessage(__('Modification on choice applied'));
                        delete interaction.modifiedChoices['ChoiceForm_' + r.choiceSerial];

                        //only if it is the last updated choice!
                        if(!interaction.modifiedChoices.length){
                            //only when the identifier has changed:
                            if(r.identifierUpdated){
                                require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                                    //reload the response grid tu update the identifier
                                    new responseClass(interaction.responseGrid, interaction);
                                });
                            }
                        }

                        //for hottext interaction, update the input value:
                        if(_this.interactionEditor && choiceData){
                            var $choiceInputs = qtiEdit.getEltInFrame('input#' + choiceProperties.choiceSerial);
                            if($choiceInputs.length){
                                $choiceInputs[0].val(choiceData);
                            }
                        }

                        if(r.errorMessage){
                            helpers.createErrorMessage(r.errorMessage);
                        }
                    }
                }
            });

        }
    }

}

interactionClass.prototype.loadResponseOptionsForm = function(){
    var relatedItem = this.getRelatedItem();
    var interaction = this;

    if(relatedItem){
        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/editOptions",
            data : {
                'interactionSerial' : this.interactionSerial
            },
            dataType : 'html',
            success : function(form){

                var $formContainer = $(interaction.responseOptionsFormContainer);
                // TODO: change by call of function setResponseOptionsMode()
                $formContainer.html(form);
                if(interaction.responseOptionsMode){
                    $formContainer.show();
                }else{
                    $formContainer.hide();
                }
            }
        });
    }else{
        throw 'the related item cannot be found';
    }

}

interactionClass.prototype.getRelatedItem = function(strict){
    if(qtiEdit.instances[this.relatedItemSerial]){
        return qtiEdit.instances[this.relatedItemSerial];
    }
    if(strict){
        throw 'no related item found';
    }
    return null;
}

interactionClass.prototype.loadChoicesForm = function(containerSelector){
    if(!containerSelector){
        containerSelector = '';
        if(this.choicesFormContainer){
            containerSelector = this.choicesFormContainer;
        }
    }
    var interactionSerial = this.interactionSerial;
    var interaction = this;

    if($(containerSelector).length){
        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/editChoices",
            data : {
                'interactionSerial' : interactionSerial
            },
            dataType : 'html',
            success : function(form){
                var $formContainer = $(containerSelector);
                $formContainer.html(form);

                //populate choices local model:
                interaction.loadChoiceDataFromChoiceForm($formContainer);//parse form data to get choice data

                qtiEdit.initFormElements($formContainer);
                interaction.setFormChangeListener();
                interaction.setShapeEditListener();

                require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                    //reload the grid:
                    new responseClass(interaction.responseGrid, interaction);
                });
            }
        });
    }

}

//@TODO: replace the form based data filing by a clean data model
interactionClass.prototype.loadChoiceDataFromChoiceForm = function($choiceFormContainer){

    var __this = this;

    //group and choice are both considered choices in QTI 
    $choiceFormContainer.find('input#choiceSerial').each(function(){

        var serial = $(this).val();
        __this.choices[serial] = {};//init interaction's internal choice object

        $(this).siblings('div').each(function(){

            var choiceIdElt = $(this).find('input#choiceIdentifier');
            if(choiceIdElt.length){
                var id = $(choiceIdElt).val();
                __this.setInteractionChoiceAttribute(serial, 'identifier', id);
                qtiEdit.idList.set(serial, id);
            }

            var choiceShapeElt = $(this).find('select#shape');
            if(choiceShapeElt.length){
                var shape = $(choiceShapeElt[0]).val();
                __this.setInteractionChoiceAttribute(serial, 'shape', shape);
            }
        });

    });
}

interactionClass.prototype.getInteractionChoiceAttribute = function(choiceSerial, attribute){
    var returnValue = null;
    if(this.choices[choiceSerial] && this.choices[choiceSerial][attribute]){
        returnValue = this.choices[choiceSerial][attribute];
    }
    return returnValue;
}

interactionClass.prototype.setInteractionChoiceAttribute = function(choiceSerial, attribute, value){
    var returnValue = false;
    if(this.choices[choiceSerial]){
        this.choices[choiceSerial][attribute] = value;
        returnValue = true;
    }
    return returnValue;
}

interactionClass.prototype.setShapeEditListener = function(target){

    if(this.shapeEditor){

        var interaction = this;

        //define the function:
        var setListener = function($choiceForm){

            if($choiceForm.length){
                var choiceSerial = $choiceForm.attr('id');
                choiceSerial = choiceSerial.replace('GroupForm_', '');
                choiceSerial = choiceSerial.replace('ChoiceForm_', '');

                var $qtiShapeCombobox = $choiceForm.find('.qti-shape').bind('change', {choiceSerial : choiceSerial}, function(e){

                    var cSerial = e.data.choiceSerial;
                    var newShape = $(this).val();
                    var oldShape = interaction.getInteractionChoiceAttribute(cSerial, 'shape');

                    if(newShape != oldShape){
                        interaction.shapeEditor.removeShapeObj(cSerial);
                        interaction.shapeEditor.startDrawing(cSerial, newShape);
                        interaction.setInteractionChoiceAttribute(cSerial, 'shape', newShape);
                    }

                    $(this).siblings('img.shapeEditorPencil').each(function(){
                        if(interaction.getInteractionChoiceAttribute(choiceSerial, 'shape') == 'default'){
                            $(this).hide();
                        }else{
                            $(this).show();
                        }
                    });
                });

                //append the edit button:
                var $imageLink = $('<img src="' + img_url + 'pencil.png"/>').insertAfter($qtiShapeCombobox);
                $imageLink.attr('title', __('draw it'));
                $imageLink.addClass('shapeEditorPencil');
                $imageLink.bind('click', {choiceSerial : choiceSerial, defaultShape : $qtiShapeCombobox.val()}, function(e){
                    var shape = $(this).siblings('.qti-shape').val();
                    interaction.shapeEditor.startDrawing(e.data.choiceSerial, shape);
                });

                //init pencil show/hide according to default shape:
                if(interaction.getInteractionChoiceAttribute(choiceSerial, 'shape') == 'default'){
                    $imageLink.hide();
                }

                //check if the coords are not empty, if so, draw the shape:
                var $qtiCoordsInput = $choiceForm.find('input[name=coords]');
                if($qtiCoordsInput.length){
                    if($qtiCoordsInput.val() && $qtiShapeCombobox.val()){
                        interaction.shapeEditor.createShape(choiceSerial, 'qti', {data : $qtiCoordsInput.val(), shape : $qtiShapeCombobox.val()});
                        interaction.shapeEditor.exportShapeToCanvas(choiceSerial);
                    }
                }

                //finally add the hover/blur control:
                $choiceForm.parents('div.formContainer_choice').hover(function(){
                    // var choiceSerial = $(this).attr('id');
                    interaction.shapeEditor.hoverIn($(this).attr('id'));
                }, function(){
                    interaction.shapeEditor.hoverOut($(this).attr('id'));
                });
            }

        }

        if(!target){
            //all choices:
            $('div.formContainer_choice').find('form').each(function(){
                setListener($(this));
            });
        }else{
            if(true){
                setListener($(target));
            }
        }
    }

}


interactionClass.prototype.addChoice = function(number, $appendTo, containerClass, setId){

    var interaction = this;

    if(!$appendTo || !$appendTo.length){
        throw 'the append target element do not exists';
    }

    var addChoice = function($appendTo, containerClass, setId){

        var postData = {};
        postData.interactionSerial = interaction.interactionSerial;

        if(setId !== undefined){
            postData.setId = setId;
        }

        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/addChoice",
            data : postData,
            async : false,
            dataType : 'json',
            success : function(r){
                if(r.added){

                    var $newFormElt = $('<div/>');
                    $newFormElt.attr('id', r.choiceSerial);
                    $newFormElt.attr('class', containerClass);
                    $newFormElt.append(r.choiceForm);
                    $appendTo.append($newFormElt);

                    //populate new choice into local data:
                    interaction.loadChoiceDataFromChoiceForm($newFormElt);

                    $newFormElt.hide();
                    interaction.initToggleChoiceOptions();
                    $newFormElt.show();

                    qtiEdit.initFormElements($newFormElt);
                    interaction.setFormChangeListener('#' + r.choiceSerial);
                    interaction.setShapeEditListener('#' + r.choiceSerial);

                }

            }
        });
    }

    this.saveModifiedChoices();

    var timer = null;
    var stopTimer = function(){
        if(!number){
            number = 1;
        }
        clearTimeout(timer);
        for(var i = 0; i < number; i++){
            addChoice($appendTo, containerClass, setId);
        }

        //rebuild the response grid, only after all choices have been added:
        require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
            new responseClass(interaction.responseGrid, interaction);
        });
    }

    //check every half a second if all choices have been saved:
    timer = setTimeout(function(){
        if(!interaction.modifiedChoices.length){
            stopTimer();
        }
    }, 500);

}

interactionClass.prototype.initToggleChoiceOptions = function(options){
    var interaction = this;
    $('.form-group').each(function(){
        interaction.toggleChoiceOptions($(this), options);
    });
}

interactionClass.prototype.toggleChoiceOptions = function($group, options){
    var interaction = this;
    var groupId = $group.attr('id');
    if(groupId.indexOf('choicePropOptions') == 0){

        if(!options){
            options = {'delete' : true, 'group' : true};
        }else{
            if(options['delete'] !== false){
                options['delete'] = true;
            }
            if(options.group !== false){
                options.group = true;
            }
        }

        // it is a choice group:
        if($('#a_' + groupId).length){
            $('#a_' + groupId).remove();
        }
        if($('#delete_' + groupId).length){
            $('#delete_' + groupId).remove();
        }

        if(options['delete']){
            var $deleteElt = $('<span id="delete_' + groupId + '" title="' + __('Delete choice') + '" class="form-group-control choice-button-delete ui-icon ui-icon-circle-close"></span>');
            $group.before($deleteElt);
            // $deleteElt.css('position', 'relative');

            //add click event listener:
            $('#delete_' + groupId).click(function(){

                var choiceSerial = $(this).attr('id').replace('delete_choicePropOptions_', '');
                util.confirmBox(__('Choice Deletion'), __('Do you want to delete the choice?'), {
                    'Delete Choice' : function(){
                        interaction.deleteChoice(choiceSerial);
                        $(this).dialog('close');
                    }
                });
            });
        }

        if(options.group){

            var $buttonElt = $('<span id="a_' + groupId + '" title="' + __('Advanced Properties') + '" class="form-group-control choice-button-advanced ui-icon ui-icon-circle-plus"></span>');
            $group.before($buttonElt);
            $group.hide();

            $('#a_' + groupId).toggle(function(){
                $(this).switchClass('ui-icon-circle-plus', 'ui-icon-circle-minus');
                $group.slideToggle();
            }, function(){
                $(this).switchClass('ui-icon-circle-minus', 'ui-icon-circle-plus');
                $group.slideToggle();
            });
        }

    }
}

interactionClass.prototype.setModifiedChoicesByForm = function($modifiedForm){
    if($modifiedForm.length){
        var id = $modifiedForm.attr('id');
        if(id.indexOf('ChoiceForm') == 0){
            this.modifiedChoices[id] = 'modified';//it is a choice form:
            this.setModifiedInteraction(true);//the interaction must be correctly saved
        }else if(id.indexOf('InteractionForm') == 0){
            this.setModifiedInteraction(true);
        }
    }
}

//define when the form is considered as changed:
interactionClass.prototype.setFormChangeListener = function(target){
    var interaction = this;
    var $choiceForm = null;

    if(!target){
        $choiceForm = $('form');//all forms
    }else{
        if(!$(target).length){
            return false;
        }else{
            var $choiceFormContainer = $(target);
            if($choiceFormContainer[0].nodeName.toLowerCase() == 'form'){
                $choiceForm = $choiceFormContainer;
            }else{
                $choiceForm = $choiceFormContainer.find('form');
            }
        }
    }

    $choiceForm.children().off('.qtiAuthoring').on('change.qtiAuthoring paste.qtiAuthoring keyup.qtiAuthoring', function(){
        var $modifiedForm = $(this).parents('form');
        interaction.setModifiedChoicesByForm($modifiedForm);
    });

    setTimeout(function(){
        $choiceForm.find('iframe').each(function(){
            var $modifiedForm = $(this).parents('form');
            var setChangesfunction = function(){
                interaction.setModifiedChoicesByForm($modifiedForm);
            };

            // contentWindow does not exist under google chrome and other browsers.
            var contentW;
            if(this.contentDocument){
                contentW = this.contentDocument;
            }else if(this.contentWindow){
                contentW = this.contentWindow.document;
            }else{
                contentW = this.document;
            }

            $(contentW).off('.qtiAuthoring').on('click.qtiAuthoring focus.qtiAuthoring', setChangesfunction); //focusin
            $(this).siblings('ul').off('.qtiAuthoring').on('click.qtiAuthoring', setChangesfunction);
        });
    }, 500);//hack to ensure that the wysiwyg editor had time to be initialized

    return true;
}

interactionClass.prototype.deleteChoice = function(choiceSerial, reloadInteraction){

    var interaction = this;
    var oldChoice = this.choices[choiceSerial];
    delete this.choices[choiceSerial];

    if(!reloadInteraction){
        reloadInteraction = false;
    }

    if(this.type === 'gapmatch' || this.type === 'hottext'){
        //@todo set new data at the same time
    }

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/deleteChoice",
        data : {
            'choiceSerial' : choiceSerial,
            'reloadInteraction' : reloadInteraction,
            'interactionSerial' : interaction.interactionSerial
        },
        dataType : 'json',
        success : function(r){
            if(r.deleted){
                if(r.reloadInteraction){
                    interaction.reload();
                    return;
                }

                $('#' + choiceSerial).remove();
                qtiEdit.idList.unset(choiceSerial);

                //delete the shape, if exists:
                if(interaction.shapeEditor){
                    interaction.shapeEditor.removeShapeObj(choiceSerial);
                }

                require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                    //TODO: need to be optimized: only after the last choice saving
                    new responseClass(interaction.responseGrid, interaction);
                });
            }else{
                interaction.choices[choiceSerial] = oldChoice;
            }
        }
    });
}

interactionClass.prototype.reload = function(){
    this.getRelatedItem(true).loadInteractionForm(this.interactionSerial);
}

interactionClass.prototype.buildInteractionEditor = function(interactionDataContainerSelector, options){

    //re-init the interaction editor object:
    this.interactionEditor = new Object();

    //interaction data container selector:
    this.interactionDataContainer = interactionDataContainerSelector;

    var interaction = this;
    var controls = {
        strikeThrough : {visible : false},
        underline : {visible : false},
        insertTable : {visible : false},
        h1 : {visible : true},
        h2 : {visible : true},
        h3 : {visible : true},
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
        removeFormat : {visible : false},
        html : getCustomControl('customHTML'),
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
        addMediaInteraction : {visible : false},
        addEndAttemptInteraction : {visible : false},
        createHotText : {visible : false},
        createGap : {visible : false},
        addObject : {visible : false},
        addMath : {visible : false},
        saveItemData : {visible : false}
    };

    var events = {
        keyup : function(e){
            if(interaction.getDeletedChoices(true).length > 0){
                if(util.msie()){//undo unavailable in msie
                    var deletedChoices = interaction.getDeletedChoices();
                    for(var key in deletedChoices){
                        //delete choices one by one:
                        interaction.deleteChoice(deletedChoices[key]);
                    }
                }else{
                    util.confirmBox(__('Choice Deletion'), __('please confirm deletion of the choice(s)'), {
                        'Cancel' : function(){
                            interaction.interactionEditor.wysiwyg('undo');
                            $(this).dialog('close');
                        },
                        'Delete Choice' : function(){
                            var deletedChoices = interaction.getDeletedChoices();
                            for(var key in deletedChoices){
                                //delete choices one by one:
                                interaction.deleteChoice(deletedChoices[key]);
                            }
                            $(this).dialog('close');
                        }
                    });
                }
                return false;
            }
            return true;
        }
    };

    if(options.extraControls){
        controls = $.extend(controls, options.extraControls);
    }

    if(options.extraEvents){
        events = $.extend(events, options.extraEvents);
    }

    this.interactionEditor = $(this.interactionDataContainer).wysiwyg({
        iFrameClass : 'wysiwyg-interaction',
        css : qtiEdit.getFrameCSS(),
        controls : controls,
        events : events
    });

}

interactionClass.buildHottextPlaceholder = function(htmlString){

    var regex = new RegExp('{{qtiHottext:([\\w]+):([^{]*)}}', 'img');
    htmlString = htmlString.replace(regex,
        function(original, serial, hottextValue){
            hottextValue = hottextValue.replace(/(['"<>=]*)/ig, '');
            var returnValue = '';
            returnValue += '<span id="' + serial + '" class="qti_hottext_box qti_choice_box"><input id="' + serial + '" class="qti_choice_link" value="' + hottextValue + '" type="button"/><span></span></span>'
            returnValue += '&nbsp;';
            return returnValue;
        });

    return htmlString;

}

interactionClass.restoreHottextPlaceholder = function(htmlString){

    var regex = '';
    if(util.msie()){//msie
        regex = new RegExp('<span[^<]*id=([^<"]*)\\s[^<]*class="[^<"]*qti_hottext_box[^<"]*"[^<]*>[^<>]*(<BR>[^<>]*)*[^<>]*<input[^<]*value=("[^<"]*"|[^<"\\s]*)[^<]*>[^<>]*(<span[^>]*></span>|<span[^>]*/>)*[^<>]*</span>(&nbsp;)?', 'img');
    }else{
        regex = new RegExp('<span[^<]*id="([^<"]*)"[^<]*class="[^<"]*qti_hottext_box[^<"]*"[^<]*>[^<>]*(<br>[^<>]*)*[^<>]*<input[^<]*value="([^<"]*)"[^<]*>[^<>]*(<span[^>]*></span>|<span[^>]*/>)*[^<>]*</span>(&nbsp;)?', 'img');
    }

    htmlString = htmlString.replace(regex,
        function(original, serial, br, hottextValue){
            //@todo: save hot text here
            var returnValue = '{{qtiHottext:' + serial + ':' + hottextValue + '}}';
            return returnValue;
        });

    return htmlString;
}

interactionClass.buildGapPlaceholder = function(htmlString){

    var regex = new RegExp('{{qtiGap:([\\w]+):([^{]*)}}', 'img');
    htmlString = htmlString.replace(regex,
        function(original, serial, gapId){
            gapId = gapId.replace(/(['"<>=]*)/ig, '');
            var returnValue = '';
            returnValue += '<span id="' + serial + '" class="qti_gap_box qti_choice_box"><input id="' + serial + '" class="qti_choice_link" value="' + gapId + '" type="button"/><span></span></span>'
            returnValue += '&nbsp;';
            return returnValue;
        });

    return htmlString;

}

interactionClass.restoreGapPlaceholder = function(htmlString){

    var regex = '';
    if(util.msie()){//msie
        regex = new RegExp('<span[^<]*id=([^<"]*)\\s[^<]*class="[^<"]*qti_gap_box[^<"]*"[^<]*>[^<>]*(<BR>[^<>]*)*[^<>]*<input[^<]*value=("[^<"]*"|[^<"\\s]*)[^<]*>[^<>]*(<span[^>]*></span>|<span[^>]*/>)*[^<>]*</span>(&nbsp;)?', 'img');
    }else{
        regex = new RegExp('<span[^<]*id="([^<"]*)"[^<]*class="[^<"]*qti_gap_box[^<"]*"[^<]*>[^<>]*(<br>[^<>]*)*[^<>]*<input[^<]*value="([^<"]*)"[^<]*>[^<>]*(<span[^>]*></span>|<span[^>]*/>)*[^<>]*</span>(&nbsp;)?', 'img');
    }

    htmlString = htmlString.replace(regex,
        function(original, serial, br, gapId){
            gapId = gapId.replace('"', '');//msie compatiblility
            //@todo: save hot text here
            var returnValue = '{{qtiGap:' + serial + ':' + gapId + '}}';
            return returnValue;
        });

    return htmlString;

}
interactionClass.prototype.fmbind = function(){

    var __this = this;

    if($.fn.fmbind && this.$form){

        this.$form.find('input.qti-file-img-interaction').each(function(){

            $(this).width('60%');

            $(this).fmbind({
                type : 'image',
                showselect : true
            }, function(elt, newMediaPath, mediaData){

                //need to redefine the scope of $modifiedForm, $eltHeight and $eltWidth
                var $modifiedForm = $(elt).parents('form');
                var $eltHeight = $modifiedForm.find('input#object_height');
                var $eltWidth = $modifiedForm.find('input#object_width');
                var height, width = '';
                if(mediaData){
                    if(mediaData.height){
                        height = mediaData.height;
                    }
                    if(mediaData.width){
                        width = mediaData.width;
                    }
                }

                $(elt).val(newMediaPath).change();
                $eltHeight.val(height).change();
                $eltWidth.val(width).change();

                __this.saveAll(function(){
                    __this.reload();
                }, false);

            });
        });
    }
}

interactionClass.prototype.buildMediaPreviewer = function(mediaAttributes){
    qtiEdit.buildMediaPreviewer($('#formInteraction_object_preview'), mediaAttributes);
}

interactionClass.prototype.buildShapeEditor = function(backgroundImagePath, options){

    var __this = this;

    var defaultOptions = {
        onDrawn : function(choiceSerial, shapeObject, self){
            //export shapeObject to qti:
            if(choiceSerial && shapeObject){
                var qtiCoords = self.exportShapeToQti(choiceSerial);
                if(qtiCoords){
                    var $choiceGroupForm = $('#' + choiceSerial).find('form');
                    if($choiceGroupForm.length){
                        $choiceGroupForm.find('input[name=coords]').val(qtiCoords);
                        __this.setModifiedChoicesByForm($choiceGroupForm);
                    }else{
                        //throw 'no choice or group form found';
                    }
                }
            }
        }
    };

    var shapeEditorOption = $.extend(defaultOptions, options);

    var myShapeEditor = new qtiShapesEditClass(
        'formInteraction_object',
        backgroundImagePath,
        shapeEditorOption
        );

    //add slider to size img in a more user friendly way
    var $eltHeight = $('form#InteractionForm').find('input#object_height');
    var $eltWidth = $('form#InteractionForm').find('input#object_width');

    //record initial dimension:
    var height = $eltHeight.val();
    var width = $eltWidth.val();
    //append slider for a more user friendly way of img resizing
    var $slider = $('<div class="qti-img-resize-slider"></div>').appendTo($('#formInteraction_object_container'));
    $slider.slider({
        range : "min",
        value : 100,
        min : 10,
        max : 200,
        slide : function(e, ui){
            var percentage = ui.value;
            var newHeight = Math.round(percentage / 100 * height);
            var newWidth = Math.round(percentage / 100 * width);
            $eltHeight.val(newHeight);
            $eltWidth.val(newWidth);
            myShapeEditor.setSize(newWidth, newHeight);
        },
        stop : function(e, ui){
            $eltHeight.change();//trigger change event
            $eltWidth.change();
        }
    });

    if(myShapeEditor){

        $('#formInteraction_object_container_title').find('span.qti-img-preview-label').text(__('preview (1:1)'));

        //map choices to the shape editor:
        this.shapeEditor = myShapeEditor;

    }
}

/*
 *Method used to check if a choice has been deleted in the interaction wysiwyg data editor (for gapmatch and hottext only)
 */
interactionClass.prototype.getDeletedChoices = function(one){
    var deletedChoices = [];
    var interactionData = $(this.interactionDataContainer).val();//TODO: improve with the use of regular expressions:
    for(var choiceSerial in this.choices){

        var doCheck = false;
        if(this.type === 'gapmatch' && choiceSerial.indexOf('gap_') > 0){
            doCheck = true;
        }else if(this.type === 'hottext'){
            doCheck = true;
        }else{
            break;
        }

        if(doCheck && interactionData.indexOf(choiceSerial) < 0){
            //not found so considered as deleted:
            deletedChoices.push(choiceSerial);
            if(one){
                return deletedChoices;
            }
        }
    }

    return deletedChoices;
}

/**
 *idem as adding gap in gapmatch
 */
interactionClass.prototype.addHottext = function(interactionData, $appendTo){
    var interactionSerial = this.interactionSerial;
    var interaction = this;

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/addGap",
        data : {
            'interactionSerial' : interactionSerial,
            'interactionData' : util.htmlEncode(interaction.interactionEditor.wysiwyg('getContent'))
        },
        dataType : 'json',
        success : function(r){
            //set the content:
            interaction.interactionEditor.wysiwyg('setContent', $("<div/>").html(r.interactionData).html());

            //then add listener
            interaction.bindChoiceLinkListener();

            //add choice form:
            var $newFormElt = $('<div/>');
            $newFormElt.attr('id', r.choiceSerial);
            $newFormElt.attr('class', 'formContainer_choice');//hard-coded: bad
            $newFormElt.append(r.choiceForm);

            //populate new choice into local data:
            interaction.loadChoiceDataFromChoiceForm($newFormElt);

            //add to parameter
            if(!$appendTo){
                var $appendTo = $('#formContainer_choices');
            }
            $appendTo.append($newFormElt);

            $newFormElt.hide();
            interaction.initToggleChoiceOptions();//{'delete':false}
            $newFormElt.show();

            qtiEdit.initFormElements($newFormElt);

            interaction.setFormChangeListener('#' + r.choiceSerial);

            //rebuild the response grid:
            require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                new responseClass(interaction.responseGrid, interaction);
            });
        }
    });
}

interactionClass.prototype.addGap = function(number, interactionData, $appendTo){

    var interaction = this;

    var addGap = function(interactionData, $appendTo){

        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/addGap",
            data : {
                'interactionSerial' : interaction.interactionSerial,
                'interactionData' : interactionData
            },
            dataType : 'json',
            success : function(r){

                if(interaction.interactionEditor){//gapmatch interaction only
                    //set the content:
                    interaction.interactionEditor.wysiwyg('setContent', $("<div/>").html(r.interactionData).html());

                    //then add listener
                    interaction.bindChoiceLinkListener();//ok keep
                }

                //add choice form:
                var $newFormElt = $('<div/>');
                $newFormElt.attr('id', r.choiceSerial);
                $newFormElt.attr('class', 'formContainer_choice');//hard-coded: bad
                $newFormElt.append(r.choiceForm);

                //populate new choice into local data:
                interaction.loadChoiceDataFromChoiceForm($newFormElt);

                //add to parameter
                if(!$appendTo){
                    var $appendTo = $('#formContainer_groups');//append to group!
                }
                $appendTo.append($newFormElt);

                $newFormElt.hide();
                interaction.initToggleChoiceOptions((interaction.type === 'gapmatch') ? {/*'delete': false*/} : {});
                $newFormElt.show();

                interaction.setFormChangeListener('#' + r.choiceSerial);
                //no shape listener for gap and hottext choices
            }
        });

    }

    //get directly interaction data from the editor:
    var interactionData = '';
    if(this.interactionEditor)
        interactionData = util.htmlEncode(this.interactionEditor.wysiwyg('getContent'));

    this.saveModifiedChoices();

    var timer = null;
    var stopTimer = function(){
        if(!number){
            number = 1;
        }
        for(var i = 0; i < number; i++){
            addGap(interactionData, $appendTo);
        }

        //rebuild the response grid only when all groups have been added
        require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
            new responseClass(interaction.responseGrid, interaction);
        });

        clearTimeout(timer);
    }
    //check every half a second if all choices have been saved:
    timer = setTimeout(function(){
        if(!interaction.modifiedChoices.length){
            stopTimer();
        }
    }, 500);

}

interactionClass.prototype.bindChoiceLinkListener = function(){

    //destroy all listeners:

    //reset the choice array:
    this.choices = [];

    var links = qtiEdit.getEltInFrame('.qti_choice_link');
    for(var i in links){

        var choiceSerial = links[i].attr('id');

        this.choices[choiceSerial] = {};

        links[i].unbind('click').click(function(){
            //focus the clicked choice form:
            window.location.hash = '#' + $(this).attr('id');

        });

        qtiEdit.makeNoEditable(links[i]);
        qtiEdit.makeNoEditable(links[i].parent('div'));
        links[i].parent('div').click(function(e){
            e.preventDefault();
        });
    }

}

interactionClass.prototype.setResponseOptionsMode = function(optionsMode){
    this.responseOptionsMode = optionsMode;
    switch(optionsMode){
        case 'manual':
            break;

        case 'map':
            if(this.responseOptionsMode != 'map'){
                //display the scoring form: //TODO: load it only when necessary:
                //this.responseMappingMode = true;
                //$('#qtiAuthoring_mappingEditor').show();

                require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                    //reload the response grid, to update column model:
                    new responseClass(this.responseGrid, this);
                });
            }
            break;

        case 'correct':
            //this.responseMappingMode = false;
            //$('#qtiAuthoring_mappingEditor').hide();

            require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                //reload the response grid, to update column model:
                new responseClass(this.responseGrid, this);
            });
            break;
    }
}

interactionClass.prototype.setModifiedInteraction = function(modified){
    if(modified){
        this.modifiedInteraction = true;
        $('a.interaction-form-submitter').addClass('form-submitter-emphasis');
    }else{
        this.modifiedInteraction = false;
        $('a.interaction-form-submitter').removeClass('form-submitter-emphasis');
    }
}

interactionClass.prototype.isModified = function(){
    return this.modifiedInteraction;
}
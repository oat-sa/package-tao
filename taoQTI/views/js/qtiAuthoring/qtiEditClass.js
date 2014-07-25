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

qtiEdit.instances = [];

qtiEdit.css = [];

qtiEdit.setFrameCSS = function(css){
    qtiEdit.css = css;
}

qtiEdit.getFrameCSS = function(){
    return qtiEdit.css;
}

function qtiEdit(itemSerial, options){

    var defaultFormContainers = {
        itemDataContainer : '#itemEditor_wysiwyg',
        interactionFormContent : '#qtiAuthoring_interaction_container',
        responseProcessingFormContent : '#qtiAuthoring_processingEditor',
        cssFormContent : '#qtiAuthoring_cssManager',
        responseMappingOptionsFormContainer : '#qtiAuthoring_mapping_container',
        responseGrid : 'qtiAuthoring_response_grid'
    };

    var formContainers = $.extend({}, defaultFormContainers);

    this.interactions = [];
    this.elements = [];
    this.itemSerial = itemSerial;
    this.itemDataContainer = formContainers.itemDataContainer;
    this.interactionFormContent = formContainers.interactionFormContent;
    this.responseProcessingFormContent = formContainers.responseProcessingFormContent;
    this.responseMappingOptionsFormContainer = formContainers.responseMappingOptionsFormContainer;
    this.responseGrid = formContainers.responseGrid;
    this.cssFormContent = formContainers.cssFormContent;

    //init windows options:
    this.windowOptions = 'width=800,height=600,menubar=no,toolbar=no,scrollbars=1';

    this.currentInteraction = null;

    this.itemEditor = new HtmlEditorItem(this, $(this.itemDataContainer));
    this.itemEditor.initEditor();

    this.loadResponseProcessingForm();

    qtiEdit.instances[this.itemSerial] = this;
}

qtiEdit.buildQtiElementPlaceholders = function(html){
    html = qtiEdit.buildInteractionPlaceholder(html);
    html = qtiEdit.buildMathPlaceholder(html);
    return qtiEdit.buildObjectPlaceholder(html);
}

qtiEdit.restoreQtiElementPlaceholders = function(html){
    html = qtiEdit.restoreInteractionPlaceholder(html);
    html = qtiEdit.restoreMathPlaceholder(html);
    return qtiEdit.restoreObjectPlaceholder(html);
}

qtiEdit.makeNoEditable = function($DOMelement){
    if($DOMelement.length){
        $DOMelement.attr('readonly', true);
        $DOMelement.attr('contenteditable', false);
        $DOMelement.focus(function(e){
//			CL('focusing');
            if(e.preventDefault){
                e.preventDefault();
            }else{
                e.returnValue = false;
            }
            return false;
        });

        $DOMelement.keydown(function(){
//			CL('key downed');
        });
        $DOMelement.bind('mousedown contextmenu keypress keydown', function(e){
//			CL('misc event: ', e.type);
            if(e.preventDefault){
                e.preventDefault();
            }else{
                e.returnValue = false;
            }
            return false;
        });
    }
}

/*
 * Format common qti authoring form elements
 */
qtiEdit.initFormElements = function($container){
    qtiEdit.mapFileManagerField($container);
    qtiEdit.mapHtmlEditor($container);
    qtiEdit.mapElementValidator($container);
}

qtiEdit.mapElementValidator = function($container){
    $container.find('input').each(function(){
        var $formElt = $(this);
        var attrName = $formElt.attr('name');
        switch(attrName){
            case 'base':
            case 'expectedLength':
            case 'step':
                {
                    require([root_url + 'taoQTI/views/js/qtiAuthoring/src/validators/class.Integer.js'], function(Validator){
                        qtiEdit.bindValidator($formElt, new Validator());
                    });
                    break;
                }
            case 'choiceIdentifier':
                {
                    require([root_url + 'taoQTI/views/js/qtiAuthoring/src/validators/class.Regex.js'], function(Validator){
                        var validatorIdentifier = new Validator({
                            regex : /^[_a-z]{1}[a-z0-9-._]{0,31}$/i,
                            message : __('The format of the identifier is not valid.')
                        });
                        qtiEdit.bindValidator($formElt, validatorIdentifier);
                    });

                    require([root_url + 'taoQTI/views/js/qtiAuthoring/src/validators/class.Uniqueidentifier.js'], function(Validator){
                        var validatorIdentifier = new Validator({
                            'element' : $formElt
                        });
                        qtiEdit.bindValidator($formElt, validatorIdentifier, function($elt, ok){
                            if(ok){
                                if(myItem.currentInteraction){
                                    var interaction = myItem.currentInteraction;
                                    var choiceSerial = $formElt.parents('div.formContainer_choice').attr('id').replace('ChoiceForm_', '');
                                    var value = $formElt.val();
                                    qtiEdit.ajaxRequest('saveChoiceIdentifier', {
                                        'interactionSerial' : interaction.interactionSerial,
                                        'choiceSerial' : choiceSerial,
                                        'identifier' : value
                                    }, function(r){
                                        if(r.saved){
                                            qtiEdit.idList.set(choiceSerial, value);

                                            require([root_url + 'taoQTI/views/js/qtiAuthoring/responseClass.js'], function(responseClass){
                                                //reload the response grid tu update the identifier
                                                new responseClass(interaction.responseGrid, interaction);
                                            });
                                        }
                                    });
                                }
                            }
                        });
                    });
                    break;
                }
            case 'upperBound':
            case 'lowerBound':
                {
                    require([root_url + 'taoQTI/views/js/qtiAuthoring/src/validators/class.Float.js'], function(Validator){
                        var validatorFloat = new Validator();
                        qtiEdit.bindValidator($formElt, validatorFloat, function($elt, ok){
                            if(ok){
                                var cleanedValue = validatorFloat.cleanInput($formElt.val());
                                $formElt.val(cleanedValue);
                            }
                        });
                    });
                    break;
                }
        }
    });
}

qtiEdit.bindValidator = function($formElt, validator, afterValidate, options){

    var $errTag = $('<div class="form-error ui-state-error ui-corner-all"/>').insertAfter($formElt).hide();
    var start = new Date().getTime();
    var now = 0;
    var timelimit = (options && options.timelimit) ? options.timelimit : 200;//any modificaiton within "timelimit" milliseconds won't be checked
    var timer = null;
    var validators = $formElt.data('validators');

    if(!validators){
        validators = [];
        $formElt.bind('keyup.qtiAuthoring paste.qtiAuthoring', function(){

            var validateAll = function(){
                var allValidators = $formElt.data('validators') || {};
                for(var i in allValidators){
                    var validateFunction = allValidators[i];
                    if(!validateFunction()){
                        break;
                    }
                }
            }

            clearTimeout(timer);
            now = new Date().getTime();
            if(now - start > timelimit){
                start = now;//reset after validation
            }
            timer = setTimeout(validateAll, timelimit);

        });
    }

    validators.push(function(){
        return validator.validate($formElt.val(), function(ok){
            if(ok){
                if(typeof afterValidate === 'function'){
                    afterValidate($formElt, ok);
                }
                $errTag.hide();
                $formElt.removeClass('qti-attribute-invalid');
            }else{
                $errTag.show().html(validator.getMessage());
                $formElt.addClass('qti-attribute-invalid');
            }
        });
    });

    $formElt.data('validators', validators);
}

qtiEdit.mapFileManagerField = function($container){

    $container.find('input.qti-file-img').each(function(){

        var imgPath = $(this).val();
        var $modifiedForm = $(this).parents('form');
        var $eltHeight = $modifiedForm.find('input#object_height');
        var $eltWidth = $modifiedForm.find('input#object_width');
        var height = parseInt($eltHeight.val());
        var width = parseInt($eltWidth.val());
        var functionDisplayPreview = function(elt, imagePath, width, height){
            if($(elt).hasClass('qti-with-preview') && width && height){
                var maxHeight = 150;
                var maxWidth = 150;
                var baseRatio = width / height;
                var previewDescription = '';
                if(Math.max(width, height) < 150){
                    //no need to resize
                    previewDescription = __('preview (1:1)');
                }else{
                    //resize to the maximum lenght:
                    previewDescription = __('preview (real size:') + ' ' + width + 'px*' + height + 'px)';
                    if(height > width){
                        height = maxHeight;
                        width = height * baseRatio;
                    }else{
                        width = maxWidth;
                        height = width / baseRatio;
                    }

                }

                //insert the image preview
                var $parentElt = $(elt).parent();
                var $previewElt = $parentElt.find('div.qti-img-preview');
                if(!$previewElt.length){
                    $previewElt = $('<div class="qti-img-preview">').appendTo($parentElt);
                }
                // var $descriptionElt = $();
                $previewElt.empty().html('<img src="' + util.getMediaResource(imagePath) + '" style="width:' + width + 'px;height:' + height + 'px;" title="preview" alt="no preview available"/>');
                $previewElt.append('<br/><span class="qti-img-preview-label">' + previewDescription + '</span>');
            }

        }

        //elt file input
        var functionAddSlider = function($elt, imagePath, oldWidth, oldHeight){

            //need to redefine the scope of $modifiedForm, $eltHeight and $eltWidth
            var $modifiedForm = $elt.parents('form');
            var $eltHeight = $modifiedForm.find('input#object_height');
            var $eltWidth = $modifiedForm.find('input#object_width');
            var theOldWidth = oldWidth;
            var theOldHeight = oldHeight;

            var $parent = $elt.parent();
            if($elt.hasClass('qti-with-resizer')){
                
                if($parent.find('div.qti-img-resize-slider').length){
                    $parent.find('div.qti-img-resize-slider').remove();//destroy and build anew
                }
                
                //do append slider:
                var $slider = $('<div class="qti-img-resize-slider"></div>').appendTo($elt.parent());
                $slider.slider({
                    range : "min",
                    value : 100,
                    min : 10,
                    max : 200,
                    slide : function(e, ui){
                        var percentage = ui.value;
                        var newHeight = Math.round(percentage / 100 * theOldHeight);
                        var newWidth = Math.round(percentage / 100 * theOldWidth);
                        $eltHeight.val(newHeight);
                        $eltWidth.val(newWidth);
                        functionDisplayPreview($elt, imagePath, newWidth, newHeight);
                    },
                    stop : function(e, ui){
                        $eltHeight.change();//trigger change event
                        $eltWidth.change();
                    }
                });
            }
        }

        if(imgPath){
            if($(this).hasClass('qti-with-preview') && width && height){
                functionDisplayPreview(this, imgPath, width, height);
                functionAddSlider($(this), imgPath, width, height);
            }
        }

        if($.fn.fmbind){
            //dynamically change the style:
            $(this).width('50%');

            //add tao file manager
            $(this).fmbind({type : 'image', showselect : true}, function(elt, newImgPath, mediaData){

                //need to redefine the scope of $modifiedForm, $eltHeight and $eltWidth
                var $modifiedForm = $(elt).parents('form');
                var $eltHeight = $modifiedForm.find('input#object_height');
                var $eltWidth = $modifiedForm.find('input#object_width');
                var height = width = 0;
                if(mediaData){
                    if(mediaData.height)
                        height = mediaData.height;
                    if(mediaData.width)
                        width = mediaData.width;
                }

                imgPath = newImgPath;//record the newImgPath in the function wide scope
                $(elt).val(imgPath);

                if($modifiedForm.length){
                    //find the active interaction:
                    for(var itemSerial in qtiEdit.instances){
                        var item = qtiEdit.instances[itemSerial];
                        var interaction = item.currentInteraction;
                        if(interaction){
                            var id = $modifiedForm.attr('id');
                            if(id.indexOf('ChoiceForm') == 0){
                                interaction.modifiedChoices[id] = 'modified';//it is a choice form:
                            }else if(id.indexOf('InteractionForm') == 0){
                                interaction.setModifiedInteraction(true);
                            }
                        }
                    }
                }

                if(height){
                    $eltHeight.val(height);
                }
                if(width){
                    $eltWidth.val(width);
                }

                functionDisplayPreview(elt, imgPath, width, height);
                functionAddSlider($(elt), imgPath, width, height);
            });
        }
    });

}

qtiEdit.mapHtmlEditor = function($container){

    //map the wysiwyg editor to the html-area fields
    $container.find('.qti-html-area').each(function(){

        var $eltSerial = $(this).parents('form').find('#interactionSerial,#choiceSerial');
        var type = $eltSerial.attr('id').replace('Serial', '');
        if(type === 'interaction'){
            var forAttr = $(this).siblings('label.form_desc').attr('for');
            type = (forAttr === 'prompt') ? 'prompt' : 'interaction';
        }
        var instanceData = {
            type : type,
            serial : $eltSerial.val()
        }

        if($(this).css('display') !== 'none' && !$(this).siblings('.wysiwyg').length){
            var htmlEditor = new HtmlEditor(instanceData.type, instanceData.serial, $(this));
            htmlEditor.initEditor();
        }
    });
}

//TODO: side effect to be fully tested
qtiEdit.destroyHtmlEditor = function($container){
    $container.find('.qti-html-area').each(function(){
        try{
            $(this).wysiwyg('destroy');
        }catch(err){

        }
    });
}

qtiEdit.getEltInFrame = function(selector, selectedDocument){
    var foundElts = [];

    if(selectedDocument){
        $(selector, selectedDocument).each(function(){
            foundElts.push($(this));
        });
    }else{
        // for each iframe:
        $('iframe').each(function(){

            // get its document
            $(this).each(function(){
                var selectedDocument = this.contentWindow.document;
                $(selector, selectedDocument).each(function(){
                    foundElts.push($(this));
                });
            });

        });
    }

    return foundElts;
}

qtiEdit.availableInteractions = function(userOptions){

    var defaultOptions = {
        active : true
    };

    var options = (userOptions) ? $.extend(defaultOptions, userOptions) : defaultOptions;

    var availableInteractions = {
        'choice' : {
            'display' : 'block',
            'label' : __('Choice Interaction'),
            'short' : 'choice',
            'icon' : img_url + 'QTI_choice.png',
            'active' : true
        },
        'inlinechoice' : {
            'display' : 'inline',
            'label' : __('Inline Choice Interaction'),
            'short' : 'inline choice',
            'icon' : img_url + 'QTI_inlineChoice.png',
            'active' : true
        },
        'associate' : {
            'display' : 'block',
            'label' : __('Associate Interaction'),
            'short' : 'associate',
            'icon' : img_url + 'QTI_associate.png',
            'active' : true
        },
        'order' : {
            'display' : 'block',
            'label' : __('Order Interaction'),
            'short' : 'order',
            'icon' : img_url + 'QTI_order.png',
            'active' : true
        },
        'match' : {
            'display' : 'block',
            'label' : __('Match Interaction'),
            'short' : 'match',
            'icon' : img_url + 'QTI_match.png',
            'active' : true
        },
        'gapmatch' : {
            'display' : 'block',
            'label' : __('Gap Match Interaction'),
            'short' : 'gap match',
            'icon' : img_url + 'QTI_gapMatch.png',
            'active' : true
        },
        'textentry' : {
            'display' : 'inline',
            'label' : __('Textentry Interaction'),
            'short' : 'text entry',
            'icon' : img_url + 'QTI_textentry.png',
            'active' : true
        },
        'extendedtext' : {
            'display' : 'block',
            'label' : __('Extended Text Interaction'),
            'short' : 'extended text',
            'icon' : img_url + 'QTI_extendedText.png',
            'active' : true
        },
        'hottext' : {
            'display' : 'block',
            'label' : __('Hottext Interaction'),
            'short' : 'hottext',
            'icon' : img_url + 'QTI_hottext.png',
            'active' : true
        },
        'media' : {
            'display' : 'block',
            'label' : __('Media Interaction'),
            'short' : 'media',
            'icon' : img_url + 'QTI_media.png',
            'active' : true
        },
        'slider' : {
            'display' : 'block',
            'label' : __('Slider Interaction'),
            'short' : 'slider',
            'icon' : img_url + 'QTI_slider.png',
            'active' : true
        },
        'fileupload' : {
            'display' : 'block',
            'label' : __('File Upload Interaction'),
            'short' : 'file upload',
            'icon' : img_url + 'QTI_fileUpload.png',
            'active' : false
        },
        'hotspot' : {
            'display' : 'block',
            'label' : __('Hotspot Interaction'),
            'short' : 'hotspot',
            'icon' : img_url + 'QTI_hotspot.png',
            'active' : true
        },
        'graphicorder' : {
            'display' : 'block',
            'label' : __('Graphic Order Interaction'),
            'short' : 'graphic order',
            'icon' : img_url + 'QTI_graphicOrder.png',
            'active' : true
        },
        'graphicassociate' : {
            'display' : 'block',
            'label' : __('Graphic Associate Interaction'),
            'short' : 'graphic associate',
            'icon' : img_url + 'QTI_graphicAssociate.png',
            'active' : true
        },
        'graphicgapmatch' : {
            'display' : 'block',
            'label' : __('Graphic Gap Match Interaction'),
            'short' : 'graphic gap',
            'icon' : img_url + 'QTI_graphicGapmatch.png',
            'active' : true
        },
        'positionobject' : {
            'display' : 'block',
            'label' : __('Position Object Interaction'),
            'short' : 'position object',
            'icon' : img_url + 'QTI_positionObject.png',
            'active' : false
        },
        'selectpoint' : {
            'display' : 'block',
            'label' : __('Select Point Interaction'),
            'short' : 'select point',
            'icon' : img_url + 'QTI_selectPoint.png',
            'active' : true
        },    
        'endattempt' : {
            'display' : 'block',
            'label' : __('End Attempt Interaction'),
            'short' : 'end attempt',
            'icon' : img_url + 'QTI_endAttempt.png',
            'active' : false
        }
    };

    var returnValue = {};

    if(options.active){
        for(var interaction in availableInteractions){
            if(availableInteractions[interaction].active){
                returnValue[interaction] = availableInteractions[interaction];
            }
        }
    }else{
        returnValue = availableInteractions;
    }

    return returnValue;
}

qtiEdit.getInteractionModel = function(type){
    if(type && typeof type == 'string'){
        var interactions = qtiEdit.availableInteractions();
        if(interactions[type]){
            return interactions[type];
        }else{
            throw 'unknown interaction model : '.type;
            return null;
        }
    }
}

qtiEdit.buildInteractionPlaceholder = function(htmlString){
    var regex = new RegExp('{{qtiInteraction:(' + Object.keys(qtiEdit.availableInteractions()).join('|') + '):([\\w]+)}}', 'img');

    htmlString = htmlString.replace(regex,
        function(original, type, serial){
            var returnValue = original;

            var interactionModel = qtiEdit.getInteractionModel(type);
            if(interactionModel){
                var displayClass = 'qti_interaction_block';
                var element = 'div';
                if(interactionModel.display == 'inline'){
                    displayClass = 'qti_interaction_inline';
                    element = 'span';
                }
                var typeClass = 'qti_interaction_type_' + type;
                returnValue = '<' + element + ' id="' + serial + '" class="qti_interaction_box ' + displayClass + ' ' + typeClass + '"><input id="' + serial + '" class="qti_interaction_link" value="' + interactionModel.label + '" type="button"/></' + element + '>'
                returnValue += '&nbsp;'
            }

            return returnValue;
        });

    return htmlString;
}

qtiEdit.restoreInteractionPlaceholder = function(htmlString){
    var regex = '';
    if(util.msie()){
        regex = new RegExp('<(SPAN|DIV)[^<]*id=([^<"]*)\\s[^<]*class="[^<"]*qti_interaction_type_(\\w+)[^<"]*"[^<]*>(?:[^<>]*</?(EM|STRONG|FONT|B|I|BR|INPUT|SUB|SUP|H\\d)[^<>]*>[^<>]*|[^<>]*<SPAN[^<>]*>[^<>]*</SPAN>[^<>]*|<SPAN[^>]*/>)*</\\1>(?:&nbsp;)?', 'img');
    }else{
        regex = new RegExp('<(span|div)[^<]*id="([^<"]*)"\\s[^<]*class="[^<"]*qti_interaction_type_(\\w+)[^<"]*"[^<]*>(?:[^<>]*</?(em|strong|font|b|i|br|input|sub|sup|h\\d)[^<>]*>[^<>]*|[^<>]*<span[^<>]*>[^<>]*</span>[^<>]*|<span[^>]*/>)*</\\1>(?:&nbsp;)?', 'img');
    }

    htmlString = htmlString.replace(regex,
        function(original, div, serial, type){
            var returnValue = original;
            var interactionModel = qtiEdit.getInteractionModel(type)
            if(interactionModel){
                returnValue = '{{qtiInteraction:' + type + ':' + serial + '}}';
            }
            return returnValue;
        });

    htmlString = htmlString.replace(new RegExp('<(span|div)[^<]*class="[^<"]*qti_interaction_type_(\\w+)[^<"]*"[^<]*\\sid="([^<"]*)"[^<]*>(?:[^<>]*</?(em|strong|font|b|i|br|input|sub|sup|h\\d)[^<>]*>[^<>]*|[^<>]*<span[^<>]*>[^<>]*</span>[^<>]*|<span[^>]*/>)*</\\1>(?:&nbsp;)?', 'img'),
        function(original, div, type, serial){
            var returnValue = original;
            var interactionModel = qtiEdit.getInteractionModel(type)
            if(interactionModel){
                returnValue = '{{qtiInteraction:' + type + ':' + serial + '}}';
            }
            return returnValue;
        });

    return htmlString;
}

qtiEdit.buildMathPlaceholder = function(htmlString){
    var regex = new RegExp('{{qtiMath:(block|inline):([\\w]+)}}', 'img');

    htmlString = htmlString.replace(regex,
        function(original, type, serial){
            var displayClass = 'qti_math_inline';
            var element = 'span';
            if(type === 'block'){
                displayClass = 'qti_math_block';
                element = 'div';
            }
            var value = 'math';
            var returnValue = '<' + element + ' id="' + serial + '" class="qti_math_box ' + displayClass + '"><input id="' + serial + '" class="qti_math_link" value="' + value + '" type="button"/></' + element + '>'
            returnValue += '&nbsp;'

            return returnValue;
        });

    return htmlString;
}

qtiEdit.restoreMathPlaceholder = function(htmlString){
    var regex = '', replace = function(original, div, serial){
            var type = (div.toUpperCase() === 'DIV') ? 'block' : 'inline';
            return '{{qtiMath:' + type + ':' + serial + '}}';
        };
    if(util.msie()){
        regex = new RegExp('<(SPAN|DIV)[^<]*id=([^<"]*)\\s[^<]*class="[^<"]*qti_math_box[^<"]*"[^<]*>(?:[^<>]*</?(EM|STRONG|FONT|B|I|BR|INPUT|SUB|SUP|H\\d)[^<>]*>[^<>]*|[^<>]*<SPAN[^<>]*>[^<>]*</SPAN>[^<>]*|<SPAN[^>]*/>)*</\\1>(?:&nbsp;)?', 'img');
    }else{
        regex = new RegExp('<(span|div)[^<]*id="([^<"]*)"\\s[^<]*class="[^<"]*qti_math_box[^<"]*"[^<]*>(?:[^<>]*</?(em|strong|font|b|i|br|input|sub|sup|h\\d)[^<>]*>[^<>]*|[^<>]*<span[^<>]*>[^<>]*</span>[^<>]*|<span[^>]*/>)*</\\1>(?:&nbsp;)?', 'img');
    }

    htmlString = htmlString.replace(regex, replace);
    htmlString = htmlString.replace(new RegExp('<(span|div)[^<]*class="[^<"]*qti_math_box[^<"]*"[^<]*id="([^<"]*)"[^<]*>(?:[^<>]*</?(em|strong|font|b|i|br|input|sub|sup|h\\d)[^<>]*>[^<>]*|[^<>]*<span[^<>]*>[^<>]*</span>[^<>]*|<span[^>]*/>)*</\\1>(?:&nbsp;)?', 'img'), replace);

    return htmlString;
}

qtiEdit.buildObjectPlaceholder = function(htmlString){
    var regex = new RegExp('{{qtiObject:([\\w]+)}}', 'img');

    htmlString = htmlString.replace(regex,
        function(original, serial){
            var label = "object";
            var returnValue = '<div id="' + serial + '" class="qti_object_box qti_object_block"><input id="' + serial + '" class="qti_object_link" value="' + label + '" type="button"/></div>'
            returnValue += '&nbsp;'
            return returnValue;
        });

    return htmlString;
}

qtiEdit.restoreObjectPlaceholder = function(htmlString){
    var regex = '', replace = function(original, serial){
            return '{{qtiObject:' + serial + '}}';
        };
    if(util.msie()){
        regex = new RegExp('<DIV[^<]*id=([^<"]*)\\s[^<]*class="[^<"]*qti_object_box[^<"]*"[^<]*>(?:[^<>]*</?(EM|STRONG|FONT|B|I|BR|INPUT|SUB|SUP|H\\d)[^<>]*>[^<>]*|[^<>]*<SPAN[^<>]*>[^<>]*</SPAN>[^<>]*|<SPAN[^>]*/>)*</\DIV>(?:&nbsp;)?', 'img');
    }else{
        regex = new RegExp('<div[^<]*id="([^<"]*)"\\s[^<]*class="[^<"]*qti_object_box[^<"]*"[^<]*>(?:[^<>]*</?(em|strong|font|b|i|br|input|sub|sup|h\\d)[^<>]*>[^<>]*|[^<>]*<span[^<>]*>[^<>]*</span>[^<>]*|<span[^>]*/>)*</div>(?:&nbsp;)?', 'img');
    }

    htmlString = htmlString.replace(regex,replace);
    htmlString = htmlString.replace(new RegExp('<div[^<]*class="[^<"]*qti_object_box[^<"]*"[^<]*id="([^<"]*)"[^<]*>(?:[^<>]*</?(em|strong|font|b|i|br|input|sub|sup|h\\d)[^<>]*>[^<>]*|[^<>]*<span[^<>]*>[^<>]*</span>[^<>]*|<span[^>]*/>)*</div>(?:&nbsp;)?', 'img'),replace);

    return htmlString;
}

qtiEdit.createInfoMessage = function(message){
    helpers.createInfoMessage('<img src="' + img_url + 'ok.png" alt="" style="float:left;margin-right:10px;"/>' + message);
}

/**
 * Set the text in the title bar (currently disabled)
 */
qtiEdit.setTitleBar = function(text){
    $('#qtiAuthoring_title_container').text(text);
}

/**
 * get item content after filtering out the editing tag
 */
qtiEdit.prototype.getItemContent = function(){
    return this.itemEditor.getContent();
}

//the global save function
qtiEdit.prototype.save = function(itemUri){
    
    var _this = this;
    
    if(!this.itemUri)
        throw 'item uri cannot be empty';
    
    //save item data then export to rdf item:
    var itemProperties = $('#AssessmentItem_Form').serializeObject();
    itemProperties.itemSerial = this.itemSerial;
    itemProperties.itemUri = this.itemUri;
    itemProperties.itemData = util.htmlEncode(this.getItemContent());

    //could check if an interaction is being edited, so suggest to save it too:
    var saveItemFunction = function(){
        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/saveItem",
            data : itemProperties,
            dataType : 'json',
            success : function(r){
                if(r.saved){
                    qtiEdit.createInfoMessage(__('The item has been successfully saved'));

                    //update title here:
                    qtiEdit.setTitleBar(itemProperties.title);
                }
            }
        });
    }

    if(this.currentInteraction){
        this.saveCurrentInteraction(saveItemFunction);
    }else{
        saveItemFunction();
    }

}

qtiEdit.prototype.preview = function(){
    //use global generisActions api to display fullscreen preview
	generisActions.fullScreen(this.itemUri, this.itemClassUri, root_url + 'taoItems/Items/fullScreenPreview');
}

qtiEdit.prototype.debug = function(){
    window.open(root_url + '/taoQTI/QtiAuthoring/debug?itemSerial=' + this.itemSerial, 'QTIDebug', this.windowOptions);
}

qtiEdit.prototype.exportItem = function(){
    //when the export action is transformed into a service:
    window.open(root_url + '/taoItems/Items/downloadItemContent?uri=' + this.itemUri + '&classUri=' + this.itemClassUri, 'QTIExport', this.windowOptions);
}

qtiEdit.saveData = function(type, serial, data){
    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/saveData",
        data : {
            'type' : type,
            'serial' : serial,
            'data' : data,
        },
        dataType : 'json',
        success : function(r){
            // CL('saved');
        }
    });
}

qtiEdit.prototype.saveItemData = function(itemSerial){

    var instance = this;

    if(!itemSerial){
        itemSerial = instance.itemSerial;
    }

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/saveItemData",
        data : {
            'itemData' : util.htmlEncode(instance.getItemContent()),
            'itemSerial' : itemSerial
        },
        dataType : 'json',
        success : function(r){
            // CL('item saved');
        }
    });
}

qtiEdit.prototype.getDeletedInteractions = function(one){
    var deletedInteractions = [];
    var itemData = $(this.itemDataContainer).val();//TODO: improve with the use of regular expressions:
    for(var interactionSerial in this.interactions){
        if(itemData.indexOf(interactionSerial) < 0){
            //not found so considered as deleted:
            deletedInteractions.push(interactionSerial);
            if(one){
                return deletedInteractions;
            }
        }
    }

    return deletedInteractions;
}

qtiEdit.prototype.loadResponseProcessingForm = function(){

    var self = this;

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/editResponseProcessing",
        data : {
            'itemSerial' : self.itemSerial
        },
        dataType : 'html',
        success : function(form){
            $(self.responseProcessingFormContent).html(form);
        }
    });
}

qtiEdit.prototype.saveItemResponseProcessing = function($myForm){
    var self = this;
    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/saveItemResponseProcessing",
        data : $myForm.serialize(),
        dataType : 'json',
        success : function(r){
            if(r.saved){
                self.setResponseMode(r.setResponseMode);
                qtiEdit.createInfoMessage(__('The response processing has been saved'));
            }
        }
    });
}

qtiEdit.prototype.setResponseMode = function(visible){
    if(visible && !this.responseMode)
        this.responseMode = true;
    else
        this.responseMode = false;
    if(((visible && !this.responseMode) || (!visible)) && this.currentInteraction)
        this.loadInteractionForm(this.currentInteraction.interactionSerial);
}

qtiEdit.prototype.loadStyleSheetForm = function(empty){

    var self = this;

    //check if the form is not empty:
    var post = '';
    if($('#css_uploader').length && !empty){
        post = $('#css_uploader').serialize();
        post += '&itemUri=' + this.itemUri;
    }else{
        post = {itemSerial : this.itemSerial, itemUri : this.itemUri};
    }

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/manageStyleSheets",
        data : post,
        dataType : 'html',
        success : function(form){
            $(self.cssFormContent).html(form);
            if($('#css_uploader').length){

                var $cssForm = $('form#css_uploader').hide();
                $('#cssFormToggleButton').toggle(function(){
                    $(this).switchClass('ui-icon-circle-plus', 'ui-icon-circle-minus');
                    $cssForm.slideToggle();
                }, function(){
                    $(this).switchClass('ui-icon-circle-minus', 'ui-icon-circle-plus');
                    $cssForm.slideToggle();
                });

                $('#css_uploader').find('input#title').each(function(){
                    $(this).val('');
                });//reset the css name after reload
                $('#css_uploader').find('input#submit').unbind('click').bind('click', function(){
                    self.loadStyleSheetForm();//submit manually to reset the event handler
                    return false;
                });

            }
        }
    });
}

qtiEdit.prototype.deleteStyleSheet = function(css_href){
    var self = this;
    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/deleteStyleSheet",
        data : {
            'itemSerial' : this.itemSerial,
            'itemUri' : this.itemUri,
            'css_href' : css_href
        },
        dataType : 'json',
        success : function(r){
            if(r.deleted){
                self.loadStyleSheetForm(true);
            }
        }
    });
}

qtiEdit.prototype.saveCurrentInteraction = function(callback, reloadResponse){
    if(this.currentInteraction){
        var interaction = this.currentInteraction;
        var $invalidAttribute = $('input.qti-attribute-invalid');
        if($invalidAttribute.length){
            util.confirmBox(
                __('Error in Interactio Editor'),
                __('There is an error in the interaction form. Please correct it before going any further.'),
                {
                    'Go To Error' : function(){
                        $invalidAttribute.focus();
                        $(this).dialog('close');
                    }
                },
            {useDefaultCloseButton : false}
            );
        }else{
            interaction.saveAll(callback, reloadResponse);
        }
    }
}

qtiEdit.prototype.loadInteractionForm = function(interactionSerial){
    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/editInteraction",
        data : {
            'interactionSerial' : interactionSerial
        },
        dataType : 'html',
        success : function(form){
            var $interactionForm = $('#qtiAuthoring_interaction_container').empty().html(form);
            qtiEdit.initFormElements($interactionForm);
            if($myTab){
                $myTab.tabs("select", 1);
            }
        }
    });
};

qtiEdit.ajaxRequest = function(options, data, success){
    if(typeof options === 'string' && typeof data === 'object' && typeof success === 'function'){
        data.itemSerial = qtiEdit.itemSerial;
        options = {
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/" + options,
            data : data,
            dataType : 'json',
            success : success
        };
    }else if(typeof(options.data) === 'object'){
        options.data.itemSerial = qtiEdit.itemSerial;
    }else if(typeof(options.data) === 'string'){
        options.data += '&itemSerial=' + qtiEdit.itemSerial;
    }
    $.ajax(options);
}

qtiEdit.buildMediaPreviewer = function($container, mediaAttributes){
    $container.empty().html('<div id="media-preview"></div>');//destray every thing first and start anew
    var object = new Qti.Object(null, mediaAttributes);
    object.setRenderer(new Qti.DefaultRenderer());
    object.render(null, $container.find('div#media-preview'));
    object.postRender();
}

qtiEdit.addElement = function(htmlEditor, type){

    if(htmlEditor.$editor){
        
        htmlEditor.$editor.wysiwyg('insertHtml', '{{qti' + util.ucfirst(type) + ':new}}');
        
        var data = htmlEditor.$editor.wysiwyg('getContent');
        data = util.htmlEncode(data);
        if(data.indexOf('{{qti' + util.ucfirst(type) + ':new}}') < 0){
            data += '{{qti' + util.ucfirst(type) + ':new}}';
        }
        
        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/addElement",
            data : {
                'serial' : htmlEditor.serial,
                'type' : htmlEditor.type,
                'body' : data,
                'newType' : type,
            },
            dataType : 'json',
            success : function(r){
                //set the content:
                htmlEditor.$editor.wysiwyg('setContent', $("<div/>").html(r.body).html());
                htmlEditor.bindElementLinkListener();
            }
        });
    }else{
        throw 'invalid html editor in argument';
    }

}
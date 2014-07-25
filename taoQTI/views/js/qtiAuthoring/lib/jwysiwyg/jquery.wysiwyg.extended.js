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

/*
 * define customized action button for jwysiwyg editor specific to TAO QTI authoring tool
 */
function getCustomControl(action, params){

    var addCSS = function(href){
        require([root_url + '/taoQTI/views/js/qtiAuthoring/lib/murmurhash/murmurhash3_gc.js'], function(){
            var cssId = murmurhash3_32_gc(href, 'cssFileName');  // you could encode the css path itself to generate id..
            if(!document.getElementById(cssId)){
                var head = document.getElementsByTagName('head')[0];
                var link = document.createElement('link');
                link.id = cssId;
                link.rel = 'stylesheet';
                link.type = 'text/css';
                link.href = href;
                link.media = 'all';
                head.appendChild(link);
            }
        });
    };

    var dependencies = {
        addChoiceInteraction : {params : ['itemEditor']},
        addAssociateInteraction : {params : ['itemEditor']},
        addOrderInteraction : {params : ['itemEditor']},
        addMatchInteraction : {params : ['itemEditor']},
        addInlineChoiceInteraction : {params : ['itemEditor']},
        addTextEntryInteraction : {params : ['itemEditor']},
        addExtendedTextInteraction : {params : ['itemEditor']},
        addHotTextInteraction : {params : ['itemEditor']},
        addGapMatchInteraction : {params : ['itemEditor']},
        addHotspotInteraction : {params : ['itemEditor']},
        addGraphicOrderInteraction : {params : ['itemEditor']},
        addGraphicAssociateInteraction : {params : ['itemEditor']},
        addGraphicGapMatchInteraction : {params : ['itemEditor']},
        addSelectPointInteraction : {params : ['itemEditor']},
        addPositionObjectInteraction : {params : ['itemEditor']},
        addSliderInteraction : {params : ['itemEditor']},
        addMediaInteraction : {params : ['itemEditor']},
        addUploadInteraction : {params : ['itemEditor']},
        addEndAttemptInteraction : {params : ['itemEditor']},
        saveItemData : {params : ['itemEditor']},
        addObject : {params : ['htmlEditor']},
        addMathML : {params : ['htmlEditor']},
        customHTML : {
            js : [root_url + '/taoQTI/views/js/qtiAuthoring/lib/codemirror/codemirror-compressed.js'],
            css : [root_url + '/taoQTI/views/js/qtiAuthoring/lib/codemirror/codemirror.css',
                root_url + '/taoQTI/views/js/qtiAuthoring/lib/codemirror/codemirror-taoQTI.css']
        }
    };

    var customButtons = {
        justifyLeft : {
            visible : true,
            groupIndex : 1,
            eltClass : 'qti_align_left',
            tooltip : 'Justify Left',
            exec : function(){
                this.editorDoc.execCommand('justifyLeft', false, []);
                $('div[align=left], p[align=left]', this.editorDoc).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_left');
                
                //webkit:
                $('div', this.editorDoc).not('.qti_math_block,.qti_object_box,.qti_interaction_block').filter(function(){
                    return $(this).css('text-align') === 'left';
                }).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_left');
                
            }
        },
        justifyCenter : {
            visible : true,
            eltClass : 'qti_align_center',
            tooltip : 'Justify Center',
            exec : function(){
                this.editorDoc.execCommand('justifyCenter', false, []);
                $('div[align=center], p[align=center]', this.editorDoc).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_center');
                
                //webkit:
                $('div', this.editorDoc).not('.qti_math_block,.qti_object_box,.qti_interaction_block').filter(function(){
                    return $(this).css('text-align') === 'center';
                }).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_center');
                
            }
        },
        justifyRight : {
            visible : true,
            eltClass : 'qti_align_right',
            tooltip : 'Justify Right',
            exec : function(){
                this.editorDoc.execCommand('justifyRight', false, []);
                $('div[align=right], p[align=right]', this.editorDoc).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_right');

                //webkit:
                $('div', this.editorDoc).not('.qti_math_block,.qti_object_box,.qti_interaction_block').filter(function(){
                    return $(this).css('text-align') === 'right';
                }).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_right');
                
            }
        },
        justifyFull : {
            visible : true,
            eltClass : 'qti_align_justify',
            tooltip : 'Justify Full',
            exec : function(){
                this.editorDoc.execCommand('justifyFull', false, []);
                $('div[align=justify], p[align=justify]', this.editorDoc).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_justify');

                //webkit:
                $('div', this.editorDoc).not('.qti_math_block,.qti_object_box,.qti_interaction_block').filter(function(){
                    return $(this).css('text-align') === 'justify';
                }).removeAttr('align').removeAttr('style').removeAttr('class').addClass('qti_align_justify');
                
            }
        },
        indent : {
            groupIndex : 2,
            visible : true,
            tooltip : 'Indent',
            exec : function(){
                this.editorDoc.execCommand('indent', false, []);
                $('blockquote', this.editorDoc).removeAttr('style').removeAttr('dir').removeAttr('class').addClass('qti_indent');
            }
        },
        addChoiceInteraction : {
            visible : true,
            className : 'add_choice_interaction add_qti_interaction',
            exec : function(){
                //display modal window with the list of available type of interactions
                var interactionType = 'choice';

                //insert location of the current interaction in the item:
                this.insertHtml("{{qtiInteraction:choice:new}}");

                //send to request to the server
                params.itemEditor.addInteraction(interactionType, this.getContent());
            },
            tooltip : 'add choice interaction'
        },
        addAssociateInteraction : {
            visible : true,
            className : 'add_associate_interaction add_qti_interaction',
            exec : function(){
                var interactionType = 'associate';
                this.insertHtml("{{qtiInteraction:associate:new}}");
                params.itemEditor.addInteraction(interactionType, this.getContent());
            },
            tooltip : 'add associate interaction'
        },
        addOrderInteraction : {
            visible : true,
            className : 'add_order_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:order:new}}");
                params.itemEditor.addInteraction('order', this.getContent());
            },
            tooltip : 'add order interaction'
        },
        addMatchInteraction : {
            visible : true,
            className : 'add_match_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:match:new}}");
                params.itemEditor.addInteraction('match', this.getContent());
            },
            tooltip : 'add match interaction'
        },
        addInlineChoiceInteraction : {
            visible : true,
            className : 'add_inlinechoice_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml('{{qtiInteraction:inlineChoice:new}}');
                params.itemEditor.addInteraction('inlineChoice', this.getContent());
            },
            tooltip : 'add inline choice interaction'
        },
        addTextEntryInteraction : {
            visible : true,
            className : 'add_textentry_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml('{{qtiInteraction:textentry:new}}');
                params.itemEditor.addInteraction('textEntry', this.getContent());
            },
            tooltip : 'add text entry interaction'
        },
        addExtendedTextInteraction : {
            visible : true,
            className : 'add_extendedtext_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml('{{qtiInteraction:extendedtext:new}}');
                params.itemEditor.addInteraction('extendedText', this.getContent());
            },
            tooltip : 'add extended text interaction'
        },
        addHotTextInteraction : {
            visible : true,
            className : 'add_hottext_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:hottext:new}}");
                params.itemEditor.addInteraction('hottext', this.getContent());
            },
            tooltip : 'add hot text interaction'
        },
        addGapMatchInteraction : {
            visible : true,
            className : 'add_gapmatch_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:gapmatch:new}}");
                params.itemEditor.addInteraction('gapMatch', this.getContent());
            },
            tooltip : 'add gap match interaction'
        },
        addHotspotInteraction : {
            visible : true,
            className : 'add_hotspot_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:hotspot:new}}");
                params.itemEditor.addInteraction('hotspot', this.getContent());
            },
            tooltip : 'add hot spot interaction'
        },
        addGraphicOrderInteraction : {
            visible : true,
            className : 'add_graphicorder_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:graphicorder:new}}");
                params.itemEditor.addInteraction('graphicOrder', this.getContent());
            },
            tooltip : 'add graphic order interaction'
        },
        addGraphicAssociateInteraction : {
            visible : true,
            className : 'add_graphicassociate_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:graphicassociate:new}}");
                params.itemEditor.addInteraction('graphicAssociate', this.getContent());
            },
            tooltip : 'add graphic associate interaction'
        },
        addGraphicGapMatchInteraction : {
            visible : true,
            className : 'add_graphicgapmatch_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:graphicgapmatch:new}}");
                params.itemEditor.addInteraction('graphicGapMatch', this.getContent());
            },
            tooltip : 'add hot spot interaction'
        },
        addSelectPointInteraction : {
            visible : true,
            className : 'add_selectpoint_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:selectpoint:new}}");
                params.itemEditor.addInteraction('selectPoint', this.getContent());
            },
            tooltip : 'add select point interaction'
        },
        addPositionObjectInteraction : {
            visible : false,
            className : 'add_positionobject_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:positionobject:new}}");
                params.itemEditor.addInteraction('positionObject', this.getContent());
            },
            tooltip : 'add position object interaction'
        },
        addSliderInteraction : {
            visible : true,
            className : 'add_slider_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:slider:new}}");
                params.itemEditor.addInteraction('slider', this.getContent());
            },
            tooltip : 'add slider interaction'
        },
        addMediaInteraction : {
            visible : true,
            className : 'add_media_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:media:new}}");
                params.itemEditor.addInteraction('media', this.getContent());
            },
            tooltip : 'add media interaction'
        },
        addUploadInteraction : {
            visible : false,
            className : 'add_fileupload_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:upload:new}}");
                params.itemEditor.addInteraction('upload', this.getContent());
            },
            tooltip : 'add file upload interaction'
        },
        addEndAttemptInteraction : {
            visible : false,
            className : 'add_endattempt_interaction add_qti_interaction',
            exec : function(){
                this.insertHtml("{{qtiInteraction:endattempt:new}}");
                params.itemEditor.addInteraction('endAttempt', this.getContent());
            },
            tooltip : 'add end attempt interaction'
        },
        saveItemData : {
            visible : false,
            className : 'addInteraction add_qti_interaction',
            exec : function(){
                params.itemEditor.saveItemData();
            },
            tooltip : 'save'
        },
        insertImage : {
            visible : true,
            className : 'insertImage',
            tooltip : 'Insert Image',
            exec : function(){
                var self = this;
                qtiEdit.ajaxRequest('editImage', {}, function(data){
                    $('<div id="editImageForm" title="' + data.title + '">' + data.html + '</div>').dialog({
                        modal : true,
                        width : 460,
                        height : 320,
                        buttons : [
                            {
                                text : __('Insert Image'),
                                click : function(){
                                    var img = "<img src='" + $(this).find('#url').val() + "' alt='" + $(this).find('#alt').val() + "' />";
                                    self.insertHtml(img);
                                    self.saveContent();
                                    $(this).dialog('close');
                                }
                            },
                            {
                                text : __('Cancel'),
                                click : function(){
                                    $(this).dialog('close');
                                }
                            }
                        ],
                        open : function(){
                            qtiEdit.mapFileManagerField($(this));
                        }
                    });

                });
            }
        },
        addObject : {
            visible : true,
            className : 'addMedia',
            exec : function(){
                qtiEdit.addElement(params.htmlEditor, 'object');
            },
            tooltip : 'insert object',
            groupIndex : 6
        },
        addMath : {
            visible : true,
            className : 'addMath',
            exec : function(){
                qtiEdit.addElement(params.htmlEditor, 'math');
            },
            tooltip : 'insert math',
            groupIndex : 6
        },
        customHTML : {
            groupIndex : 10,
            visible : true,
            exec : function(){

                if(this.viewHTML){

                    //get data from code mirror and restore textarea
                    if(this.codemirrorEditor){
                        this.codemirrorEditor.save();
                        this.codemirrorEditor.toTextArea();
                        this.codemirrorEditor = null;
                    }

                    //set data to jwysiwyg
                    var value = $(this.original).val();
                    this.setContent(value);

                    //show jwysiwyg editor:
                    $(this.original).hide();
                    $(this.editor).parent().css('height', '95%');
                    $(this.editor).show();

                    //show enabled authoring options in jwysiwyg and the authoring tool
                    $(this.element).find('ul.panel li').show();
                    $(this.element).find('ul.panel li.html').attr('title', __('View source code'));
                    if($(this.element).parents('#qtiAuthoring_itemEditor').length){
                        $('#qtiAuthoring_menu_interactions_overlay').hide();
                    }

                    if(this.options.events.unsetHTMLview){
                        this.options.events.unsetHTMLview(this.editorDoc);
                    }

                }else{

                    var $ed = $(this.editor);
                    this.saveContent();
                    var width = $(this.element).outerWidth() - 6;
                    var height = $(this.element).height() - $(this.panel).height() - 6;
                    $(this.original).css({
                        width : width,
                        height : height,
                        resize : 'none'
                    }).show();

                    //hide disabled authoring options in jwysiwyg and the authoring tool
                    $(this.element).find('ul.panel li').hide();
                    $(this.element).find('ul.panel li.html').attr('title', __('Return to normal edition')).show();
                    if($(this.element).parents('#qtiAuthoring_itemEditor').length){
                        $('#qtiAuthoring_menu_interactions_overlay').show();
                    }

                    //build code mirror
                    this.codemirrorEditor = CodeMirror.fromTextArea(this.original, {
                        mode : {
                            name : 'xml',
                            htmlMode : true,
                            alignCDATA : true
                        }
                    });
                    this.codemirrorEditor.setSize(width, height);
                    this.codemirrorEditor.autoFormatRange({line : 0, ch : 0}, {line : this.codemirrorEditor.lineCount() + 1, ch : 0});
                    this.codemirrorEditor.setCursor(0);

                    //hide jwysiwyg
                    $ed.hide();
                    $(this.editor).parent().css('height', 'auto');

                    if(this.options.events.setHTMLview){
                        this.options.events.setHTMLview(this.editorDoc);
                    }

                }

                this.viewHTML = !(this.viewHTML);
            },
            tooltip : __('View source code')
        }
    }

    if(customButtons[action]){
        //check dependency : 
        if(dependencies[action]){
            var dep = dependencies[action];
            if(dep.params){
                for(var i in dep.params){
                    if(!params[dep.params[i]]){
                        throw 'missing required param : '.dep.params[i];
                        break;
                    }
                }
            }

            if(dep.css){
                for(var i in dep.css){
                    addCSS(dep.css[i]);
                }
            }

            if(dep.js){
                require(dep.js, function(){
                    //loading
                });
            }
        }

        return customButtons[action];

    }else{
        throw 'unknown action : '.action;
    }
}



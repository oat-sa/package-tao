HtmlEditorItem = HtmlEditor.extend({
    init : function(item, $editor){
        this.item = item;
        this._super('item', item.itemSerial, $editor);
        this.iFrameClass = 'wysiwyg-item';
    },
    getControls : function(){
        return {
            strikeThrough : {visible : false},
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
            removeFormat : {visible : false},
            addChoiceInteraction : getCustomControl('addChoiceInteraction', {itemEditor : this}),
            addAssociateInteraction : getCustomControl('addAssociateInteraction', {itemEditor : this}),
            addOrderInteraction : getCustomControl('addOrderInteraction', {itemEditor : this}),
            addMatchInteraction : getCustomControl('addMatchInteraction', {itemEditor : this}),
            addInlineChoiceInteraction : getCustomControl('addInlineChoiceInteraction', {itemEditor : this}),
            addTextEntryInteraction : getCustomControl('addTextEntryInteraction', {itemEditor : this}),
            addExtendedTextInteraction : getCustomControl('addExtendedTextInteraction', {itemEditor : this}),
            addHotTextInteraction : getCustomControl('addHotTextInteraction', {itemEditor : this}),
            addGapMatchInteraction : getCustomControl('addGapMatchInteraction', {itemEditor : this}),
            addHotspotInteraction : getCustomControl('addHotspotInteraction', {itemEditor : this}),
            addGraphicOrderInteraction : getCustomControl('addGraphicOrderInteraction', {itemEditor : this}),
            addGraphicAssociateInteraction : getCustomControl('addGraphicAssociateInteraction', {itemEditor : this}),
            addGraphicGapMatchInteraction : getCustomControl('addGraphicGapMatchInteraction', {itemEditor : this}),
            addSelectPointInteraction : getCustomControl('addSelectPointInteraction', {itemEditor : this}),
            addPositionObjectInteraction : getCustomControl('addPositionObjectInteraction', {itemEditor : this}),
            addSliderInteraction : getCustomControl('addSliderInteraction', {itemEditor : this}),
            addUploadInteraction : getCustomControl('addUploadInteraction', {itemEditor : this}),
            addEndAttemptInteraction : getCustomControl('addEndAttemptInteraction', {itemEditor : this}),
            addMediaInteraction : getCustomControl('addMediaInteraction', {itemEditor : this}),
            saveItemData : getCustomControl('saveItemData', {itemEditor : this}),
            addObject : getCustomControl('addObject', {htmlEditor : this}),
            addMath : getCustomControl('addMath', {htmlEditor : this}),
            insertImage : getCustomControl('insertImage', {})
        };
    },
    bindElementLinkListener : function(){
        this._super();
        this.bindInteractionLinkListener();
    },
    bindInteractionLinkListener : function(){

        var htmlEditor = this;
        var links = qtiEdit.getEltInFrame('.qti_interaction_link', this.getEditorDocument());
        for(var i in links){

            var $interaction = links[i];
            var interactionSerial = $interaction.attr('id');
            htmlEditor.elements[interactionSerial] = interactionSerial;

            $interaction.mousedown(function(e){
                e.preventDefault();
            });
            $interaction.bind('click', {'interactionSerial' : interactionSerial}, function(e){
                e.preventDefault();
                htmlEditor.item.loadInteractionForm(e.data.interactionSerial);
            });

            qtiEdit.makeNoEditable($interaction);
            //append the delete button:
            var $interactionContainer = $interaction.parent('.qti_interaction_box');
            $interactionContainer.bind('dragover drop', function(e){
                e.preventDefault();
                return false;
            });

            qtiEdit.makeNoEditable($interactionContainer);

            var $deleteButton = $('<span class="qti_interaction_box_delete"></span>').appendTo($interactionContainer);
            $deleteButton.attr('title', __('Delete interaction'));
            $deleteButton.hide();
            $deleteButton.bind('click', {'interactionSerial' : interactionSerial}, function(e){
                e.preventDefault();
                util.confirmBox(__('Interaction Deletion'), __('Please confirm interaction deletion'), {
                    'Delete Interaction' : function(){
                        htmlEditor.deleteElements([e.data.interactionSerial]);
                        $(this).dialog('close');
                    }
                });
                return false;
            });

            $deleteButton.bind('mousedown contextmenu', function(e){
                e.preventDefault();
            });

            qtiEdit.makeNoEditable($deleteButton);
            $interaction.parent().hover(function(){
                $(this).children('.qti_interaction_box_delete').show();
            }, function(){
                $(this).children('.qti_interaction_box_delete').hide();
            });

        }
    },
    addInteraction : function(interactionType, itemData){

        var htmlEditor = this;
        itemData = util.htmlEncode(itemData);
        if(!itemData.match(/{{qtiInteraction:(\w)+:new}}/img)){
            itemData += '{{qtiInteraction:' + interactionType + ':new}}';
        }
        
        qtiEdit.ajaxRequest({
            type : "POST",
            url : root_url + "taoQTI/QtiAuthoring/addInteraction",
            data : {
                'interactionType' : interactionType,
                'itemData' : itemData
            },
            dataType : 'json',
            success : function(r){
                //set the content:
                htmlEditor.$editor.wysiwyg('setContent', $("<div/>").html(r.itemData).html());

                //then add listener
                htmlEditor.bindInteractionLinkListener();
            }
        });
    }
});
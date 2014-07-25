<div id="qtiAuthoring_interaction_left_container">
	<div id="qtiAuthoring_interactionEditor">

		<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default">
				<?=get_data('interactionType')?> <?=__('Interaction Editor')?>
		</div>
		<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
			<div id="formInteraction_content_form_body" class="ext-home-container">
				<?=get_data('formInteraction')?>
			</div>

			<div class="ext-home-container">
				<div id="formInteraction_title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
						<?=__('Interaction content editor:')?>
				</div>
				<div id="formContainer_interaction" class="ui-widget-content ui-corner-bottom formContainer_choices" style="padding:15px;">
					<span class="interactionEditor_wysiwyg_instruction"><?=__('To insert a "hot text" in your interaction, set focus on the desired place in the editor below then click on the "set hot text" button ')?></span><img src="<?=BASE_WWW?>img/qtiAuthoring/add_hottext.png" title ="set hot text button" alt=""/>.
					<textarea name="interactionEditor_wysiwyg_name" id="interactionEditor_wysiwyg"><?=get_data('interactionData')?></textarea>
					<div id="interactionEditor_wysiwyg_addChoice"></div>
				</div>
			</div>

			<div id="formChoices_container" class="ext-home-container">
			</div>

			<div id="formInteraction_content_form_bottom" class="ext-home-container">
				<div class="xhtml_form">
					<div id="formInteraction_content_form_bottom_button" class="form-toolbar">
					</div>
				</div>
			</div>

		</div>

	</div>
</div>

<script type="text/javascript">
var myInteraction = null;
$(document).ready(function(){
	try{
		myInteraction = new interactionClass('<?=get_data('interactionSerial')?>', myItem.itemSerial, {"choicesFormContainer":'#formChoices_container'});
		myInteraction.setType('<?=get_data('interactionType')?>');
	}catch(err){
		CL('error creating interaction', err);
	}

	try{

		myInteraction.buildInteractionEditor('#interactionEditor_wysiwyg', {
			extraControls:{
				'createGap': {
					visible : true,
					className: 'addHottext',
					exec: function(){
						this.insertHtml('{{qtiHottext:new}}');
						myInteraction.addHottext();
					},
					tags: ['a'],
					tooltip: 'set a hot text',
					groupIndex: 12
				}
			},
			extraEvents:{
				frameReady: function(editor){
					editor.setContent(interactionClass.buildHottextPlaceholder(editor.getContent()));
					myInteraction.bindChoiceLinkListener();
				},
				unsetHTMLview : function(){
					myInteraction.bindChoiceLinkListener();
				},
				beforeSetContent : function(content){
					return interactionClass.buildHottextPlaceholder(content);
				},
				beforeSaveContent : function(content){
					myInteraction.setModifiedInteraction(true);
					return interactionClass.restoreHottextPlaceholder(content);
				},
				afterGetContent : function(content){
					return interactionClass.restoreHottextPlaceholder(content);
				}
			}
		});
		
	}catch(err){
		CL('error building interaction data editor', err);
	}

	$('.interaction-form-submitter').clone().appendTo('#formInteraction_content_form_bottom_button');
	myInteraction.initInteractionFormSubmitter();
	
});
</script>

<div id="qtiAuthoring_interaction_right_container">
<?include('form_response_container.tpl');?>
</div>
<div style="clear:both"/>
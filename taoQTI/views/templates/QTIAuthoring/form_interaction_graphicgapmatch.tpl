<div id="qtiAuthoring_interaction_left_container">
	<div id="qtiAuthoring_interactionEditor"> 

		<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default">
				<?=get_data('interactionType')?> <?=__('Interaction Editor')?>
		</div>
		<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
			<div id="formInteraction_content_form_body" class="ext-home-container">
				<?=get_data('formInteraction')?>
			</div>
			
			<div class="xhtml_form">
				<div id="formInteraction_object_container_title"><?=__('Graphic interaction stage')?>&nbsp;<span class="qti-img-preview-label"></span></div>
				<div id="formInteraction_object_container">
					<div id="formInteraction_object">
						<?=__('Background image not set yet. Select it in the "Image source url" input then update.')?>
					</div>
				</div>
			</div>
			
			<div id="formChoices_container" class="ext-home-container"/>
			
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
	
	var backgroundImagePath = "<?=get_data('backgroundImagePath')?>";
	var options = {};
	if(backgroundImagePath){
		options.backgroundImagePath = backgroundImagePath;
		var width = "<?=get_data('width')?>";
		var height = "<?=get_data('height')?>";
		if(width) options.width = width;
		if(height) options.height = height;
	}
	
	try{
		myInteraction = new interactionClass(
			'<?=get_data('interactionSerial')?>', 
			myItem.itemSerial, 
			{
				"choicesFormContainer":'#formChoices_container',
				"shapeEditorOptions":options
			}
		);
		myInteraction.setType('<?=get_data('interactionType')?>');
	}catch(err){
		CL('error creating interaction', err);
	}
	
	$('.interaction-form-submitter').clone().appendTo('#formInteraction_content_form_bottom_button');
	myInteraction.initInteractionFormSubmitter();
	
});
</script>

<div id="qtiAuthoring_interaction_right_container">
<?include('form_response_container.tpl');?>
</div>
<div style="clear:both"/>

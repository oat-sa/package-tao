<div id="qtiAuthoring_interaction_left_container">
	<div id="qtiAuthoring_interactionEditor"> 
		
		<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default">
				<?=get_data('interactionType')?> <?=__('Interaction Editor')?>
		</div>
		<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
			<div class="ext-home-container">
				<?=get_data('formInteraction')?>
			</div>
			
			<div id="formInteraction_object_container">
				<div id="formInteraction_object" />
			</div>
			
		</div>
	
	</div>
</div>
<div id="qtiAuthoring_interaction_right_container">
	<?include('form_response_container.tpl');?>
</div>
<div style="clear:both"/>

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
				"shapeEditorOptions":options
			}
		);
		myInteraction.setType('<?=get_data('interactionType')?>');
	}catch(err){
		CL('error creating interaction', err);
	}
});
</script>
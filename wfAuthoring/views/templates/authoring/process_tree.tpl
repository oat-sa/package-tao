<div class="ui-widget-content ui-corner-bottom">

	<?$sectionName=get_data("section");?>

	<div id="tree-<?=$sectionName?>" ></div>
	<div id="tree-actions-<?=$sectionName?>" class="tree-actions">
		<input type="text"   id="filter-content-<?=$sectionName?>" value="*"  autocomplete='off'  size="10" title="<?=__('Use the * character to replace any string')?>" />
		<input type='button' id="filter-action-<?=$sectionName?>"  value="<?=__("Filter")?>" 	  /><br />
		<input type='button' id="open-action-<?=$sectionName?>"    value='<?=__("Open all")?>'  />
		<input type='button' id="close-action-<?=$sectionName?>"   value='<?=__("Close all")?>' />
	</div>

</div>

<script type="text/javascript">
	$(function(){
		require(['require', 'jquery', 'generis.tree.browser'], function(req, $, GenerisTreeBrowserClass) {
			new GenerisTreeBrowserClass('#tree-<?=$sectionName?>', authoringControllerPath+"getInstancesOf?instanceof=<?=$sectionName?>", {
				formContainer: 			"#<?=$sectionName?>_form",
				actionId: 				"<?=$sectionName?>",
				editInstanceAction: 	authoringControllerPath+"editInstance",
				createInstanceAction: 	authoringControllerPath+"addInstance",
				deleteAction: 			authoringControllerPath+"delete",
				duplicateAction: 		authoringControllerPath+"cloneInstance",
				instanceName:			"<?=$sectionName?>"
			});
<?if($sectionName == "serviceDefinition"):?>

			//add a callback function to disable instantiating servicedefinition class directly
			$("#tree-serviceDefinition > ul > li:first").removeClass("node-class");
<?endif;?>
		});
	});
</script>
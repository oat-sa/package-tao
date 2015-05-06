<div class="ui-widget-content ui-corner-bottom">
	
	<div id="tree-activity" ></div>
	
</div>
			
<script type="text/javascript">
	$(function(){
		
		initActivityTree();
		
	});
	
	function initActivityTree(){
		new ActivityTreeClass('#tree-activity', authoringControllerPath+"getActivities", {
			processUri: processUri,
			formContainer: "#activity_form",
			createActivityAction: authoringControllerPath+"addActivity",
			createInteractiveServiceAction: authoringControllerPath+"addInteractiveService",
			createInferenceRuleAction: authoringControllerPath+"addInferenceRule",
			createConsistencyRuleAction: authoringControllerPath+"addConsistencyRule",
			editInteractiveServiceAction: authoringControllerPath+"editCallOfService",
			editActivityPropertyAction: authoringControllerPath+"editActivityProperty",
			editConnectorAction: authoringControllerPath+"editConnector",
			editInferenceRuleAction: authoringControllerPath+"editInferenceRule",
			editConsistencyRuleAction: authoringControllerPath+"editConsistencyRule",
			deleteConnectorAction: authoringControllerPath+"deleteConnector",
			deleteActivityAction: authoringControllerPath+"deleteActivity",
			deleteInteractiveServiceAction: authoringControllerPath+"deleteCallOfService",
			deleteInferenceRuleAction: authoringControllerPath+"deleteInferenceRule",
			deleteConsistencyRuleAction: authoringControllerPath+"deleteConsistencyRule",
			setFirstActivityAction: authoringControllerPath+"setFirstActivity",
			createConnectorAction: authoringControllerPath+"addConnector"
		});
	}
	
	//link the tree to the activity diagram:
	ActivityDiagramClass.relatedTree = 'tree-activity';
	
	function refreshActivityTree(){
		$.tree.reference('#tree-activity').refresh();
		$.tree.reference('#tree-activity').reselect();
	}
	
	function reselectActivityTree(){
		$.tree.reference('#tree-activity').reselect();
	}
	
</script>
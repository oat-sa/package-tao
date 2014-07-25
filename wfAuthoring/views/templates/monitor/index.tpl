<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.cancelProcess.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.deleteProcess.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.resumeProcess.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.pauseProcess.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.currentActivities.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.currentActivitiesLabel.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.processPreviousActivity.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.processNextActivity.js"></script>

<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.activityVariables.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.activityVariable.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.activityVariableLabel.js"></script>

<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.activityPreviousActivity.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>views/js/grid/wfengine.grid.activityNextActivity.js"></script>

<script type="text/javascript" src="<?=ROOT_URL?>wfEngine/views/js/wfApi/wfApi.min.js"></script>

<style>
	#filter-container { width:19%;  height:561px; }
	.main-container { height:584px; padding:0; margin:0; overflow:auto !important; }
	#monitoring-processes-container, #process-details-container { padding:0; height:50%; overflow:auto; }
	.current_activities_container { height:100%; }
	.currentActivities-subgrid .ui-jqgrid, .currentActivities-subgrid .jqgrow, .currentActivities-subgrid td { border: 0 none !important; border-right:1px solid #AAA !important; }
	.currentActivities-subgrid .ui-jqgrid-hdiv, .currentActivities-subgrid .ui-jqgrid-titlebar { display:none; }
	#process-details-tabs { height:269px; }

	.tabs-bottom { position: relative; }
	.tabs-bottom .ui-tabs-panel { height:100%; overflow: auto; }
	.tabs-bottom .ui-tabs-nav { position: absolute !important; left: 0; bottom: 0; right:0; padding: 0 0.2em 0.2em 0; }
	.tabs-bottom .ui-tabs-nav li { margin-top: -2px !important; margin-bottom: 1px !important; border-top: none; border-bottom-width: 1px; }
	#Monitor_processes .ui-tabs-selected { margin-bottom: inherit; padding-bottom: 0 }

	.activity-variable-edit-container { white-space:inherit; white-space:nowrap !important; }
	.activity-variable-value { display:block; margin: 3px 0; }
	.activity-variable-actions { white-space:inherit; white-space:nowrap !important; margin-bottom:7px; }
	.activity-variable-actions img { margin-top:3px; }
	a.activity-variable-action { border-color:#CCCCCC #AAAAAA #AAAAAA #CCCCCC; border-width:1px; border-style:solid; padding:3px 6px; cursor:pointer; margin-right:5px; text-decoration:none; }
</style>

<div id="filter-container" class="data-container tabs-bottom">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Filter')?>
	</div>
	<div id="tabs-1">
		<div id="facet-filter">
		</div>
	</div>
</div>

<div class="main-container">
	<div id="monitoring-processes-container">
		<table id="monitoring-processes-grid">
		</table>
	</div>
	<div id="process-details-container" class="tabs-bottom">
		<div id="process-details-tabs">
			<ul class="4ui-helper-hidden-accessible">
				<li><a href="#current_activities_container"><?=__("Current Activities")?></a></li>
				<li><a href="#history_process_container"><?=__("History")?></a></li>
			</ul>
			<div id="current_activities_container">
				<table id="current-activities-grid">
				</table>
			</div>
			<div id="history_process_container">
				<table id="history-process-grid">
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	//Global variable
	var monitoringGrid = null;
	var currentActivitiesGrid = null;
	var historyProcessGrid = null;

	//Selected process id
	//quick hack to test, to replace quickly
	var selectedProcessId = null;
	var selectedActivityExecutionId = null;

	//Workflow variables
	var wfVariables = [];

	//refresh the monitoring the target processes
	function refreshMonitoring(processesUri) {
		$.getJSON('<?=_url('monitorProcess', 'Monitor')?>',
			{
				'processesUri': processesUri
			},
			function(DATA) {
				monitoringGrid.refresh(DATA);
				if (selectedProcessId) {
					if (typeof DATA[selectedProcessId] != 'undefined') {
						//refresh does not work here ... strange
						currentActivitiesGrid.empty();
						currentActivitiesGrid.add(DATA[selectedProcessId]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']);
					}
				}
			}
		);
	}

	//load the monitoring interface functions of the parameter filter
	function loadMonitoring(filter) {
		$.getJSON('<?=_url('monitorProcess', 'Monitor')?>',
			{
				'filter': filter
			},
			function (DATA) {
				monitoringGrid.empty();
				currentActivitiesGrid.empty();
				historyProcessGrid.empty();

				monitoringGrid.add(DATA);
				selectedProcessId = null;
			}
		);
	}

	$(function(){
		require(['require', 'jquery', 'generis.facetFilter', 'grid/tao.grid'], function(req, $, GenerisFacetFilterClass) {
			//the grid model
			model = <?=$model?>;

			//workflow variables
			wfVariables = <?=$wfVariables?>;

			/*
			 * Instantiate the tabs
			 */
			var filterTabs = new TaoTabsClass('#filter-container', {'position':'bottom'});
			var processDetailsTabs = new TaoTabsClass('#process-details-tabs', {'position':'bottom'});

			/*
			 * instantiate the facet based filter widget
			 */
			var getUrl = '<?=_url('getFilteredInstancesPropertiesValues', 'Monitor')?>';
			//the facet filter options
			var facetFilterOptions = {
				'template' : 'accordion',
				'callback' : {
					'onFilter' : function(facetFilter){
						loadMonitoring(facetFilter.getFormatedFilterSelection());
					}
				}
			};
			//set the filter nodes
			var filterNodes = [
<?
	$empty = true;
	foreach ($properties as $property):
		if (!$empty) echo ','; ?>
				{
					id: '<?=md5($property->getUri())?>',
					label : '<?=$property->getLabel()?>',
					url : getUrl,
					options: {
						'propertyUri' 	: '<?= $property->getUri() ?>',
						'classUri' 	: '<?= $clazz->getUri() ?>',
						'filterItself': false
					}
				}
<?
		$empty = false;
	endforeach;
?>
			];
			//instantiate the facet filter
			var facetFilter = new GenerisFacetFilterClass('#facet-filter', filterNodes, facetFilterOptions);


			/*
			 * instantiate the monitoring grid
			 */
			//the monitoring grid options
			var monitoringGridOptions = {
				'height': $('#monitoring-processes-grid').parent().height(),
				'title': __('Monitoring processes'),
				'callback': {
					'onSelectRow': function(rowId) {
						//$('#monitoring-processes-grid').jqGrid('editRow', rowId);
						selectedProcessId = rowId;

						//display the process' current activities
						currentActivitiesGrid.empty();
						currentActivitiesGrid.add(monitoringGrid.data[rowId]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']);

						//display the process history
						$.getJSON('<?=_url('processHistory', 'Monitor')?>',
							{
								'uri':rowId
							},
							function (DATA) {
								historyProcessGrid.empty();
								historyProcessGrid.add(DATA);
							}
						);
					}
				}
			};
			//Override the monitoring model with the model of actions
			model['cancelProcess'] = {
				'id' 			: 'cancelProcess'
				, 'title' 		: 'Stop'
				, 'name' 		: 'cancelProcess'
				, 'widget' 		: 'CancelProcess'
				, 'weight'		: 0.5
				, 'align'		: 'center'
				, 'position'	: 5
			};
			model['deleteProcess'] = {
				'id' 			: 'deleteProcess'
				, 'title' 		: 'Delete'
				, 'name' 		: 'deleteProcess'
				, 'widget' 		: 'DeleteProcess'
				, 'weight'		: 0.5
				, 'align'		: 'center'
				, 'position'	: 6
			};
			model['resumeProcess'] = {
				'id' 			: 'resumeProcess'
				, 'title' 		: 'Resume'
				, 'name' 		: 'resumeProcess'
				, 'widget' 		: 'ResumeProcess'
				, 'weight'		: 0.5
				, 'align'		: 'center'
				, 'position'	: 7
			};
			model['pauseProcess'] = {
				'id' 			: 'pauseProcess'
				, 'title' 		: 'Pause'
				, 'name' 		: 'pauseProcess'
				, 'widget' 		: 'PauseProcess'
				, 'weight'		: 0.5
				, 'align'		: 'center'
				, 'position'	: 8
			};
			//instantiate the grid widget
			monitoringGrid = new TaoGridClass('#monitoring-processes-grid', model, '', monitoringGridOptions);
			//load monitoring grid
			loadMonitoring(null);

			//width/height of the subgrids
			var subGridWith = $('#current_activities_container').width() - 12 /* padding */;
			var subGridHeight = $('#current_activities_container').height() - 45;

			/**
			 * Instantiate the details area
			 */
			//the grid model
			var currentActivitiesModel = model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']['subgrids'];
			//Override the current activities model with the model of actions
			currentActivitiesModel['previousActivity'] = {
				'id' 			: 'previousActivity'
				, 'title' 		: 'Previous'
				, 'name' 		: 'previousActivity'
				, 'widget' 		: 'ActivityPreviousActivity'
				, 'weight'		: 0.5
				, 'align'		: 'center'
				, 'position'	: 5
			};
			currentActivitiesModel['nextActivity'] = {
					'id' 			: 'nextActivity'
					, 'title' 		: 'Next'
					, 'name' 		: 'nextActivity'
					, 'widget' 		: 'ActivityNextActivity'
					, 'weight'		: 0.5
					, 'align'		: 'center'
					, 'position'	: 6
				};
			//the current activities grid options
			 var currentActivitiesOptions = {
				'height' : subGridHeight,
				'width'  : subGridWith,
				'title'  : __('Current activities')
			};
			//instantiate the grid widget
			currentActivitiesGrid = new TaoGridClass('#current-activities-grid', currentActivitiesModel, '', currentActivitiesOptions);


			/**
			 * Instantiate the history area
			 */
			//the grid model
			var historyProcessModel = <?=$historyProcessModel?>;
			//the history grid options
			 var historyProcessOptions = {
				'height' : subGridHeight,
				'width'  : subGridWith,
				'title' 	: __('Process History')
			};
			//instantiate the grid widget
			historyProcessGrid = new TaoGridClass('#history-process-grid', historyProcessModel, '', historyProcessOptions);
		});
	});
</script>

<?include(BASE_PATH.'/views/templates/footer.tpl');?>

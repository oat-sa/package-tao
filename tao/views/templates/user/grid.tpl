<!--<div id="main-container" class="main-container">-->
<div class="main-container">
	<div id="monitoring-processes-container">
		<table id="monitoring-processes-grid" />
	</div>
</div>

<script type="text/javascript">
var monitoringGrid = null;
var monitoringFilter = null;

//load the monitoring interface functions of the parameter filter
function loadMonitoring(filter) {
	monitoringFilter = filter;
	params = '<?=$gridParams?>';
	$.getJSON(root_url+'tao/SaSUsers/getGridData'+params,
		{
			'filter':filter
		},
		function (DATA) {
			//clean the grid
			monitoringGrid.empty();
			//add the new data
			insertedRows = 0;
			insertedRows = monitoringGrid.add(DATA);
			selectedProcessId = null;

			$('#monitoring-processes-grid').jqGrid('setGridParam',{rowNum:insertedRows}).trigger("reloadGrid");
		}
	);
}

$(function() {
	require(['require', 'jquery', 'grid/tao.grid'], function(req, $) {
		model = <?=$model?>;
		var monitoringGridOptions = {
			height: 769,
			title: 'TAO Users'
		};
		monitoringGrid = new TaoGridClass('#monitoring-processes-grid', model, '', monitoringGridOptions);

		//load monitoring grid
		loadMonitoring();
	});
});
</script>

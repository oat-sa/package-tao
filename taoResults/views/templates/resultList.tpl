<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>taoResults/views/css/resultList.css" />

<div id="filter-container" class="data-container tabs-bottom">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Results Selection Filters')?>
	</div>
	<div id="tabs-1">
		<div id="facet-filter">
		</div>
	</div>
</div>

<div id="main-container">
	<div id="inspect-results-container">
		<table id="inspect-results-grid">
		</table>
		<div id="pagera1"></div>
	</div>
	<div align="right">
		<span id="buildTableButton" class="ui-state-default ui-corner-all">
			<?=__('Export Table')?>
		</span>
	</div>
</div>

<script type="text/javascript">
//Global variable
var deliveryResultGrid = null;

//load the results interface functions of the parameter filter
function loadResults(filter) {
	$.getJSON('<?=_url("getResults")?>',
		{
			'filter':filter
		},
		function (DATA) {
			deliveryResultGrid.empty();
			deliveryResultGrid.add(DATA);

		}
	);
}

$(function(){
	require(['jquery', 'i18n', 'helpers', 'generis.facetFilter', 'grid/tao.grid'], function($, __, helpers, GenerisFacetFilterClass) {


		//the grid model
		model = <?=$model?>;
		/*
		 * instantiate the facet based filter widget
		 */
		var getUrl = '<?=_url("getFilteredInstancesPropertiesValues")?>';

		//the facet filter options
		var facetFilterOptions = {
			template: 'accordion',
			callback: {
				'onFilter': function(facetFilter) {
					loadResults(facetFilter.getFormatedFilterSelection());
				}
			},

		};

		//set the filter nodes
		var filterNodes = [
			<?foreach($properties as $property):?>
			{
				id: '<?=md5($property->uriResource)?>',
				label: '<?=$property->getLabel()?>',
				url: getUrl,
				options: {
					'propertyUri': '<?= $property->uriResource ?>',
					'classUri': '<?= $clazz->uriResource ?>',
	        'filterItself': false
				}
			},
			<?endforeach;?>
		];
		//instantiate the facet filter
		var facetFilter = new GenerisFacetFilterClass('#facet-filter', filterNodes, facetFilterOptions);

		$( "#facet-filter" ).tabs( "option", "show", { effect: "blind", duration: 10000 });

		/*
		*  Results Delviery Grid
		 */

		/*
		 * instantiate the delivery results grid
		 */
		//the delivery results grid options
		var resultsGridOptions = {
			'height': 'auto',
			width: (parseInt($("#inspect-results-grid").width()-40)),
			'title': __('Delivery results'),
			'callback': {
				'onSelectRow': function(rowId) {
					label = deliveryResultGrid.data[rowId]['http://www.w3.org/2000/01/rdf-schema#label'];
					if (!label) {
						label = __('Delivery Result');
					}
					//helpers.openTab(label, '<?=_url('viewResult', 'Results')?>?uri='+escape(rowId));
				}
			}
		};
		//tao grid class override
		TaoGridClass.prototype.editRow = function(rowId){;}
		//instantiate the grid widget
		deliveryResultGrid = new TaoGridClass('#inspect-results-grid', model, '', resultsGridOptions);
		//load delivery results grid
		loadResults(null);

		//width/height of the subgrids
		var subGridWith = $('#current_activities_container').width() - 12 /* padding */;
		var subGridHeight = $('#current_activities_container').height() - 45;

		$('#buildTableButton').click(function(e) {
			e.preventDefault();
			filterSelection = facetFilter.getFormatedFilterSelection();
			uri = '<?=_url('index','ResultTable')?>?'+$.param({'filter': filterSelection});
			helpers.openTab(__('custom table'), uri);
		});

	});


});


</script>
<?php
Template::inc('footer.tpl', 'tao');
?>
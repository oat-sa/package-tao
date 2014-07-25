<style>
<!--
.ui-helper-horizontal { display:inline-block; }
-->
</style>

<div id="facet-filter"></div>
<table id="result-list"></table>
<div id="result-list-pager"></div>
<div class="ui-state-error" style="display:none"><?=__('No result found')?></div>
<br />

<script type="text/javascript">
$(document).ready(function(){

	/*
	 * instantiate the filter nodes widget
	 */

	var getUrl = root_url + 'taoItems/items/getFilteredInstancesPropertiesValues';
	//the facet filter options
	var facetFilterOptions = {
		'template' : 'accordion',
		'callback' : {
			'onFilter' : function(facetFilter){
				refreshResult(facetFilter);
			}
		}
	};
	//set the filter nodes
	var filterNodes = [
		<?foreach($properties as $property):?>
		{
			id					: '<?=md5($property->getUri())?>'
			, label				: '<?=$property->getLabel()?>'
			, url				: getUrl
			, options 			:
			{
				'propertyUri' 	: '<?= $property->getUri() ?>'
				, 'classUri' 	: '<?= $clazz->getUri() ?>'
			}
		},
		<?endforeach;?>
	];

	require(['require', 'jquery', 'generis.facetFilter', 'grid/tao.grid'], function(req, $, GenerisFacetFilterClass) {
		//instantiate the facet filter
		facetFilter = new GenerisFacetFilterClass('#facet-filter', filterNodes, facetFilterOptions);

		/*
		 * instantiate the result widget
		 */

		//define jqgrid column
		var properties = ['id', 'label'
		<?foreach(get_data('properties') as $uri => $property):?>
			 ,'<?=$property->getLabel()?>'
		<?endforeach?>
			<?php //,__('Actions')?>
		];

		//define jqgrid model
		var model = [
	     	{name:'id',index:'id', width: 25, align:"center", sortable: false},
	    	{name:'property_0',index:'property_0', width: 75, align:"center", sortable: false},
		<?for($i = 1; $i-1 < count(get_data('properties')); $i++):?>
			 {name:'property_<?=$i?>',index:'property_<?=$i?>'},
		<?endfor?>
			<?php //{name:'actions',index:'actions', align:"center", sortable: false}, ?>
		];

		//instantiate jqgrid
		$("#result-list").jqGrid({
			datatype: "local",
			colNames: properties ,
			colModel: model,
			width: parseInt($("#result-list").parent().width()) - 15,
			sortname: 'id',
			sortorder: "asc",
			caption: __("Filter results")
		});
	});


	//function used to refresh the result functions of the filter
	function refreshResult(facetFilter)
	{
		//Refresh the result
		$.getJSON (root_url+'taoItems/items/searchInstances'
			,{
				'classUri' : '<?= $clazz->getUri() ?>'
				, 'filter' : facetFilter.getFormatedFilterSelection()
			}
			, function (DATA) {
				// empty the grid
				var myGrid = $("#result-list"); // the variable you probably have already somewhere
				var gridBody = myGrid.children("tbody");
				var firstRow = gridBody.children("tr.jqgfirstrow");
				gridBody.empty().append(firstRow);

				for(var i in DATA) {
					var row = {'id':i};
					for (var j in DATA[i].properties) {
						if (DATA[i].properties[j] == null){
							row['property_'+j] = '';
						} else {
							row['property_'+j] = DATA[i].properties[j];
						}
					}
					jQuery("#result-list").jqGrid('addRowData', i+1, row);
				}
			}
		);
	}
});
</script>

<!--<?include(dirname(__FILE__).'/../portail/footer.tpl');?>-->

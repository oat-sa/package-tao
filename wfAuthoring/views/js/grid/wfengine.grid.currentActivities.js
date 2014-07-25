/**
 * This class is a grid column adapter used to format cells to fit specific needs

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridClassCurrentActivitiesAdapter constructor
 */
function TaoGridCurrentActivitiesAdapter(){}

TaoGridCurrentActivitiesAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = '';

	/*for(var i in cellvalue){
		returnValue += cellvalue[i]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser'] 
			+ ' - ' + cellvalue[i]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf']
			+ '<br/>' ;
	}*/
	returnValue = '<div class="currentActivities-subgrid"><table id="current_activities_'+options.rowId.substr(options.rowId.indexOf('#')+1)+'"></table></div>';
	
	return returnValue;
};

TaoGridCurrentActivitiesAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	var subgridId = $(cell).find('table').attr('id');
	var model = [
		{'id':'currentActivities', 'title':'Current activities', 'widget':'CurrentActivitiesLabel' }
		, {'id':'previousActivity', 'title':'Previous', 'widget':'ProcessPreviousActivity', 'align':'center', 'weight':0.1, 'preFormatter':'rowId' }
		, {'id':'nextActivity', 'title':'Next', 'widget':'ProcessNextActivity', 'align':'center', 'weight':0.1 }
	];
	var options = {};
	var subGrid = new TaoGridClass('#'+subgridId, model, '', options);
//	alert ('quoi la');
	subGrid.add(grid.getCellData(rowId, columnId));
};

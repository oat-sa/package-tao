/**
 * This class is a grid column adapter used to format cells to fit specific needs

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridCurrentActivitiesLabelAdapter constructor
 */
function TaoGridCurrentActivitiesLabelAdapter(){}

TaoGridCurrentActivitiesLabelAdapter.preFormatter = function(grid, rowData, rowId, columnId)
{
	var returnValue = {
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser' : grid.data[rowId]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser']
		, 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf' : grid.data[rowId]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf']
	};
	return returnValue;
}

TaoGridCurrentActivitiesLabelAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = '';

	returnValue += cellvalue['http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser'] 
		+ ' - ' + cellvalue['http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf']
		+ '<br/>' ;
	
	return returnValue;
};

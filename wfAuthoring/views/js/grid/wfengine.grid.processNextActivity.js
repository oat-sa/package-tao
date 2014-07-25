/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt cell to switch to the next activity

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridNextActivityAdapter constructor
 */

function TaoGridProcessNextActivityAdapter(){}

TaoGridProcessNextActivityAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = '<a href="#"><img src="'+root_url+'/wfEngine/views/img/next.png"/></a>';
	return returnValue;
}

TaoGridProcessNextActivityAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	var activityExecutionUri = rowId;
	$(cell).find('a').one('click', function(){
		wfApi.ActivityExecution.next(activityExecutionUri, function(data){
			var rowData = grid.getRowData(rowId);
			refreshMonitoring([rowData['http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsProcessExecution']]);
		}, function(){
			alert('unable to move the cursor to the next activity '+activityExecutionUri);
		});
	});
}

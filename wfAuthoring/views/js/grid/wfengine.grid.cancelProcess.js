/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt cell to stop processes

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridCancelProcessAdapter constructor
 */

function TaoGridCancelProcessAdapter(){}

TaoGridCancelProcessAdapter.preFormatter = function(grid, rowData, rowId, columnId)
{
	var returnValue = rowId;
	return returnValue;
}

TaoGridCancelProcessAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = '<a href="#"><img src="'+root_url+'/wfEngine/views/img/stop.png"/></a>';
	return returnValue;
}

TaoGridCancelProcessAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	var processExecutionUri = grid.data[rowId][columnId];
	$(cell).find('a').one('click', function(){
		wfApi.ProcessExecution.cancel(processExecutionUri, function(data){
			refreshMonitoring([rowId]);
		}, function(){
			alert('unable to cancel the process execution '+processExecutionUri);
		});
	});
}

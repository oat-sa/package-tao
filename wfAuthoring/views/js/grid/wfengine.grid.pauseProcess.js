/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt cell to pause processes

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridPauseProcessAdapter constructor
 */

function TaoGridPauseProcessAdapter(){}

TaoGridPauseProcessAdapter.preFormatter = function(grid, rowData, rowId, columnId)
{
	var returnValue = rowId;
	return returnValue;
}

TaoGridPauseProcessAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = '<a href="#"><img src="'+root_url+'/wfEngine/views/img/status_paused.png"/></a>';
	return returnValue;
}

TaoGridPauseProcessAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	var processExecutionUri = grid.data[rowId][columnId];
	$(cell).find('a').one('click', function(){
		wfApi.ProcessExecution.pause(processExecutionUri, function(data){
			refreshMonitoring([rowId]);
		}, function(){
			alert('unable to pause the process execution '+processExecutionUri);
		});
	});
}

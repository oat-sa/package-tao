/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt cell to resume processes

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridResumeProcessAdapter constructor
 */

function TaoGridResumeProcessAdapter(){}

TaoGridResumeProcessAdapter.preFormatter = function(grid, rowData, rowId, columnId)
{
	var returnValue = rowId;
	return returnValue;
}

TaoGridResumeProcessAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = '<a href="#"><img src="'+root_url+'/wfEngine/views/img/resume.png"/></a>';
	return returnValue;
}

TaoGridResumeProcessAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	var processExecutionUri = grid.data[rowId][columnId];
	$(cell).find('a').one('click', function(){
		wfApi.ProcessExecution.resume(processExecutionUri, function(data){
			refreshMonitoring([rowId]);
		}, function(){
			alert('unable to resume the process execution '+processExecutionUri);
		});
	});
}

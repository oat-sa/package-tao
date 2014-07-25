
$(document).ready(function () {
    require(['require', 'jquery', root_url  + 'tao/views/js/jquery.fileDownload.js'],
    function () {

	$('#filter').val(filter);
	$('.dataResult').html(function(index, oldhtml) {
	    
	    return oldhtml;
	    });
	$('#filter').change(function(e) {
	    url = root_url + 'taoResults/Results/viewResult';
	    data.filter = $( this ).val();
	    helpers._load(helpers.getMainContainerSelector(uiBootstrap.tabs), url, data);
	    });
	    
	 
	


	$('.traceDownload').on("click", function (e) {
	    var variableUri = $(this).val();
	    $.fileDownload(root_url + 'taoResults/Results/getTrace', {
	      preparingMessageHtml: __("We are preparing your report, please wait..."),
	      failMessageHtml: __("There was a problem generating your report, please try again."),
	      successCallback: function () { },
	      httpMethod: "POST",
	       ////This gives the current selection of filters (facet based query) and the list of columns selected from the client (the list of columns is not kept on the server side class.taoTable.php
	      data: {'variableUri': variableUri}
	    });
	});



    });
       });


$(document).ready(function(){
	$('#finishButton').click(function() {
		api.finish();
	});

	//preview form toggling
	$('#preview-options-opener').click(function(){
		var fromClass 	= 'ui-icon-carat-1-s';
		var toClass 	= 'ui-icon-carat-1-e';
		if($('#preview-options').css('display') == 'none'){
			fromClass 	= 'ui-icon-carat-1-e';
			toClass 	= 'ui-icon-carat-1-s';
		}
		$(this).find('span.ui-icon').switchClass(fromClass,toClass);
		$('#preview-options').toggle();
	});

	//prevent wrong iframe loading from chrome
	if($.browser.webkit){
		$("#preview-container").attr('src', $("#preview-container").attr('src'));
	}
});
define(['jquery', 'helpers', 'ui/feedback'], function($, helpers, feedback){
	return {
        start : function(){
            
            $('.script').click(function(event) {
            	var action = $(event.target).data('action');
            	$.ajax({
                    type: "POST",
                    url: helpers._url(action, 'ScriptRunner', 'taoDevTools'),
                    dataType: 'json',
                    success: function(data) {
                        helpers.loaded();
                        if (data.success) {
                            feedback().success(data.message);
                        } else {
                        	feedback().error(data.message);
                        }
                    }
            	});
            });
            
        }
    };

});

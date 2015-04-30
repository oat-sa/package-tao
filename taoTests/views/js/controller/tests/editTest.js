
define(['module', 'jquery','ui/lock'],
	function(module, $, lock){

        var editTestController = {
            start : function(options){
            	$('#lock-box').each(function() {lock($(this)).register()});
            }
        };

        return editTestController;
});

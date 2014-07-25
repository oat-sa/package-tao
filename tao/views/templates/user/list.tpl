<div class="main-container">
	<table id="user-list"></table>
	<div id="user-list-pager"></div>
</div>
<script type="text/javascript">
require(['jquery', 'i18n', 'helpers', 'grid/tao.grid'], function($, __, helpers) {
	var $tabs = $('#tabs');
        
        function editUser(uri){
		var index = helpers.getTabIndexByName('edit_user');
		if(index && uri){
			var editUrl = "<?=_url('edit', 'Users', 'tao')?>" + '?uri=' + uri;
			$tabs.tabs('url', index, editUrl);
			$tabs.tabs('enable', index);
			helpers.selectTabByName('edit_user');
		}
	}
        
	function removeUser(uri){
		if(confirm("<?=__('Please confirm user deletion')?>")){
			window.location = "<?=_url('delete', 'Users', 'tao')?>" + '?uri=' + uri;
		}
	}
        
        $tabs.tabs('disable', helpers.getTabIndexByName('edit_user'));
        var myGrid = $("#user-list").jqGrid({
                url: "<?=_url('data', 'Users', 'tao')?>",
                datatype: "json",
                colNames:[ __('Login'), __('Name'), __('Mail'), __('Roles'), __('Data Language'), __('Interface Language'), __('Actions')],
                colModel:[
                        {name:'login',index:'login', sortable: false},
                        {name:'name',index:'name', sortable: false},
                        {name:'email',index:'email', width: '200', sortable: false},
                        {name:'roles',index:'roles', sortable: false},
                        {name:'deflg',index:'deflg', align:"center", sortable: false},
                        {name:'uilg',index:'uilg', align:"center", sortable: false},
                        {name:'actions',index:'actions', align:"center", sortable: false}
                ],
                rowNum:20,
                height:350,
                width: (parseInt($("#user-list").width()) - 2),
                pager: '#user-list-pager',
                viewrecords: false,
                caption: __("Users"),
                gridComplete: function(){
                        $.each(myGrid.getDataIDs(), function(index, elt){
                                myGrid.setRowData(elt, {
                                        actions: "<a id='user_editor_"+elt+"' href='#' class='user_editor nd' ><img class='icon' src='<?=BASE_WWW?>img/pencil.png' alt='<?=__('Edit user')?>' /><?=__('Edit')?></a> | " +
                                        "<a id='user_deletor_"+elt+"' href='#' class='user_deletor nd' ><img class='icon' src='<?=BASE_WWW?>img/delete.png' alt='<?=__('Delete user')?>' /><?=__('Delete')?></a>"
                                });
                        });
                        $(".user_editor").click(function(){
                                editUser(this.id.replace('user_editor_', ''));
                        });
                        $(".user_deletor").click(function(){
                                removeUser(this.id.replace('user_deletor_', ''));
                        });
                        $(window).unbind('resize').bind('resize', function(){
                                myGrid.jqGrid('setGridWidth', (parseInt($(".main-container").eq(0).width()) - 2));
                        });
                }
        });
        myGrid.navGrid('#user-list-pager',{edit:false, add:false, del:false});

        helpers._autoFx();

});
</script>
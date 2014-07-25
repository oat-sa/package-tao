<script type="text/javascript" src="<?=ROOT_URL?>/tao/views/js/Updater.js"></script>
<link
	rel="stylesheet" href="<?=ROOT_URL?>/tao/views/css/update.css"
	type="text/css" />

<div id="update-title"
	class="ui-widget-header ui-corner-top ui-state-default">
	<?=__("TAO updater")?>
</div>

<div id="updating-container"
	class="ui-widget-content ui-corner-bottom">

	<div id="check_update-container"
		class="ext-home-container ui-state-highlight">
		<span><?=__('Check if updates are available for your version of TAO')?>
		:&nbsp; <input type="button" value="check update" id="checkUpdateButton" />
	</div>

	<div id="available_update-container" class="ui-helper-hidden ext-home-container ui-state-highlight">
		<span><?=__('Updates are available. Click on update to update your version of TAO')?></span>
		:&nbsp; <input type="button" value="Update" id="updateButton" />
		<br/><br/>
		<div id="update-table-container">
			<table id="update-grid" />
		</div>
	</div>
	
	<div id="no_update-container" class="ui-helper-hidden ext-home-container ui-state-highlight">
		<span><?=__('No update available')?></span>
	</div>
</div>

<script type="text/javascript">
        $(function(){  
            var myUpdater = new UpdaterClass('update-grid');
			$('#checkUpdateButton').click(function(){
				$('#check_update-container').hide();
            	if (myUpdater.checkUpdate()){
            		$('#available_update-container').show();
            		myUpdater.showUpdatesDetails();
                } else {
            		$('#no_update-container').show();
                }
            });

			$('#updateButton').click(function(){
				myUpdater.update ();
			});
        });
</script>

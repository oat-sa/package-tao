<div id="item-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Authoring')?>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id='authoringButton' name='authoring' type='button' value='<?=__('Edit workflow')?>'/>
	</div>
</div>
<script type="text/javascript">
require(['jquery', 'helpers'], function($, helpers) {
        $('#authoringButton').click(function(e) {
                //e.preventDefault();
                uri = '<?=_url('authoring', 'Process', 'wfAuthoring', array('uri' => get_data('processUri')))?>';
                helpers.openTab('<?=get_data('label')?>', uri);
        });
});
</script>

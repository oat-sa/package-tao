<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">

<div id="import-container" style="float: left;margin: 5px; ">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Import')?>
	</div>
	<div class="ui-widget ui-widget-content ">
	<?if(get_data('importErrors')):?>
		<fieldset class='ui-state-error'>
			<legend><strong><?=__('Error during file import')?></strong></legend>
			<ul id='error-details'>
			<?foreach(get_data('importErrors') as $ierror):?>
				<li><?=$ierror?></li>
			<?endforeach?>
			</ul>
		</fieldset>
	<?endif?>
	
	<?=get_data('importForm')?>
		<div class="breaker"></div>
	</div>
	</div>
<div id="export-container" style="float: left;margin: 5px;">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Export ')?>
	</div>
	<div class="ui-widget ui-widget-content">
	    <?=get_data('exportForm')?> 
		<div class="breaker"></div>
	</div>
</div>


	<div class="ext-home-container">
	</div>
</div>
<?if (has_data('download')):?>
	<iframe src="<?=get_data('download');?>" style="height: 0px;min-height: 0px"></iframe>
<?php endif;?>
<?php
Template::inc('footer.tpl', 'tao')
?>

<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?=Template::css('lists.css', 'tao')?>" />
<div class="main-container">
	<div id="list-container">
		<?foreach(get_data('lists') as $i => $list):?>
				<div id='list-data_<?=$list['uri']?>' class="listbox">
					<fieldset>
						<legend><span><?=$list['label']?></span></legend>
						<div class="list-elements" id='list-elements_<?=$list['uri']?>'>
							<ol>
								<?foreach($list['elements'] as $level => $element):?>
									<li id="list-element_<?=$level?>">
										<span class="list-element" id="list-element_<?=$level?>_<?=$element['uri']?>" ><?=$element['label']?></span>
									</li>
								<?endforeach?>
							</ol>
						</div>
						<div class="list-controls">
						<?if($list['editable']):?>
							<a href="#" class="list-editor" id='list-editor_<?=$list['uri']?>'>
								<img src="<?=Template::img('pencil.png', 'tao')?>" class="icon" /><?=__('Edit')?>
							</a>
							|
							<a href="#" class="list-deletor" id='list-deletor_<?=$list['uri']?>'>
								<img src="<?=Template::img('delete.png', 'tao')?>" class="icon" /><?=__('Delete')?>
							</a>
						<?else:?>
							<?=__('Edit')?> | <?=__('Delete')?>

						<?endif?>
						</div>
					</fieldset>
				</div>
		<?endforeach?>

		<div style="margin-top:10px">
			<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
				<strong><?=__('Create a list')?></strong>
			</div>
			<div id="form-container" class="ui-widget-content ui-corner-bottom">
				<?=get_data('form')?>
			</div>
		</div>
	</div>
</div>

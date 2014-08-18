<?if(get_data('actions')):?>
<div id="action-title" class='ui-widget ui-widget-header ui-state-default container-title  ui-corner-top'><?=__('Actions')?></div>
	<div id="action-items" class="ui-widget ui-widget-content ui-corner-bottom">
	<?foreach(get_data('actions') as $action):?>
		<div id="action_<?=$action['name']?>">
		<?if($action['disabled']):
			// Snippet: determine what is the action icon depending on the current extension.
			if (get_data('shownExtension') !== false && file_exists(ROOT_PATH .  $action['ext']  .  '/views/img/actions/' . $action['name'] . '_disabled.png')) {
				$actionIcon = ROOT_URL . $action['ext'].  '/views/img/actions/' . $action['name'] . '_disabled.png';
			}else{
				$actionIcon = TAOBASE_WWW . 'img/actions/' . $action['name'] . '_disabled.png';
			}?>
			<img src="<?=$actionIcon?>" /><br />
			<span><?=$action['display']?></span>
		<?else:
			// Snippet: determine what is the action icon depending on the current extension.
			if (get_data('shownExtension') !== false && file_exists(ROOT_PATH . $action['ext'] .  '/views/img/actions/' . $action['name'] . '.png')) {
				$actionIcon = ROOT_URL . $action['ext'] .  '/views/img/actions/' . $action['name'] . '.png';
			} else {
				$actionIcon = TAOBASE_WWW . 'img/actions/' . $action['name'] . '.png';
			}
			if($action['js']):?>
				<a href="<?=$action['url']?>" data-action="<?=$action['js']?>" data-uri="<?=$action['uri']?>" data-class-uri="<?=$action['classUri']?>" title="<?=$action['rowName']?>">
				<img src="<?=$actionIcon?>" /><br />
				<?=$action['display']?></a>
			<?else:?>
				<a class="<?if(!$action['reload']){?>nav<?}?>" href="<?=$action['url']?>?uri=<?=$action['uri']?>&classUri=<?=$action['classUri']?>" title="<?=$action['display']?>">
				<img src="<?=$actionIcon?>" /><br />
				<?=$action['display']?></a>
			<?endif;?>
		<?endif;?>
	</div>
	<?endforeach;?>
</div>
<?endif;?>
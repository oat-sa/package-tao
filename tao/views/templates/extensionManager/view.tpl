<link rel="stylesheet" type="text/css" href="<?= TAOBASE_WWW ?>css/extensionManager.css" />

<? if(isset($message)): ?>
<div id="message">
	<pre><?= $message; ?></pre>
</div>
<? endif; ?>

<div id="extensions-manager-title" class="ui-widget-header ui-corner-top ui-state-default">
	<?= __('Installed Extensions') ?>
</div>
<div id="extensions-manager-container" class="ui-widget-content ui-corner-bottom">
	<table summary="modules" class="maximal">
		<thead>
			<tr>
				<th class="bordered"></th>
				<th class="bordered author"><?= __('Author'); ?></th>
				<th class="version"><?= __('Version'); ?></th>
				<!-- <th><?= __('Loaded'); ?></th>  -->
				<!-- <th><?= __('Loaded at Startup'); ?></th> -->
			</tr>
		</thead>
		<tbody>
		<? foreach(get_data('installedExtArray') as $extensionObj): ?>
		<? if($extensionObj->getId() !=null): ?>
			<tr>
				<td class="ext-id bordered"><?= $extensionObj->getName(); ?></td>
				<td class="bordered"><?= str_replace(',', '<br />', $extensionObj->getAuthor()) ; ?></td>
				<td><?= $extensionObj->getVersion(); ?></td>
			</tr>
		<? endif; ?>
		<? endforeach;?>
		</tbody>
	</table>
</div>

<div id="available-extensions-title" class="ui-widget-header ui-corner-top ui-state-default">
	<?= __('Available Extensions') ?>
</div>
<div id="available-extensions-container" class="ui-widget-content ui-corner-bottom">
	<? if (count(get_data('availableExtArray')) > 0): ?>
	<form action="<?= BASE_URL; ?>/ExtensionsManager/install" metdod="post">
		<table summary="modules" class="maximal">
			<thead>
				<tr>
					<th class="bordered"></th>
					<th class="bordered author"><?= __('Author'); ?></th>
					<th class="bordered version"><?= __('Version'); ?></th>
					<th class="bordered require"><?= __('Requires'); ?></th>
					<th class="install"></th>
				</tr>
			</thead>
			<tbody>
				<? foreach(get_data('availableExtArray') as $k => $ext): ?>
				<tr id="<?= $ext->getId();?>">
					<td class="ext-name bordered"><?= $ext->getName(); ?></td>
					<td class="bordered"><?= $ext->getAuthor(); ?></td>
					<td class="bordered"><?= $ext->getVersion(); ?></td>
					<td class="dependencies bordered">
						<ul>
						<? foreach ($ext->getDependencies() as $req => $version): ?>
							<li class="ext-id ext-<?= $req ?><?= array_key_exists($req, get_data('installedExtArray')) ? ' installed' : '' ?>" rel="<?= $req ?>"><?= $req ?></li>
						<? endforeach; ?>
						</ul>
					</td>
					<td class="install">
						<input name="ext_<?= $ext->getId();?>" type="checkbox" />
					</td>
				</tr>
				<? endforeach; ?>
			</tbody>
		</table>
		<div class="actions">
			<input class="install" id="installButton" name="install_extension" value="<?= __('Install') ?>" type="submit" disabled="disabled" />
		</div>
	</form>
	<? else: ?>
	<div id="noExtensions" class="ui-state-highlight">
		<?= __('No extensions available.') ?>
	</div>
	<? endif; ?>
</div>

<div id="installProgress" title="<?= __('Installation...') ?>">
	<div class="progress"><div class="bar"></div></div>
	<p class="status">...</p>
	<div class="console"></div>
</div>
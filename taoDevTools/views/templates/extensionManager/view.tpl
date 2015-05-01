<link rel="stylesheet" type="text/css" href="<?= BASE_WWW ?>css/extensionManager.css" />

<?php if(isset($message)): ?>
<div id="message">
	<pre><?= $message; ?></pre>
</div>
<?php endif; ?>
<div class="actions tao-scope">
  <button class="btn-success small" type="button" id="installButton" disabled="disabled"><span class="icon-tools"></span> <?= __('Apply') ?></button>
  <button class="btn-info small" type="button" id="addButton"><span class="icon-add"></span> <?= __('Create new') ?></button>
</div>
	<form action="<?= _url('install', 'ExtensionsManager'); ?>" metdod="post" class="tao-scope" id="available-extensions-container">
		<table summary="modules" class="matrix">
			<thead>
				<tr>
					<th class=" install"></th>
				<th></th>
					<th><?= __('Description'); ?></th>
					<th><?= __('License'); ?></th>
					<th class=" require"><?= __('Requires'); ?></th>
					<th class=" version"><?= __('Version'); ?></th>
					<th class=" version"><?= __('Installed'); ?></th>
					<th style="width: 200px"><?= __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach(get_data('extensions') as $k => $ext): ?>
				<tr id="<?= $ext->getId();?>">
					<td class="install">
					   <?php if (!common_ext_ExtensionsManager::singleton()->isInstalled($ext->getId())) :?>
						<input name="ext_<?= $ext->getId();?>" type="checkbox" />
						<?php else:?>
						<span class="icon-checkbox-checked" style="color: #0E914B"></span>
						<?php endif;?>
					</td>
				<td class="ext-name "><?= $ext->getId(); ?></td>
					<td><?= $ext->getManifest()->getLabel(); ?> (<?= $ext->getManifest()->getDescription(); ?>)</td>
					<td><?= $ext->getManifest()->getLicense(); ?></td>
					<td class="dependencies ">
						<ul class="plain">
						<?php foreach ($ext->getDependencies() as $req => $version): ?>
						  <?php if (!in_array($req, get_data('installedIds'))) : ?>
							<li class="ext-id ext-<?= $req ?>" rel="<?= $req ?>"><?= $req ?></li>
							<?php endif;?>
						<?php endforeach; ?>
						</ul>
					</td>
					<td class=" version"><?= $ext->getVersion(); ?></td>
					<td class=" version"><?= common_ext_ExtensionsManager::singleton()->getInstalledVersion($ext->getId()); ?></td>
					<td>
					<?php if (common_ext_ExtensionsManager::singleton()->isInstalled($ext->getId())) :
                            if (!common_ext_ExtensionsManager::singleton()->isEnabled($ext->getId())) : ?>
    					           <button class="btn-info small enableButton" type="button" data-extid="<?=$ext->getId()?>"><?= __('Enable') ?></button>
    					    <?php elseif (!helpers_ExtensionHelper::mustBeEnabled($ext)) : ?>
    					           <button class="btn-warning small disableButton" type="button" data-extid="<?=$ext->getId()?>"><?= __('Disable') ?></button>
				            <?php endif;?>
    					    <?php if (!helpers_ExtensionHelper::isRequired($ext) && !is_null($ext->getManifest()->getUninstallData())) : ?>
    					           <button class="btn-warning small uninstallButton" type="button" data-extid="<?=$ext->getId()?>"><?= __('Uninstall') ?></button>
					       <?php endif;?>
					   <?php endif;?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</form>

<div id="installProgress" title="<?= __('Installation...') ?>">
	<div class="progress"><div class="bar"></div></div>
	<p class="status">...</p>
	<div class="console"></div>
</div>
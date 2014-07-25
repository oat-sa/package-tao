<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/acl.css" />

<div class="main-container">
	<div class="aclContainer">
		<div id="aclRoles-title" class="ui-widget-header ui-corner-top ui-state-default">
			<?= __('Available roles')?>
		</div>
		<div id="aclRoles" class="ui-widget-content ui-corner-bottom">
			<form>
				<div>
					<select id="roles" name="roles" size="1">
						<option value=""><?= __('Roles') ?>...</option>
						<?php foreach (get_data('roles') as $r): ?>
							<option value="<?= $r['id'] ?>"><?= $r['label'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</form>
		</div>
	</div>

	<div class="aclContainer">
		<div id="aclModules-title" class="ui-widget-header ui-corner-top ui-state-default">
			<?=__('Modules')?>
		</div>
		<div id="aclModules" class="ui-widget-content ui-corner-bottom">
			<ul class="group-list"></ul>
		</div>
	</div>

	<div class="aclContainer">
		<div id="aclActions-title" class="ui-widget-header ui-corner-top ui-state-default">
			<?=__('Actions')?>
		</div>
		<div id="aclActions" class="ui-widget-content ui-corner-bottom">
			<ul class="group-list"></ul>
		</div>
	</div>
</div>
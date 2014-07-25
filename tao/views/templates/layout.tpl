<? include(TAO_TPL_PATH . 'layout_header.tpl') ?>

	<div id="main-menu" class="ui-state-default" >
		<a href="<?=_url('index', 'Main', 'tao')?>" title="<?=__('TAO Home')?>"><span id="menu-bullet"></span></a>
		<div class="left-menu">
			<?$first = true;foreach(get_data('extensions') as $extension):?>
<?php if ($extension['enabled']): ?>
				<?if($first):$first = false;?><?else:?>|<?endif?>
				<span class="<? if (get_data('shownExtension') == $extension['extension']) echo 'current-extension' ?>">
					<a href="<?=_url('index', null, null, array('structure' => $extension['id'], 'ext' => $extension['extension']))?>" title="<?=__($extension['description'])?>"><?=__($extension['name'])?></a>
				</span>
<?php endif; ?>
			<?endforeach?>
		</div>

		<div class="right-menu">
            <div>
                <a class="icon" id="logout-icon" href="<?=_url('logout', 'Main', 'tao')?>" title="<?=__('Log Out')?>">
				</a>
			</div>
<?php if (tao_helpers_funcACL_funcACL::hasAccess('tao', 'UserSettings', null)): ?>
            <div class="vr">|</div>
            <div>
                <a class="icon" id="usersettings-icon" href="<?=_url('index', 'Main', 'tao', array('structure' => 'user_settings', 'ext' => 'tao'))?>" title="<?=__('My Settings')?>">
    			</a>
					   
					<p class="icon-desc">
                    <?=__('Logged in as:')?></br>
                    <strong><?=get_data('userLabel')?></strong>
                    </p>
			</div>
			<div class="vr">|</div>
			
<?php endif; ?>
<?php if (tao_helpers_funcACL_funcACL::hasAccess('filemanager', 'Browser', null)): ?>
            <div>
                <a class="icon file-manager" id="mediamanager-icon" href="#" title="<?=__('Media Management')?>">
    			</a>
			</div>
<?php endif; ?>
<?php if (tao_helpers_funcACL_funcACL::hasAccess('tao', 'Users', null)): ?>
            <div>
                <a class="icon" id="users-icon" href="<?=_url('index', 'Main', 'tao', array('structure' => 'users', 'ext' => 'tao'))?>" title="<?=__('User Management')?>">
				</a>
			</div>
				<?php endif; ?>
<?php if (tao_helpers_funcACL_funcACL::hasAccess('tao', 'Settings', null) && tao_helpers_SysAdmin::isSysAdmin()): ?>
            <div>
                <a class="icon" id="settings-icon" href="<?=_url('index', 'Main', 'tao', array('structure' => 'settings', 'ext' => 'tao'))?>" title="<?=__('System Settings')?>"></a>
			</div>
<?php endif; ?>
            <div class="breaker"></div>
		</div>
	</div>
<? if(get_data('sections')):?>

	<script type='text/javascript'>
		var shownExtension	= '<?=$shownExtension?>';
		var shownStructure = '<?=$shownStructure?>';
	</script>
	<div id="tabs">
		<ul>
		<?foreach(get_data('sections') as $section):?>
			<li><a id="<?=$section['id']?>" href="<?=ROOT_URL . substr($section['url'], 1) ?>" title="<?=$section['name']?>"><?=__($section['name'])?></a></li>
		<?endforeach?>
		</ul>

		<div id="sections-aside">
			<div id="section-trees"></div>
			<div id="section-actions"></div>
		</div>
		<div class="clearfix"></div>
		<div id="section-meta"></div>
	</div>

<?else:?>

	<?include('main/home.tpl');?>

<?endif?>

<? include 'layout_footer.tpl' ?>
	</body>
</html>
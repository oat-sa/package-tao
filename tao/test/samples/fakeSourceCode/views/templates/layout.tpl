<? include 'layout_header.tpl' ?>

  <div class="content-wrap">
	<div id="main-menu" class="ui-state-default" >
		<a href="<?=_url('index', 'main', 'tao')?>" title="<?=__('TAO Home')?>"><span id="menu-bullet"></span></a>
		<div class="left-menu tao-scope">
			<?foreach(get_data('extensions') as $i => $extension):?>
				<?if(get_data('currentExtension') == $extension['extension']):?>
					<span class="current-extension">
				<?else:?>
					<span>
				<?endif?>
						<a href="<?=_url('index', null, null, array('extension' => $extension['extension']))?>" title="<?=__($extension['description'])?>"><?=__($extension['name'])?></a>
					</span>
				<?if($i < (count(get_data('extensions')) - 1)):?>|<?endif?>
			<?endforeach?>
		</div>
		
		<div class="right-menu">
			<span>
				<a href="<?=_url('index', 'Main', 'index')?>" title="<?=__('Home')?>">
					<img src="<?=BASE_WWW?>img/home.png" alt="<?=__('Home')?>" />
				</a>
			</span>
		  	<span>
		  		<a href="<?=_url('index', null, null, array('structure' => 'users'))?>" title="<?=__('Users')?>">
		  			<img src="<?=BASE_WWW?>img/users.png" alt="<?=__('Users')?>" />
		  		</a>
		  	</span>
		  	<span>
		  		<a href="<?=_url('index', 'Settings')?>" class="settings-loader" title="<?=__('Settings')?>">
		  			<img src="<?=BASE_WWW?>img/settings.png" alt="<?=__('Settings')?>" />
		  		</a>
		  	</span>
			<span>
				<a href="#" class="file-manager" title="<?=__('Media manager')?>">
					<img src="<?=BASE_WWW?>img/mediamanager.png" alt="<?=__('Media manager')?>" />
				</a>
			</span>
		  	<span>
		  		<a href="<?=_url('logout')?>" title="<?=__('Logout')?>">
		  			<img src="<?=BASE_WWW?>img/logout.png" alt="<?=__('Logout')?>" />
		  		</a>
		  	</span>
		</div>
	</div>
	
<?if(get_data('sections')):?>

	<div id="tabs">
		<ul>
		<?foreach(get_data('sections') as $section):?>
			<li><a id="<?=(string)$section['id']?>" href="<?=ROOT_URL.(string)$section['url']?>" title="<?=(string)$section['name']?>"><?=__((string)$section['name'])?></a></li>
		<?endforeach?>
		</ul>
		
		<div id="section-trees"></div>
		<div id="section-actions" ></div>
		<div id="section-meta"></div>
	</div>

<?else:?>

	<?include('main/home.tpl');?> 

<?endif?>
</div>
<!-- /content-wrap -->
<? include 'layout_footer.tpl' ?>
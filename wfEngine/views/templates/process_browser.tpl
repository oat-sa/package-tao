<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=__("TAO - An Open and Versatile Computer-Based Assessment Platform")?></title>
                <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" media="screen" />
                <link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/process_browser.css" media="screen" />
		
                <script type="text/javascript" src="<?=TAOBASE_WWW?>js/lib/require.js" ></script>
                <script type="text/javascript">
                (function(){
                    require(['<?=get_data('client_config_url')?>'], function(){

                        require(['jquery', 'wfEngine/controller/processBrowser', 'serviceApi/ServiceApi', 'serviceApi/StateStorage', 'serviceApi/UserInfoService','wfEngine/wfApi/wfApi.min'], 
                            function($, ProcessBrowser, ServiceApi, StateStorage, UserInfoService){
                            
                            var services = [
                            <?foreach($services as $i => $service):?>
                            {
                               frameId  : 'tool-frame-<?=$i?>',
                               api      : <?=$service['api']?>, 
                               style    : <?=json_encode($service['style'])?>
                            } <?=($i < count($services) - 1) ? ',' : '' ?>
                            <?endforeach;?>  
                            ];
                            
                            ProcessBrowser.start({
                                activityExecutionUri    : '<?=get_data('activityExecutionUri')?>',
                                processUri              : '<?=get_data('processUri')?>',
                                activityExecutionNonce  : '<?=get_data('activityExecutionNonce')?>',
                                services                : services
                            });

                        });
                    });
                }());
                </script>
	</head>

	<body>
  <div class="content-wrap">
		<div id="runner">
		<div id="process_view"></div>
        <?if(!tao_helpers_Context::check('STANDALONE_MODE') && !has_data('allowControl') || get_data('allowControl')):?>
			<ul id="control">
	        	<li>
	        		<span id="connecteduser" class="icon"><?=__("User name:")?> <span id="username"><?=$userViewData['username']?></span></span>
	        		<span class="separator"></span>
	        	</li>
	
	
	         	<li>
	         		<a id="pause" class="action icon" href="<?=BASE_URL?>ProcessBrowser/pause?processUri=<?=urlencode($browserViewData['processUri'])?>"><?=__("Pause")?></a> <span class="separator"></span>
	         	</li>
	
	         	<?if(get_data('debugWidget')):?>
				<li>
					<a id="debug" class="action icon" href="#">Debug</a> <span class="separator"></span>
				</li>
	        	<?endif?>
	
	         	<li>
	         		<a id="logout" class="action icon" href="<?=_url('logout', 'Main')?>"><?=__("Logout")?></a>
	         	</li>
	
			</ul>
			
			<?if(get_data('debugWidget')):?>
					<div id="debugWindow" style="display:none;">
						<?foreach(get_data('debugData') as $debugSection => $debugObj):?>
						<fieldset>
							<legend><?=$debugSection?></legend>
							<pre>
								<?print_r($debugObj)?>
							</pre>
						</fieldset>
						<?endforeach?>
					</div>
			<?endif?>
		<?endif?>

		<div id="content">
			<div id="business">
				<div id="navigation">
					<?if($browserViewData['controls']['backward']):?>
						<input type="button" id="back" value="<?= __("Back")?>"/>
					<?else:?>
						<input type="button" id="back" value="" style="display:none;"/>
					<?endif?>

					<?if($browserViewData['controls']['forward']): ?>
						<input type="button" id="next" value="<?= __("Forward")?>"/>
					<?else:?>
						<input type="button" id="next" value="" style="display:none;"/>
					<?endif?>
				</div>

				<div id="tools">
                                    <?foreach($services as $i => $service):?>
                                    <iframe id="tool-frame-<?=$i?>" class="toolframe" frameborder="0" scrolling="no" ></iframe>
                                    <?endforeach;?>  
				</div>
			</div>
			<br class="clear" />
  		</div>
	</div>
    </div>
<!-- /content-wrap -->
<?php
if (!tao_helpers_Context::check('STANDALONE_MODE')) {
    Template::inc('layout_footer.tpl', 'tao');
}
?>
</body>
</html>
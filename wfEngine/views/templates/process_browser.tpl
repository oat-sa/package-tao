<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=__("TAO - An Open and Versatile Computer-Based Assessment Platform")?></title>

		<script type="text/javascript">
			var taobase_www = '<?=TAOBASE_WWW?>';
			var root_url = '<?=ROOT_URL?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/require-jquery.js"></script>
		<script src="<?=TAOBASE_WWW?>js/main.js"></script>
                <script src="<?=TAOBASE_WWW?>js/spin.min.js"></script>
		<script src="<?=TAOBASE_WWW?>js/serviceApi/ServiceApi.js"></script>
		<script src="<?=TAOBASE_WWW?>js/serviceApi/StateStorage.js"></script>
		
		<script type="text/javascript" src="<?=ROOT_URL?>wfEngine/views/js/wfApi/wfApi.min.js"></script>
		<script type="text/javascript" src="<?=ROOT_URL?>wfEngine/views/js/WfRunner.js"></script>
		<script type="text/javascript">
                    
                    require(['jquery', 'json2'], function($) {

                        $(document).ready(function(){
                            var $back = $('#back');
                            var $next = $("#next");
                        
                            var wfRunner = new WfRunner(
                                <?=json_encode(get_data('activityExecutionUri'))?>,
                                <?=json_encode(get_data('processUri'))?>,
                                <?=json_encode(get_data('activityExecutionNonce'))?>
                            );
                            <?foreach($services as $service):?>
                            wfRunner.initService(<?=$service['api']?>, <?=json_encode($service['style'])?>);
                            <?endforeach;?>
                            
                            $back.click(function(e){
                                
                                $back.off('click');
                                $next.off('click');
                                wfRunner.backward();
                                
                                e.preventDefault();
                            });
                            $("#next").click(function(e){
                            
                                $back.off('click');
                                $next.off('click');
                                wfRunner.forward();
                                
                                e.preventDefault();
                            });
                            
                            $("#debug").click(function(){
                                $("#debugWindow").toggle('slow');
                            });
                         });
                    });		
		</script>
		
		<style media="screen">
			@import url(<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css);
			@import url(<?=BASE_WWW?>css/process_browser.css);
		</style>

	</head>

	<body>
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
	         		<a id="logout" class="action icon" href="<?=BASE_URL?>DeliveryServerAuthentification/logout"><?=__("Logout")?></a>
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
					<?if(USE_PREVIOUS):?>
						<?if($browserViewData['controls']['backward']):?>
							<input type="button" id="back" value="<?= __("Back")?>"/>
						<?else:?>
							<input type="button" id="back" value="" style="display:none;"/>
						<?endif?>
					<?endif?>

					<?if($browserViewData['controls']['forward']): ?>
						<input type="button" id="next" value="<?= __("Forward")?>"/>
					<?else:?>
						<input type="button" id="next" value="" style="display:none;"/>
					<?endif?>
				</div>

				<div id="tools">
				</div>
			</div>
			<br class="clear" />
  		</div>
	</div>
	

<?php
    if (!tao_helpers_Context::check('STANDALONE_MODE')) {
        include TAO_TPL_PATH .'layout_footer.tpl';
    }
?>
</body>
</html>
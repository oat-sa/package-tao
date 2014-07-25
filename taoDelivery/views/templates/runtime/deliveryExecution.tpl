<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=tao_helpers_I18n::getLangCode()?>" lang="<?=tao_helpers_I18n::getLangCode()?>">
	<head>
		<title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
		<script type="text/javascript">
			var root_url = '<?=ROOT_URL?>';
			var base_url = '<?=BASE_URL?>';
			var taobase_www = '<?=TAOBASE_WWW?>';
			var base_www = '<?=BASE_WWW?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/jquery-1.8.0.min.js"></script>
		<script src="<?=TAOBASE_WWW?>js/json2.js"></script>
		<script src="<?=TAOBASE_WWW?>js/serviceApi/StateStorage.js"></script>
        <script src="<?=TAOBASE_WWW?>js/serviceApi/ServiceApi.js"></script>
        <script type="text/javascript">
        	$(function(){
        		$("#loader").css('display', 'none');
        		
        		var serviceApi = <?=get_data('serviceApi')?>;
        		var $frame = $('#iframeDeliveryExec');
        		var autoResizeId;
        		
        		var autoResize = function autoResize() {
					$frame = $('#iframeDeliveryExec');
					$frame.height($frame.contents().find('#runner').height());
				};
        		
        		if (jQuery.browser.msie) {
					$frame[0].onreadystatechange = function(){	
						if(this.readyState == 'complete'){
							autoResizeId = setInterval(autoResize, 10);
						}
					};
				} else {		
					$frame[0].onload = function(){
						autoResizeId = setInterval(autoResize, 10);
					};
				}
        		
        		serviceApi.onFinish(function() {
        			// Stop resizing iframe.
					clearInterval(autoResizeId);
        		
        			$.ajax({
        				url  		: <?= tao_helpers_Javascript::buildObject(_url('finishDeliveryExecution'))?>,
        				data 		: <?= tao_helpers_Javascript::buildObject(array('deliveryExecution' => get_data('deliveryExecution')))?>,
        				type 		: 'post',
        				dataType	: 'json',
        				success     : function(data) {
            				window.location = data.destination;
        				}
        			});
        		});
        	
        		serviceApi.loadInto($frame[0]);

	   });
        </script>
        <link rel="stylesheet" type="text/css" href="<?echo BASE_WWW; ?>css/main.css"/>
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
	</head>

	<body>
		<div id="process_view"></div>

		<?php if (get_data('showControls')) :?>
		<ul id="control">
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo get_data('userLabel'); ?></span> </span>
        		<span class="separator"></span>
        	</li>
         	<li>
         		<a class="action icon" id="logout" href="<?=_url('logout', 'DeliveryServerAuthentification')?>"><?php echo __("Logout"); ?></a>
         	</li>
		</ul>
        <?php endif; ?>
		<div id="content" class='ui-corner-bottom'>
                <div id="tools">
                    <iframe id="iframeDeliveryExec" class="toolframe" frameborder="0" style="width:100%;overflow:hidden;"></iframe>
				</div>
		</div>
		<!-- End of content -->
<? include TAO_TPL_PATH .'layout_footer.tpl';?>
</body>
</html>
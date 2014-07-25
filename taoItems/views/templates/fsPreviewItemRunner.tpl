<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=PRODUCT_NAME?> <?=TAO_VERSION?></title>
       
        <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/style.css" />
        <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
        <link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>taoItems/views/css/preview.css" />
        
        <script src="<?=TAOBASE_WWW?>js/jquery-1.8.0.min.js"></script>
        <script src="<?=TAOBASE_WWW?>js/jquery-ui-1.8.23.custom.min.js"></script>

        <?if(has_data('previewUrl')):?>
        <script type="text/javascript" src="<?=ROOT_URL.get_data('resultJsApiPath');?>"></script>
        <script type="text/javascript" src="<?=ROOT_URL?>tao/views/js/serviceApi/ServiceApi.js"></script>
        <script type="text/javascript" src="<?=ROOT_URL?>tao/views/js/serviceApi/PseudoStorage.js"></script>
        <script type="text/javascript" src="<?=ROOT_URL?>taoItems/views/js/runtime/ItemServiceImpl.js"></script>
        <script type="text/javascript" src="<?=ROOT_URL?>taoItems/views/js/preview-console.js"></script>
        <script type="text/javascript" src="<?=ROOT_URL?>taoItems/views/js/previewItemRunner.js"></script>
        <script type="text/javascript" src="<?=ROOT_URL.get_data('resultJsApiPath');?>"></script>

        <script type='text/javascript'>
            var resultApi = <?=get_data('resultJsApi');?>;
            var serviceApi = new ServiceApi(<?=tao_helpers_Javascript::buildObject(get_data('previewUrl'))?>, {}, 'preview', new PseudoStorage());
            var api = new ItemServiceImpl(serviceApi);
            $(function(){
                $('#preview-container').unbind('load').load(function() {
                    return function(api, frame) {
                        api.connect(frame);
                    }(api, this)}
                );
                $('#preview-container').attr('src', serviceApi.getCallUrl()); 
            });
        </script>

        <?endif?>

</head>
<body class='fullScreen'>
	
	<?if(has_data('previewUrl')):?>
		
            <iframe id='preview-container' name="preview-container"></iframe>
            <!-- to emulate wf navigaton: <button id='finishButton' ><?=__('Finish')?></button> -->

            <div id='preview-console' class="ui-widget">
                    <div class="console-control">
                            <span class="ui-icon ui-icon-circle-close" title="<?=__('close')?>"></span>
                            <span class="ui-icon ui-icon-circle-plus toggler" title="<?=__('show/hide')?>"></span>
                            <span class="ui-icon ui-icon-trash" title="<?=__('clean up')?>"></span>
                            <?=__('Preview Console')?> 
                    </div>
                    <div class="console-content"><ul></ul></div>
            </div>
       <?else:?>
            <h3><?=__('PREVIEW BOX')?></h3>
            <p><?=__("Not yet available")?></p>
	<?endif?>

</body>
</html>

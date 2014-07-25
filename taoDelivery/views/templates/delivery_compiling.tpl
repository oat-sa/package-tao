<?include('header.tpl')?>

<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/compiling.css" />

<div class="main-container">
    <div class="ext-home-container ui-state-highlight">
        <div>

            <h1>
                <img src="<?=BASE_WWW?>img/compile.png" />&nbsp;&nbsp;<?=__('Publishing "%s"', get_data('deliveryLabel'))?> <?=get_data('processLabel')?></h1>

            <div style="margin: 20px 10px;">
    		<?=__('To be accessible by test takers on the specified dates, you have to publish your delivery.')?>
    		<br />
    		<?=__('Be careful, later modifications on its content are not  taken into account. You have to publish it again to make your modifications available to test takers.')?>
    	</div>

    	<div style="margin: 30px 10px;">
    	   <div class="buttonSuperArea" id="initCompilation">
    	   <div class="buttonArea">
	        <a id="back" href="#" onclick="uiBootstrap.initTrees()" class="button">
	           <?=__('Cancel')?>
            </a>
	
            <a href="#" class="button"
                onclick="initCompilation('<?=get_data('uri')?>','<?=get_data('classUri')?>')">
    			<?if(get_data('isCompiled')):?>
    				<?=__('Publish again')?> 
    			<?else:?>
    				<?=__('Publish')?>
    			<?endif;?>
    		</a>
    		</div>
    		</div>
            </div>

            <script type="text/javascript"
                src="<?=BASE_WWW?>js/deliveryCompiling.js"></script>

            <div id="progressbar" style="margin: 20px 10px;"></div>

            <div id="testsContainer" style="margin: 20px 10px;"></div>

            <div id="generatingProcess" style="margin: 20px 10px;">
                <div id="generatingProcess_info"
                    style="margin-bottom: 10px;">
                    <img
                        style="position: relative; top: 10px; margin-right: 10px;"
                        src="<?=BASE_WWW?>img/process-ajax-loader.gif" /><?=__('Publishing is in progress, please wait...')?></div>
                <div id="generatingProcess_feedback"></div>
                
            </div>
        	<div id ="postCompilation" style="margin: 30px 10px;">
        	    <div class="buttonSuperArea">
            	    <div class="buttonArea">
                       <a id="back" href="#" onclick="uiBootstrap.initTrees()" class="button">
    	                   <?=__('Ok')?>
                       </a>
                    </div>
            </div>	   
        </div>

        <script type="text/javascript">
            $(document).ready(function(){
            	$('#generatingProcess_info').hide();
            });
        </script>
    </div>
</div>

<?include('footer.tpl')?>

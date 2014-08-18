<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		
		<?if(count(get_data('importErrors')) > 0):?>
			<fieldset class='ui-state-error'>
				<legend><strong><?=get_data('importErrorTitle')?></strong></legend>
				<ul id='error-details'>
				<?foreach(get_data('importErrors') as $ierror):?>
					<li><?=$ierror['message']?></li>
				<?endforeach?>
				</ul>
			</fieldset>
		<?endif?>
		
		<?=get_data('myForm')?>
	</div>
	
	<script type="text/javascript">
	$(function(){
		$('#commit_message').parent().hide();
		
		var $fileImport = $("#file_import"); 
		$fileImport.bind("async_file_uploaded", function(event, data){

			var fileName = data.name;
			var $fileNameField = $('input[id="http%3A%2F%2Fwww__tao__lu%2FOntologies%2Fgeneris__rdf%23FileName"]');
			
			// Catch the file upload and fill the file name field, if it is empty
			if($.trim($fileNameField.val()) == ''){
				//fill the file name field
				$fileNameField.val(fileName.replace(/[^A-Za-z_0-9\.]/ig, '_'));
			}
		});
		$fileImport.bind('async_file_selected', function(){
			$('#commit_message').parent().show('clip');
		});
		
		
		$('#delete-versioned-file').unbind('click').one('click', function(){
			if(confirm('<?=__('Are you sure to delete the versioned resource?\nThe history will be lost as well.')?>')){
				$(this).parent().siblings('input[name=file_delete]').val(1);
				$('a.form-submiter:first').click();
			}
			return false;
		});
	});
	</script>
</div>
<?php
Template::inc('footer.tpl', 'tao')
?>

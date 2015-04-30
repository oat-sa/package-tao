<?php
use oat\tao\helpers\Template;
?>
<div class="main-container flex-container-main-form">
	<h2><?=get_data('formTitle')?></div>
	<div class="form-content">
		
		<?php if(count(get_data('importErrors')) > 0):?>
			<fieldset class='ui-state-error'>
				<legend><strong><?=get_data('importErrorTitle')?></strong></legend>
				<ul id='error-details'>
				<?php foreach(get_data('importErrors') as $ierror):?>
					<li><?=$ierror['message']?></li>
				<?php endforeach?>
				</ul>
			</fieldset>
		<?php endif?>
		
		<?=get_data('myForm')?>
	</div>
</div>
<script>
require(['jquery'], function($){
    $('#commit_message').parent().hide();
    
    var $fileImport = $("#file_import"); 
    $fileImport.on("async_file_uploaded", function(event, data){

        var fileName = data.name;
        var $fileNameField = $('input[id="http%3A%2F%2Fwww__tao__lu%2FOntologies%2Fgeneris__rdf%23FileName"]');
        
        // Catch the file upload and fill the file name field, if it is empty
        if($.trim($fileNameField.val()) == ''){
            //fill the file name field
            $fileNameField.val(fileName.replace(/[^A-Za-z_0-9\.]/ig, '_'));
        }
    });
    $fileImport.on('async_file_selected', function(){
        $('#commit_message').parent().show('clip');
    });
    
    
    $('#delete-versioned-file').off('click').one('click', function(){
        if(confirm('<?=__('Are you sure to delete the versioned resource?\nThe history will be lost as well.')?>')){
            $(this).parent().siblings('input[name=file_delete]').val(1);
            $('a.form-submitter:first').click();
        }
        return false;
    });
});
</script>
<?php
Template::inc('footer.tpl', 'tao')
?>

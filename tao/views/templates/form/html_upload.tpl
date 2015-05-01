<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=__('File Uploader')?></title>
	<link rel="shortcut icon" href="<?= Template::img('favicon.ico')?>" type="image/x-icon" />

	<script type='text/javascript' src="<?= Template::js('lib/jquery-1.8.0.min.js')?>"></script>

	<?php if(get_data('uploaded') === true):?>

	<script type="text/javascript">
		$(document).ready(function(){
			<?php if(get_data('setLinear')):?>
			var fileData = '<?=get_data('uploadData')?>';
			<?php else:?>
			var fileData = '<?=get_data('uploadFilePath')?>';
			<?php endif?>

			$("<?=get_data('target')?>", window.opener.document).val(fileData);

			var desc = "<?=__('Selected file:')?> <?=get_data('uploadFile')?>";
			var descElt = $("<?=get_data('target')?>", window.opener.document).parent().find('div#html-upload-desc');
			if(descElt.length > 0){
				descElt.text(desc);
			}
			else{
				$("<?=get_data('target')?>", window.opener.document).before('<div id="html-upload-desc">'+desc+'</div>');
			}
			window.close();
		});
	</script>
	<?php endif?>
</head>
<body>
	<form method='post' enctype='multipart/form-data'>
		<input type='hidden' name='upload_sent' value="1" />
		<input type='hidden' name='MAX_FILE_SIZE' value='<?=get_data('sizeLimit')?>' />

		<input type="file" name="Filedata" accept="<?=get_data('accept')?>" /><br />
		<input type="submit" value="<?=__('Upload')?>" />
	</form>
</body>
</html>
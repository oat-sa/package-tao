<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?=__('Restricted session');?></title>
</head>

<body>
	<div id="main" class=tao-scope ui-widget-content ui-corner-all">
		<h2><?=__('You are currently restricted to the following roles');?></h2>
		<ul>
		<?php foreach (get_data('roles') as $role):?>
		  <li><?=$role?></li>
		<?php endforeach;?>
		</ul>
		<div class='message'>
            <a href="<?=_url('restore')?>" class="btn-info" type="button""> 
    			<?=__('Restore');?>
			</a>
		</div>
	</div>
</body>

</html>
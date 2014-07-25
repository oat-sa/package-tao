<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?=__('Configured LTI Link');?></title>

    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/tao-main-style.css">	
</head>

<body class="tao-scope">
    <div style="padding: 0 0 10px 0">
    	<?= has_data('linkTitle')
    	   ? __('"%s" has been configured:', get_data('linkTitle'))
    	   : __('This tool has been configured:')?>
    </div>

    <div class="grid-row">
        <div class="col-3">
            <table class="matrix">
                <tbody>
                <tr><th><?=__('Selected Delivery');?></th><td><?= get_data('delivery')->getLabel()?></td></tr>
                <?php if(has_data('executionCount')) :?>
                <tr><th><?=__('Executions');?></th><td><?= get_data('executionCount')?></td></tr>
                <?php else :?>
                <tr><th><?=__('Executions');?></th><td><?= __('no monitoring')?></td></tr>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
        
</body>

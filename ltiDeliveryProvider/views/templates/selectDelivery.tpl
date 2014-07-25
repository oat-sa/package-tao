<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?=__('Thank you');?></title>
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/jquery-1.8.0.min.js "></script>
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/jquery-ui-1.8.23.custom.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/custom-theme/jquery-ui-1.8.22.custom.css" />
    <link rel="stylesheet" type="text/css" href="<?= BASE_WWW ?>css/select.css" />	
</head>

<body>
    <div>
    	<?= has_data('linkTitle')
    	   ? __('Please select a delivery for %s', get_data('linkTitle'))
    	   : __('Please select a delivery')?>
    </div>
    <div class="section_title">
        <?=__('Available Deliveries');?>
    </div>
    <div>
    <form action="<?=get_data('submitUrl')?>">
        <input type="hidden" name="link" value="<?=get_data('link')?>" />
    <?php
        $count = 0;
        foreach (get_data('deliveries') as $delivery) :
        $count++;
    ?>
        <input id="radio_<?=$count;?>" type="radio" name="uri" value="<?=$delivery->getUri();?>" /><label for="radio_<?=$count;?>"><?=$delivery->getLabel();?></label>
    <?php endforeach;?>
        <br />
        <input type="submit" value="<?=__('Select');?>" />
    </form>
    </div>
    

</body>
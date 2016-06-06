<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?=__('Select Delivery');?></title>
<link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/tao-main-style.css">
</head>

<body class="tao-scope">
    <div style="padding: 20px">
        <div style="padding: 0 0 10px 0">
        	<?=has_data('linkTitle') ? __('Please select a delivery for "%s"', get_data('linkTitle')) : __('Please select a delivery')?>
        </div>
        <h2>
            <?=__('Available Deliveries');?>
        </h2>
        <form action="<?=get_data('submitUrl')?>">
            <input type="hidden" name="link" value="<?=get_data('link')?>" />
            <ul class="none">
            <?php foreach (get_data('deliveries') as $delivery) : ?>
              <li><label>
                <input name="uri" type="radio" value="<?=$delivery->getUri();?>">
                <span class="icon-radio"></span><?=$delivery->getLabel();?>
              </label></li>  
            <?php endforeach;?>
            </ul>
            <input type="submit" value="<?=__('Select');?>" />
        </form>
    </div>
</body>
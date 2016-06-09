<?php
use oat\tao\helpers\Template;
?>

<div class="main-container flex-container-main-form">
    <h2><?=get_data('formTitle')?></h2>
    <div class="form-container">
        <?=get_data('myForm')?>
    </div>
</div>

<script>
    requirejs.config({
        config : {
            'tao/controller/users/add' : {
                loginId : <?=json_encode(get_data('loginUri'))?>,
                exit    : <?=json_encode(get_data('exit'))?>
            }
        } 
    });		
</script>



<?php Template::inc('footer.tpl'); ?>

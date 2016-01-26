<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>


<header class="section-header flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
</header>

<div class="main-container flex-container-main-form">
    <div class="form-content">
        <?=get_data('myForm')?>
    </div>
</div>

<div class="data-container-wrapper flex-container-remainder">
    <?=get_data('memberForm')?>

    <?=get_data('deliveryForm')?>
</div>

<?php
Template::inc('footer.tpl', 'tao');
?>

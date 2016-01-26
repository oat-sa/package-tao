<?php
use oat\tao\helpers\Template;
?>
    <header class="section-header flex-container-full">
        <h2><?=get_data('formTitle')?></h2>
    </header>
    <div class="main-container flex-container-main-form">
        <div class="form-content">
            <?=get_data('myForm')?>
        </div>
    </div>

<?php Template::inc('footer.tpl', 'tao') ?>

<?php
use oat\tao\helpers\Template;
Template::inc('form_context.tpl', 'tao');
?>
<link rel="stylesheet" href="<?= Template::css('media.css') ?>"/>
    <div class="main-container flex-container-main-form">
        <header class="section-header flex-container-full">
            <h2><?=get_data('formTitle')?></h2>
        </header>
        <div class="form-content">
            <?=get_data('myForm')?>
        </div>
    </div>
    <div class="data-container-wrapper flex-container-remaining">
        <header class="section-header flex-container-full">
            <h2><?=__('Preview')?></h2>
        </header>
        <?php if(has_data('fileurl')):?>
        <div class="previewer" data-url="<?=get_data('fileurl')?>" data-type="<?=get_data('mimeType')?>" data-xml="<?=get_data('xml')?>"></div>
        <?php endif;?>
        <?php if(has_data('data')):?>
        <pre><?=get_data('data')?></pre>
        <?php endif;?>
        <?php if(has_data('error')):?>
            <div class="feedback-error small"><span class="icon-error small"></span><?=get_data('error')?></div>
        <?php endif;?>
    </div>

<?php Template::inc('footer.tpl', 'tao'); ?>

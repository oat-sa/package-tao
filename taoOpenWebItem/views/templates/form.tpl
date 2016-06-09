<?php
use oat\tao\helpers\Template;
Template::inc('form_context.tpl', 'tao')
?>
<header class="section-header flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
</header>
<div class="main-container flex-container-main-form">
    <div class="form-content">

        <?php if(get_data('hasContent')):?>
            <div class="feedback-info">
                <span class="icon-info"></span><?= __('This Open Web Item already has content. Go to Preview to see it or import a replacement below.')?>
            </div>
        <?php endif?>
        <?=get_data('myForm')?>
        <?php if(has_data('report')):?>
            <?php echo tao_helpers_report_Rendering::render(get_data('report')); ?>
        <?php endif?>
    </div>
</div>
<div class="data-container-wrapper flex-container-remaining"></div>

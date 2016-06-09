<?php
use oat\tao\helpers\Template;
?>

<header class="section-header flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
</header>
<div class="main-container flex-container-main-form">
    <?php if(has_data('lockDate')) : ?>
        <div id="lock-box"
            data-id="<?= get_data('id') ?>"
            data-msg="<?= __('You checked out this item %2s ago', tao_helpers_Date::displayInterval(get_data('lockDate'), tao_helpers_Date::FORMAT_INTERVAL_SHORT)) ?>"></div>
    <?php endif; ?>
    <div class="form-content">
        <?=get_data('myForm')?>
    </div>
</div>

<script>
requirejs.config({
    config: {
        'taoItems/controller/items/edit': {
            'isPreviewEnabled' : <?= json_encode(get_data('isPreviewEnabled')) ?>,
            'isAuthoringEnabled' : <?= json_encode(get_data('isAuthoringEnabled')) ?>
        }
    }
});
</script>

<?php Template::inc('footer.tpl', 'tao'); ?>

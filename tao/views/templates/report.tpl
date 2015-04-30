<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?= Template::css('report.css','tao') ?>" media="screen"/>
<div class="section-header flex-container-full">
    <h2>
    <?= get_data('report')->getType() == common_report_Report::TYPE_ERROR
            ? __('Error')
            : __('Success'); ?>
    </h2>
</div>
<div class="main-container flex-container-full report">
    <?php if (get_data('report')->hasChildren() === true): ?>
    <label id="fold">
        <span class="check-txt"><?php echo __("Show detailed report"); ?></span>
        <input type="checkbox"/>
        <span class="icon-checkbox"></span>
    </label>
    <?php endif; ?>
    <?php echo tao_helpers_report_Rendering::render(get_data('report')); ?>
</div>
<script type="text/javascript">
require(['jquery', 'tao/report'], function($, report){

    // Fold action (show detailed report).
    $('#fold > input[type="checkbox"]').click(function() {
        report.fold();
    });
    
    // Continue button
    $('#import-continue').on('click', function() {
        $('.tree').trigger('refresh.taotree', [{
            uri : <?php echo json_encode(get_data('selectNode')); ?>
        }]);
    });
});
</script>

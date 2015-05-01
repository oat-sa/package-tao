<?php
use oat\tao\helpers\Template;
?>
<script>
<?php if(has_data('errorMessage') || has_data('message')): ?>
    require(['ui/feedback'], function(feedback){
        <?php if(has_data('errorMessage')): ?>
        feedback().error(<?= json_encode(get_data('errorMessage')) ?>);
        <?php endif; ?>

        <?php if(has_data('message')): ?>
        feedback().info(<?= json_encode(get_data('message')) ?>);
        <?php endif; ?>
    });
<?php endif; ?>
<?php if(get_data('reload')): ?>
    require(['jquery'], function ($) {
        $('.tree').trigger('refresh.taotree', [{
            selectNode : <?=json_encode(get_data('selectNode'))?>
        }]);
    });
<?php endif;?>
</script>

<?php
use oat\tao\helpers\Template;
Template::inc('Lock/locked.tpl', 'tao');
?>
<script>
requirejs.config({
    config: {
        'taoItems/controller/items/edit': {
            'isPreviewEnabled' : <?= json_encode(true) ?>,
            'isAuthoringEnabled' : <?= json_encode(false) ?>
        }
    }
});
</script>
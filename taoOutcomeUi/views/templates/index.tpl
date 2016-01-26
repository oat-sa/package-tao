<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
    <div class="feedback-<?= get_data('type')?>">
        <?= get_data("error") ?>
    </div>
</div>
<?php
Template::inc('footer.tpl', 'tao');
?>
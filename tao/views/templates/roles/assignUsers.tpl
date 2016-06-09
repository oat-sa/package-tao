<?php
use oat\tao\helpers\Template;
?>
    <header class="section-header flex-container-full">
        <h2><?=get_data('formTitle')?></h2>
    </header>
    <div class="data-container-wrapper flex-container-remaining">
        <?=get_data('userTree')?>
    </div>

<?php Template::inc('footer.tpl', 'tao') ?>;

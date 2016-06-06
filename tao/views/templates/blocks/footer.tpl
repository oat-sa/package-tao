<?php
use oat\tao\helpers\Layout;
?>
<footer class="dark-bar">
    © 2013 - <?= date('Y') ?> · <span class="tao-version"><?= TAO_VERSION_NAME ?></span> ·
    <a href="http://taotesting.com" target="_blank">Open Assessment Technologies S.A.</a>
    · <?= __('All rights reserved.') ?>
    <?php $releaseMsgData = Layout::getReleaseMsgData();
    if ($releaseMsgData['msg'] && ($releaseMsgData['is-unstable'] || $releaseMsgData['is-sandbox'])): ?>
        <span class="rgt">
            <?php if ($releaseMsgData['is-unstable']): ?>
                <span class="icon-warning"></span>
            <?php endif; ?>
            <?=$releaseMsgData['version-type']?> ·
        <a href="<?=$releaseMsgData['link']?>" target="_blank"><?=$releaseMsgData['msg']?></a></span>
    <?php endif; ?>
</footer>
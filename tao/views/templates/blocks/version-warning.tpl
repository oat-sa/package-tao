<?php
use oat\tao\helpers\Layout;

$releaseMsgData = Layout::getReleaseMsgData();
?>
<?php if(!!$releaseMsgData['msg']): ?>
    <div class="feedback-warning small version-warning">
        <span class="icon-warning"></span>
        <?= $releaseMsgData['version-type'] ?> ·
        <?php if ($releaseMsgData['is-unstable']): ?>
            <a href="<?= $releaseMsgData['link'] ?>" target="_blank">
                <?= $releaseMsgData['msg'] ?>
            </a>
        <?php else: ?>
            <?= __('All data will be removed in %s', Layout::getSandboxExpiration()) ?>
        <?php endif; ?>
        <span title="<?= __('Remove Message') ?>" class="icon-close close-trigger"></span>
    </div>
<?php endif; ?>
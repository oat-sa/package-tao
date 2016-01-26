<?php
use oat\tao\helpers\Layout;
?>

    <?php foreach (get_data('actions') as $action): ?>
    <li class="action <?= get_data('action_classes')?>"
        id="<?=$action->getId()?>"
        title="<?= __($action->getName()) ?>"
        data-context="<?= $action->getContext() ?>"
        data-action="<?= $action->getBinding() ?>">
        <a class="li-inner" href="<?= $action->getUrl(); ?>">
            <?= Layout::renderIcon( $action->getIcon(), ' icon-magicwand'); ?> <?= __($action->getName()) ?>
        </a>
    </li>
    <?php endforeach; ?>

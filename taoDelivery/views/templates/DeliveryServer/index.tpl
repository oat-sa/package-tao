<?php
use oat\tao\helpers\Template;
use oat\taoDelivery\helper\Delivery;

$resumableDeliveries = get_data('resumableDeliveries');
$availableDeliveries = get_data('availableDeliveries');
?>
<div class="test-listing">
    <h1><?= __("My Tests"); ?></h1>
    <?php if (count($resumableDeliveries) > 0) : ?>
        <h2 class="info">
            <?= __("In progress") ?>: <?= count($resumableDeliveries); ?>
        </h2>

        <ul class="entry-point-box plain">
            <?php foreach ($resumableDeliveries as $delivery): ?>
                <li>
                    <a class="block entry-point entry-point-started-deliveries" href="<?= $delivery[Delivery::LAUNCH_URL] ?>">
                        <h3><?= _dh($delivery[Delivery::LABEL]) ?></h3>

                        <?php foreach ($delivery[Delivery::DESCRIPTION] as $desc) : ?>
                        <p><?= $desc?></p>
                        <?php endforeach; ?>

                        <div class="clearfix">
                            <span class="text-link" href="<?= $delivery[Delivery::LAUNCH_URL] ?>"><span class="icon-continue"></span> <?= __("Resume") ?> </span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (count($availableDeliveries) > 0) : ?>
        <h2>
            <?= __("Available") ?>: <?= count($availableDeliveries); ?>
        </h2>
        <ul class="entry-point-box plain">
            <?php foreach ($availableDeliveries as $delivery) : ?>
                <?php Template::inc('DeliveryServer/delivery_entry.tpl', null, ['delivery' => $delivery]); ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php
use oat\taoDelivery\helper\Delivery;
$delivery = get_data('delivery');
?>
<li>
    <a class="block entry-point entry-point-all-deliveries <?= ($delivery["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled" ?>"
        href="<?= ($delivery["TAO_DELIVERY_TAKABLE"]) ? $delivery[Delivery::LAUNCH_URL] : "#" ?>">
        <h3><?= _dh($delivery[Delivery::LABEL]) ?></h3>

        <?php foreach ($delivery[Delivery::DESCRIPTION] as $desc) : ?>
        <p><?= $desc?></p>
        <?php endforeach; ?>
        <div class="clearfix">
            <span class="text-link"><span class="icon-play"></span> <?= __('Start') ?> </span>
        </div>
    </a>
</li>

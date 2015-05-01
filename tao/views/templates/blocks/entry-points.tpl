<ul id="entry-point-box" class="plain">
    <?php foreach (get_data('entries') as $entry) : ?>
        <li>
            <a class="block entry-point entry-point-<?= $entry->getId() ?>" href="<?= $entry->getUrl() ?>">
                <h1><?= $entry->getTitle() ?></h1>

                <p><?= $entry->getDescription() ?></p>

                <div class="clearfix">

                    <span class="text-link" href="<?= $entry->getUrl() ?>"><span class="icon-login"></span> <?= __('Enter') ?> <?= $entry->getLabel() ?> </span>
                </div>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

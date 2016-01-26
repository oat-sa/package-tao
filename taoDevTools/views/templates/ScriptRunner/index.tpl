<div>
    <?php foreach (get_data('actions') as $id => $label) : ?>
        <button class="script" data-action="<?= $id ?>"><?= $label ?></button>
    <?php endforeach; ?>
</div>

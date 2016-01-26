<div class="grid-row">
    <div class="col-8"><pre><?= get_data('jsonPackage') ?></pre></div>
    <div class="col-4">
        <ul>
            <?php foreach (get_data('assets') as $asset) : ?>
                <li><a href="<?= _url('getAsset', null, null, array('id' => get_data('id'), 'asset' => $asset)) ?>" target="_blank"><?= $asset ?></a></li>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<script>
</script>
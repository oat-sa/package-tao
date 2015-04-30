<?php

use oat\tao\helpers\Template;
$tutLink = '<a href="http://style.taotesting.com/icon-listing/" target="_blank">Tao Style Guide</a>';
$jsonLink = sprintf('<a href="%s" target="dwl">selection.json</a>', _url('downloadCurrentSelection','FontConversion'));
$gitLink = '<a href="https://github.com/oat-sa/extension-tao-devtools" target="_blank">GitHub repository</a>';
$icomoonLink = '<a href="https://icomoon.io/app/#/select" target="_blank">icomoon</a>';

?>
<link rel="stylesheet" href="<?= Template::css('devtools.css') ?>" />

<div class="main-container flex-container-main-form">
    <h2><?=__('Update Tao Icon Font')?></h2>
    <p><?= __('Before you start working with the TAO icon font make sure that this extension is in sync with the %s.', $gitLink)?></p>
    <p><?= __('If you need instructions please follow the tutorial in the %s.', $tutLink) ?></p>
    <p><?= __('You will need a copy of %s to get started.', $jsonLink) ?><iframe name="dwl" class="viewport-hidden"></iframe></p>
    <div class="form-content">
        <div class="xhtml_form">
            <p><?= __('Upload the zip file created on %s below:', $icomoonLink)?></p>
            <div id="upload-container" data-url="<?=_url('processFontArchive','FontConversion');?>"></div>
        </div>
    </div>
</div>
<div class="data-container-wrapper flex-container-remaining">
    <h2><?=__('Existing Icons')?></h2>

    <ul class="plain complete-icon-listing clearfix">
        <?php foreach(get_data('icon-listing') as $icon): ?>
            <?php $name = $icon -> properties -> name ?>
            <li title="CSS class: icon-<?= $icon -> properties -> name ?>">
                <div class="icon icon-<?= $icon -> properties -> name ?>"></div>
                <div class="truncate"><?= $icon -> properties -> name ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


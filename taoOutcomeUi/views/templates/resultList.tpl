<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" href="<?= Template::css('icon.css') ?>" />
<div id="inspect-result" class="flex-container-full"
    data-model="<?= tao_helpers_Display::encodeAttrValue(json_encode(get_data("model"))) ?>"
    data-uri="<?= tao_helpers_Display::encodeAttrValue(get_data("uri")) ?>"
>

	<div class="grid-row">
    	<div class="col-12">
    		<div class="inspect-results-grid"></div>
    	</div>
	</div>
</div>

<div class="preview-modal-feedback modal">
    <div class="modal-body clearfix">
        <p><?= __('Please confirm deletion') ?></p>

        <div class="rgt">
            <button class="btn-regular small cancel" type="button"><?= __('Cancel') ?></button>
            <button class="btn-info small save" type="button"><?= __('Ok') ?></button>
        </div>
    </div>
</div>
<?php
Template::inc('footer.tpl', 'tao');
?>

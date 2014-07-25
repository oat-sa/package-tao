<div id="formObject_content_<?=get_data('objectSerial')?>" class="ui-widget-content ui-corner-bottom">
    <div class="ext-home-container qti-form-container">
        <div id="formObject_errors" class="form-error ui-state-error ui-corner-all" style="display: none;"><?=get_data('errorMessage')?></div>
        <?=get_data('form')?>
    </div>
    <div>
        <div id="formInteraction_preview_container_title"><?=__('Media Preview')?>&nbsp;:<span class="qti-img-preview-label"></span></div>
        <div id="formInteraction_preview_container">
            <div id="formInteraction_object_preview">
                <?=__('No media selected')?>
            </div>
        </div>
    </div>
</div>
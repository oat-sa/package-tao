<div id="formObject_content_<?=get_data('objectSerial')?>" class="ui-widget-content ui-corner-bottom">
    <div class="ext-home-container qti-form-container">
        <div id="formObject_errors" class="form-error ui-state-error ui-corner-all" style="display: none;"><?=get_data('errorMessage')?></div>
        <?=get_data('form')?>
    </div>
    <div>
        <div id="formInteraction_preview_container_title"><?=__('Math Preview')?>&nbsp;:<span class="qti-img-preview-label"></span></div>
        <div id="formInteraction_preview_container">
            <div id="texReviewBox" style="visibility:hidden">
                <div id="mathOutput" class="output">$${}$$</div>
            </div>
            <div id="mathReviewBox" style="display:none"></div>
            <div id="mathReviewBuffer" style="visibility:hidden"></div>
        </div>
    </div>
</div>
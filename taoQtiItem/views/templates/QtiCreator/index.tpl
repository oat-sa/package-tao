<?php
use oat\tao\helpers\Template;
?>

<link rel="stylesheet" href="<?= Template::css('qti.css') ?>" />
<link rel="stylesheet" href="<?= Template::css('item-creator.css') ?>" />
<link rel="stylesheet" href="<?= Template::css('preview.css','taoItems') ?>" />


<div id="item-editor-scope" data-content-target="wide">

<div class="wrapper clearfix content sidebar-popup-parent" id="item-editor-wrapper">


<!-- left sidebar -->
<div class="item-editor-sidebar-wrapper left-bar">
    <div class="action-bar plain content-action-bar horizontal-action-bar">
        <ul class="action-group plain clearfix authoring-back-box item-editor-menu">
            <li id="authoringBack" class="btn-info small">
            <span class="li-inner">
                <span class="icon-left"></span>
                <?= __('Manage Items') ?>
            </span>
            </li>
        </ul>
    </div>
    <form class="item-editor-sidebar" id="item-editor-interaction-bar" autocomplete="off"></form>
</div>
<!-- /left sidebar -->
<!-- item panel -->
<main id="item-editor-panel" class="clearfix">
    <div class="item-editor-action-bar action-bar plain content-action-bar horizontal-action-bar">
        <ul class="plain item-editor-menu action-group">

            <li id="save-trigger" class="btn-info small">
            <span class="li-inner">
                <span class="icon-save"></span>
                <?= __('Save') ?>
            </span>
            </li>
            <li class="preview-trigger btn-info small">
            <span class="li-inner">
                <span class="icon-preview"></span>
                <?= __('Preview') ?>
            </span>
            </li>
            <li id="print-trigger" class="btn-info small">
            <span class="li-inner">
                <span class="icon-print"></span>
                <?= __('Print') ?>
            </span>
            </li>
        </ul>
        <div id="item-editor-label" class="truncate action-group"></div>
    </div>
    <div id="item-editor-scroll-outer">
        <div id="item-editor-scroll-inner">
            <!-- item goes here -->
        </div>
    </div>
</main>
<!-- /item panel -->
<!-- right sidebar -->
<div class="item-editor-sidebar-wrapper right-bar sidebar-popup-parent">
<div class="action-bar content-action-bar horizontal-action-bar">
    <ul class="action-group plain clearfix item-editor-menu plain">
        <li id="appearance-trigger" class="btn-info small rgt">
                    <span class="li-inner">
                        <span class="icon-item"></span>
                        <span class="icon-style"></span>
                        <span class="menu-label" data-item="<?= __('Item properties') ?>" data-style="<?= __('Style Editor') ?>"><?= __('Style Editor') ?></span>
                    </span>
        </li>
    </ul>
</div>
<div class="item-editor-sidebar" id="item-editor-item-widget-bar">

<div class="item-editor-item-related sidebar-right-section-box" id="item-style-editor-bar">
    <section class="tool-group clearfix" id="sidebar-right-css-manager">

        <h2><?= __('Style Sheet Manager') ?></h2>

        <div class="panel">

            <ul class="none" id="style-sheet-toggler">
                <!-- TAO style sheet -->
                <li data-css-res="taoQtiItem/views/css/qti.css">
                        <span class="icon-preview style-sheet-toggler"
                              title="<?= __('Disable this stylesheet temporarily') ?>"></span>
                    <span><?= __('TAO default styles') ?></span>
                </li>

            </ul>
            <button id="stylesheet-uploader" class="btn-info small block"><?= __('Add Style Sheet') ?></button>
        </div>
    </section>

    <section class="tool-group clearfix" id="sidebar-right-style-editor">

        <h2><?= __('Style Editor') ?></h2>

        <div class="panel color-picker-panel">
            <div class="item-editor-color-picker sidebar-popup-container-box">
                <div class="color-picker-container sidebar-popup">
                    <div class="sidebar-popup-title">
                        <h3 id="color-picker-title"></h3>
                        <a class="closer" href="#" data-close="#color-picker-container"></a>
                    </div>
                    <div class="sidebar-popup-content">
                        <div class="color-picker"></div>
                        <input id="color-picker-input" type="text" value="#000000">
                    </div>
                </div>
                <div class="reset-group">
                    <div class="clearfix">
                        <label for="initial-bg" class="truncate"><?= __('Background color') ?></label>
                            <span class="icon-eraser reset-button" data-value="background-color"
                                  title="<?= __('Remove custom background color') ?>"></span>
                            <span class="color-trigger" id="initial-bg" data-value="background-color"
                                  data-target="body div.qti-item, body div.qti-item .qti-associateInteraction .result-area > li > .target"></span>
                    </div>
                    <div class="clearfix">
                        <label for="initial-color" class="truncate"><?= __('Text color') ?></label>
                            <span class="icon-eraser reset-button" data-value="color"
                                  title="<?= __('Remove custom text color') ?>"></span>
                            <span class="color-trigger" id="initial-color" data-value="color"
                                  data-target="body div.qti-item"></span>
                    </div>
                    <div class="clearfix">
                        <label for="initial-color" class="truncate"><?= __('Border color') ?></label>
                            <span class="icon-eraser reset-button" data-value="color"
                                  title="<?= __('Remove custom border color') ?>"></span>
                            <span class="color-trigger" id="initial-color" data-value="border-color"
                                  data-target="body div.qti-item .solid,body div.qti-item .matrix, body div.qti-item table.matrix th, body div.qti-item table.matrix td"></span>
                    </div>
                    <div class="clearfix">
                        <label for="initial-color" class="truncate"><?= __('Table headings') ?></label>
                            <span class="icon-eraser reset-button" data-value="color"
                                  title="<?= __('Remove custom background color') ?>"></span>
                            <span class="color-trigger" id="initial-color" data-value="background-color"
                                  data-target="body div.qti-item .matrix th"></span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="panel">

            <div><?= __('Font family') ?></div>

            <div class="reset-group">
                <select
                    data-target="body div.qti-item"
                    id="item-editor-font-selector"
                    data-has-search="false"
                    data-placeholder="<?= __('Default') ?>"
                    class="select2 has-icon"
                    data-role="font-selector"></select>
                    <span class="icon-eraser reset-button" data-role="font-selector-reset"
                          title="<?= __('Remove custom font family') ?>"></span>
            </div>

        </div>
        <div class="panel">
            <div><?= __('Font size') ?></div>
            <div class="reset-group">
                            <span id="item-editor-font-size-changer" data-target="body div.qti-item">
                                <a href="#" data-action="reduce" title="<?= __('Reduce font size') ?>"
                                   class="icon-smaller"></a>
                                <a href="#" data-action="enlarge" title="<?= __('Enlarge font size') ?>"
                                   class="icon-larger"></a>
                            </span>

                            <span id="item-editor-font-size-manual-input" class="item-editor-unit-input-box">
                                <input type="text" id="item-editor-font-size-text" class="has-icon"
                                       placeholder="<?= __('e.g. 13') ?>">
                                    <span class="unit-indicator">px</span>
                            </span>
                    <span class="icon-eraser reset-button" data-role="font-size-reset"
                          title="<?= __('Remove custom font size') ?>"></span>
            </div>

        </div>
        <hr>
        <div class="panel">
            <h3><?= __('Item width') ?></h3>
                <span class="icon-help tooltipstered" data-tooltip-theme="info"
                      data-tooltip="~ .tooltip-content:first"></span>

            <div class="tooltip-content">
                <?= __(
                    'Change the width of the item. By default the item has a width of 100% and adapts to the size of any screen. The maximal width is by default 1024px - this will also change when you set a custom with.'
                ) ?>
            </div>
            <div id="item-editor-item-resizer" data-target="body div.qti-item">
                <label class="smaller-prompt">
                    <input type="radio" name="item-width-prompt" checked value="no-slider">
                    <span class="icon-radio"></span>
                    <?= __('Adapt to screen size') ?>
                </label>
                <label class="smaller-prompt">
                    <input type="radio" name="item-width-prompt" value="slider">
                    <span class="icon-radio"></span>
                    <?= __('Defined width') ?>
                </label>

                <div class="reset-group slider-box">
                    <p id="item-editor-item-resizer-slider"></p>
                                <span id="item-editor-item-resizer-manual-input" class="item-editor-unit-input-box">
                                    <input type="text" id="item-editor-item-resizer-text" class="has-icon"
                                           placeholder="<?= __('e.g. 960') ?>">
                                    <span class="unit-indicator">px</span>
                                </span>
                        <span class="icon-eraser reset-button" data-role="item-width-reset"
                              title="<?= __('Remove custom item width') ?>"></span>
                </div>
            </div>

        </div>

    </section>

</div>
<div class="item-editor-item-related sidebar-right-section-box" id="item-editor-item-property-bar">
    <section class="tool-group clearfix" id="sidebar-right-item-properties">
        <h2><?= __('Item Properties') ?></h2>

        <div class="panel"></div>
    </section>
</div>
<div class="item-editor-item-related sidebar-right-section-box" id="item-editor-text-property-bar">
    <section class="tool-group clearfix" id="sidebar-right-text-block-properties">
        <h2><?= __('Text Block Properties') ?></h2>

        <div class="panel"></div>
    </section>
</div>
<div class="item-editor-interaction-related sidebar-right-section-box" id="item-editor-interaction-property-bar">
    <section class="tool-group clearfix" id="sidebar-right-interaction-properties">
        <h2><?= __('Interaction Properties') ?></h2>

        <div class="panel"></div>
    </section>
</div>
<div class="item-editor-choice-related sidebar-right-section-box" id="item-editor-choice-property-bar">
    <section class="tool-group clearfix" id="sidebar-right-choice-properties">
        <h2><?= __('Choice Properties') ?></h2>

        <div class="panel"></div>
    </section>
</div>
<div class="item-editor-response-related sidebar-right-section-box" id="item-editor-response-property-bar">
    <section class="tool-group clearfix" id="sidebar-right-response-properties">
        <h2><?= __('Response Properties') ?></h2>

        <div class="panel"></div>
    </section>
</div>
<div class="item-editor-modal-feedback-related sidebar-right-section-box"
     id="item-editor-modal-feedback-property-bar">
    <section class="tool-group clearfix" id="sidebar-right-response-properties">
        <h2><?= __('Modal Feedback Prop.') ?></h2>

        <div class="panel"></div>
    </section>
</div>
<div class="item-editor-body-element-related sidebar-right-section-box" id="item-editor-body-element-property-bar">
    <section class="tool-group clearfix" id="sidebar-right-body-element-properties">
        <h2><?= __('Element Properties') ?></h2>

        <div class="panel"></div>
    </section>
</div>
</div>
</div>
<!-- /right sidebar -->


</div>
<!-- preview: item may needed to be saved before -->
<div class="preview-modal-feedback modal">
    <div class="modal-body clearfix">
        <p><?= __('The item needs to be saved before it can be previewed') ?></p>

        <div class="rgt">
            <button class="btn-regular small cancel" type="button"><?= __('Cancel') ?></button>
            <button class="btn-info small save" type="button"><?= __('Save') ?></button>
        </div>
    </div>
</div>

<div id="mediaManager"></div>
<div id="modal-container"></div>
</div>

<script>
requirejs.config({
   config: {
       'taoQtiItem/controller/creator/main' : <?= json_encode(get_data('config')) ?>
   } 
});
</script>

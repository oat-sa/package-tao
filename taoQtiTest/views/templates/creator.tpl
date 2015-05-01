<div id="test-creator" data-content-target="wide">

<!-- left section: items selection -->
    <section class="test-creator-sidebar test-creator-area test-creator-items">
        <div class="action-bar plain content-action-bar horizontal-action-bar">
            <ul class="action-group plain clearfix authoring-back-box item-editor-menu">
                <li id="authoringBack" class="btn-info small">
                    <span class="li-inner">
                    <span class="icon-left"></span>
                        <?= __('Manage Tests') ?>
                    </span>
                </li>
            </ul>
        </div>
        <h1><?=__('Select Items')?></h1>
        <div class='item-selection'>
            <input id="item-filter" type="search" placeholder='<?=__('Filter')?>' />
            <br />
            <small><?=__("Use Ctrl/Meta key or Lasso for multiple selection")?></small>
            <ul class='item-box plain'></ul>
        </div>
    </section>

<!-- test editor  -->
    <section class="test-creator-test test-creator-area">
        <div class="action-bar plain content-action-bar horizontal-action-bar">
            <ul class="action-group plain clearfix authoring-back-box item-editor-menu">
                <li id="saver" class="btn-info small">
                    <span class="li-inner">
                        <span class="icon-save"></span>
                        <?=__('Save')?>
                    </span>
                </li>
            </ul>
        </div>
        <h1><span data-bind="title"></span>
            <div class="actions">
                <div class="tlb">
                    <div class="tlb-top">
                        <span class="tlb-box">
                            <span class="test-actions tlb-bar">
                                <span class="tlb-start"></span>
                                <span class="tlb-group">
                                    <a href="#" class="tlb-button-off property-toggler" title="<?=__('Manage test properties')?>">
                                        <span class="icon-settings"></span>
                                    </a>
                                </span>
                                <span class="tlb-end"></span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </h1>
        <div class="test-content">
            <div class="testparts" data-bind-each="testParts" data-bind-tmpl="testpart"> </div>
            <button class="btn-info small testpart-adder">
                <span class="icon-add"></span><?=__('New test part')?>
            </button>
        </div>
    </section>

    <section class="test-creator-sidebar test-creator-area test-creator-props">
        <div class="action-bar plain content-action-bar horizontal-action-bar">

        </div>
       <h1><?=__('Properties')?></h1>
    </section

</div>
<script type="text/javascript">
requirejs.config({
    config: {
        'taoQtiTest/controller/creator/creator' : {
            routes : {
                get  : '<?=get_data('loadUrl')?>',
                save  : '<?=get_data('saveUrl')?>',
                items : '<?=get_data('itemsUrl')?>',
                identifier : '<?=get_data('identifierUrl')?>'
            },
            labels : <?=get_data('labels')?>
       }
    }
});
</script>

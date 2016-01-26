<div class="data-container-wrapper flex-container-full">
    
    <!-- is this field still required? -->
    <input type='hidden' name='uri' value="<?=get_data('uri')?>" />

    <section class="double">
        <header>
            <h1><?=__('Available Items')?></h1>
        </header>

        <div>
            <span class="elt-info"><?=__('Select the items composing the test.')?></span>
            <div id="item-tree"></div>
        </div>

        <footer>
            <button id="saver-action-item" class="btn btn-info small"><span class="icon-add"></span><?=__('Add to the test')?></button>
        </footer>
    </section>

    <section class="sequence-container">
        <header>
            <h1><?=__('Items sequence')?></h1>
        </header>

        <div>
            <span class="elt-info" <?php if (!count(get_data('itemSequence'))) echo ' style="display:none"' ?>><?=__('Drag and drop the items to order them')?></span>
            <ul id="item-sequence" class="listbox">
            <?php foreach(get_data('itemSequence') as $index => $item):?>
                <li id="item_<?=$item['uri']?>" >
                    <?=$index?>. <?=$item['label']?>
                </li>
            <?php endforeach?>
            </ul>
        </div>

        <footer>
            <button class="saver btn btn-info small"><span class="icon-save"></span><?=__('Save')?></button>
        </footer>
    </section>

    <section class="sequence-container">
        <header>
            <h1><?=__('Test configuration')?></h1>
        </header>

        <div id="config-list">
            <?php foreach(get_data('testConfig') as $config => $value):?>
            <div id="config_<?=$config?>" >
                <label>
                    <input type="checkbox" name="<?=$config?>" value="1" <?=($value['checked'])?'checked':'';?>>
                    <span class="icon-checkbox"></span>
                    <?=$value['label']?>
                </label>
            </div>
            <?php endforeach?>
        </div>

        <footer>
            <button class="saver btn btn-info small"><span class="icon-save"></span><?=__('Save')?></button>
        </footer>
    </section>

</div>

<script>
requirejs.config({
    config: {
        'taoTestLinear/controller/Authoring/index' : {
            sequence    : <?=get_data('relatedItems')?>,
            labels      : <?=get_data('allItems')?>,
            saveurl     : <?=json_encode(get_data('saveUrl'))?>,
            openNodes   : <?=json_encode(get_data('itemOpenNodes'))?>,
            rootNode    : <?=json_encode(get_data('itemRootNode'))?>
        }
    }
});
</script>

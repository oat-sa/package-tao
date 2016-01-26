<li class="search-area"
    title="<?= get_data('searchLabel') ?>"
    data-url="<?= _url('searchParams', 'Search', 'tao', array('rootNode' => get_data('rootNode'))) ?>">
    <input type="text" value="" name="query" placeholder="<?= get_data('searchLabel') ?>">
    <button class="icon-find" type="button"></button>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">
        <div>
        <strong>ex:</strong> <em>label:exam* AND model:QTI</em>
        </div>
        <hr style="margin:5px 0;"/>
        <?php foreach (oat\tao\model\search\IndexService::getIndexesByClass(new \core_kernel_classes_Class(get_data('rootNode'))) as $uri => $indexes): ?>
            <?php foreach ($indexes as $index): ?>
            <div>
                <?php $prop = new core_kernel_classes_Property($uri); ?>
                <span class="<?= ($index->isFuzzyMatching()) ? "icon-find" : "icon-target" ?>"></span> <strong><?= _dh($index->getIdentifier()) ?></strong> (<?= _dh($prop->getLabel()) ?>)
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <hr style="margin:5px 0;"/>
        <div class="grid-row" style="min-width:250px; margin: 0">
            <div class="col-6" style="margin: 0">
                <span class="icon-find"></span> = Fuzzy Matching
            </div>
            <div class="col-6" style="margin: 0">
                <span class="icon-target"></span> = Exact Matching
            </div>
        </div>
    </div>
</li>
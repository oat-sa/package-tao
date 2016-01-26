<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;

$sections = get_data('sections');
?>

<?php if ($sections): ?>
    <div class="section-container">
        
        <ul class="tab-container clearfix">
            <?php foreach ($sections as $section): ?>

                <li class="small <?php if($section->getDisabled()):?>disabled<?php endif?>">
                    <a href="#panel-<?= $section->getId() ?>"
                       data-url="<?= $section->getUrl() ?>"
                       title="<?= $section->getName(); ?>"><?= __($section->getName()) ?></a>
                </li>

            <?php endforeach ?>
        </ul>

        <?php foreach ($sections as $section): ?>
            <div class="hidden clear content-wrapper content-panel" id="panel-<?= $section->getId() ?>">
            
                <section class="navi-container">
                    <div class="section-trees">
                        <?php foreach ($section->getTrees() as $i => $tree):
                        ?>
                            <div class="tree-block">
                                <div class="plain action-bar horizontal-action-bar">
                                </div>
                            </div>

                            <div id="tree-<?= $section->getId() ?>"
                                 class="taotree taotree-<?= is_null($tree->get('className')) ? 'default' : strtolower(
                                     $tree->get('className')
                                 ) ?>"
                                 data-url="<?= $tree->get('dataUrl') ?>"
                                 data-rootNode="<?= $tree->get('rootNode') ?>"
                                 data-actions="<?= htmlspecialchars(json_encode($tree->getActions()), ENT_QUOTES) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
    
                    <div class="tree-action-bar-box">
                        <ul class="plain action-bar tree-action-bar vertical-action-bar">
                        <?php  
                            Template::inc('blocks/actions.tpl', 'tao', array(
                                'actions' => $section->getActionsByGroup('tree')
                            ));
                        ?>
                        </ul>
                        <ul class="hidden action-bar">
                        <?php
                            Template::inc('blocks/actions.tpl', 'tao', array(
                                'actions' => $section->getActionsByGroup('none')
                            )); 
                        ?>
                        </ul>
                    </div>

                </section>

                <section class="content-container">
                    <ul class="plain action-bar content-action-bar horizontal-action-bar">
                        <?php
                        Template::inc('blocks/actions.tpl', 'tao', array(
                                'action_classes' => 'btn-info small',
                                'actions' => $section->getActionsByGroup('content')
                            ));
                        ?>
                        <?php
                        foreach ($section->getTrees() as $i => $tree) {
                            if (!is_null($tree->get('rootNode'))) {
                                Template::inc('blocks/search.tpl', 'tao', array(
                                    'rootNode' => $tree->get('rootNode'),
                                    'searchLabel' => __('Search %s', $tree->get('className'))
                                ));
                            }
                        }
                        ?>
                    </ul>

                    <div class="content-block"></div>

                </section>

            </div>
        <?php endforeach ?>

        <aside class="meta-container">
            <div id="section-meta"></div>
        </aside>
    </div>
<?php endif; ?>

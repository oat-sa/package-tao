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
                        <?php foreach ($section->getTrees() as $i => $tree): ?>
                            <div class="tree-block">
                                <?php 
                                    Template::inc('blocks/actions.tpl', 'tao', array(
                                        'actions_id' => 'tree-actions-'.$i, 
                                        'actions_classes' => 'search-action-bar horizontal-action-bar', 
                                        'action_classes' => 'tree-search btn-info small', 
                                        'actions' => $section->getActionsByGroup('search')
                                    )); 
                                ?>
                            </div>
                            <div class="search-form">
                                <div data-purpose="search" data-current="none" class="search-area search-search"></div>
                            </div>
                            <div class="filter-form">
                                <div class="search-area search-filter">
                                    <div class="xhtml_form">
                                        <div class="form-group">
                                            <input type="text" autocomplete="off" placeholder="<?=__('You can use * as a wildcard')?>">
                                        </div>
                                    </div>
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
                        <?php  
                            Template::inc('blocks/actions.tpl', 'tao', array(
                                'actions_classes' => 'tree-action-bar vertical-action-bar', 
                                'actions' => $section->getActionsByGroup('tree')
                            )); 
                            Template::inc('blocks/actions.tpl', 'tao', array(
                                'actions_classes' => 'hidden', 
                                'actions' => $section->getActionsByGroup('none')
                            )); 
                        ?>
                    </div>

                </section>

                <section class="content-container">
                    <?php  
                        Template::inc('blocks/actions.tpl', 'tao', array(
                            'actions_classes' => 'content-action-bar horizontal-action-bar', 
                            'action_classes' => 'btn-info small', 
                            'actions' => $section->getActionsByGroup('content')
                        )); 
                    ?>

                    <div class="content-block"></div>

                </section>

            </div>
        <?php endforeach ?>

        <aside class="meta-container">
            <div id="section-meta"></div>
        </aside>
    </div>
<?php endif; ?>

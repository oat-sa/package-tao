<div class="splash-screen-wrapper tao-scope">
    <div id="splash-screen" class="modal splash-modal">
        <div class="modal-title">
            <?=__('Get started with TAO')?>
        </div>
        <ul class="modal-nav plain clearfix">
            <li class="active"><a href="#" data-panel="overview"><?=__('Overview')?></a></li>
            <li><a href="#" data-panel="videos"><?=__('Videos')?></a></li>
        </ul>
        <div class="splash-content-wrap">
            <div class="modal-content">
                <div class="panels" data-panel-id="overview" style="display: block;">
                    <p><?=__('Discover how easy it is to create an assessment with TAO!')?></p>
                    <?php $extensions = get_data('defaultExtensions')?>
                    <div class="diagram">
                        <div class="grid-row">
                            <div class="col-6">
                                <a href="#"
                                   class="block pentagon<?php if(!$extensions['items']['enabled']): ?> disabled<?php endif ?>"
                                   data-module-name="items"
                                   data-url="<?=_url('index', 'Main', 'taoCe', array('structure' => 'items', 'ext' => $extensions['items']['extension']))?>">
                                    <span class="icon-item"></span>
                                    <?=__($extensions['items']['name'])?>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#"
                              class="block pentagon <?php if(!$extensions['TestTaker']['enabled']): ?> disabled<?php endif ?>" 
                              data-module-name="TestTaker"
                              data-url="<?=_url('index', 'Main', 'taoCe', array('structure' => 'TestTaker', 'ext' => $extensions['TestTaker']['extension']))?>">
                                    <span class="icon-test-taker"></span>
                               <?=__($extensions['TestTaker']['name'])?>
                                </a>
                            </div>
                        </div>
                        <div class="grid-row">
                            <div class="col-6">
                                <a href="#"
                                   class="block pentagon<?php if(!$extensions['tests']['enabled']): ?> disabled<?php endif ?>"
                                   data-module-name="tests"
                                   data-url="<?=_url('index', 'Main', 'taoCe', array('structure' => 'tests', 'ext' => $extensions['tests']['extension']))?>">
                                    <span class="icon-test"></span>
                                    <?=__($extensions['tests']['name'])?>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#"
                                   class="block pentagon<?php if(!$extensions['groups']['enabled']): ?> disabled<?php endif ?>"
                                   data-module-name="groups"
                                   data-url="<?=_url('index', 'Main', 'taoCe', array('structure' => 'groups', 'ext' => $extensions['groups']['extension']))?>">
                                    <span class="icon-test-takers"></span>
                                    <?=__($extensions['groups']['name'])?>
                                </a>
                            </div>
                        </div>
                        <div class="grid-row">
                            <div class="col-12">
                                <a href="#"
                                   class="block pentagon wide<?php if(!$extensions['delivery']['enabled']): ?> disabled<?php endif ?>"
                                   data-module-name="delivery"
                                   data-url="<?=_url('index', 'Main', 'taoCe', array('structure' => 'delivery', 'ext' => $extensions['delivery']['extension']))?>">
                                    <span class="icon-delivery"></span>
                                    <?=__($extensions['delivery']['name'])?>
                                </a>
                            </div>
                        </div>
                        <div class="grid-row">
                            <div class="col-12">
                                <a href="#"
                                   class="block wide<?php if(!$extensions['results']['enabled']): ?> disabled<?php endif ?>"
                                   data-module-name="results"
                                   data-url="<?=_url('index', 'Main', 'taoCe', array('structure' => 'results', 'ext' => $extensions['results']['extension']))?>">
                                    <span class="icon-result"></span>
                                    <?=__($extensions['results']['name'])?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="desc">
                        <div class="module-desc default">
                        <span><?=__('Select an icon on the left to find out more.')?><span/>
                        </div>
                        <?php foreach(get_data('defaultExtensions') as $extension): ?>
                            <div class="module-desc" data-module="<?=$extension['id']?>">
                                <span class="icon"></span>
                                <?php include 'splash/' . $extension['id'] . '.tpl' ?>
                            </div>
                        <?php endforeach?>
                    </div>
                    <?php
                    $moreShowed = false;
                    foreach(get_data('additionalExtensions') as $extension):
                        ?>
                        <?php if(!$moreShowed) echo '<span class="more">More:</span>';?>
                        <a href="#" class="module new-module" data-module-name="<?=$extension['id']?>" data-url="<?=_url('index', 'Main', 'taoCe', array('structure' => $extension['id'], 'ext' => $extension['extension']))?>">
                            <span class="icon-extension"></span>
                            <?=__($extension['name'])?>
                        </a>
                        <?php      $moreShowed = true;
                    endforeach?>
                </div>
                <div class="panels" data-panel-id="videos">
                </div>
            </div>
            <div class="modal-footer clearfix">
                <div class="checkbox-wrapper">
                    <label class="checkbox">
                        <input id="nosplash" type="checkbox" <?php if(get_data('firstTime') == false): ?>checked="checked"<?php endif ?> />
                        <span class="icon-checkbox"></span>
                        <?=__('Do not show this window again when TAO opens.')?>
                    </label>
                    <span class="note"><?=__('Hint: You can access this overview at any time via the Help button.')?></span>
                </div>
                <button id="splash-close-btn" class="btn-info" type="button" disabled="disabled"><?=__('Go to ')?><span class="module-name"><?=__('selection')?></span></button>
            </div>
        </div>
    </div>
</div>

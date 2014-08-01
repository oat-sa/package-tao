<?php
use oat\tao\helpers\Template;

Template::inc('layout_header.tpl', 'tao')
?>
<div class="content-wrap">
    <div id="main-menu" class="ui-state-default">
        <a href="<?=_url('entry', 'Main', 'tao')?>" title="<?=__('TAO Home')?>"><span id="menu-bullet"></span></a>

        <div class="left-menu tao-scope">
            <?php foreach(get_data('menu') as $entry): ?>
            <span <? if (get_data('shownExtension') == $entry['extension']): ?>class="current-extension"<?php endif ?>>
            <a href="<?=$entry['url']?>" title="<?=__($entry['description'])?>"><?=__($entry['name'])?></a>
            </span>
            <?php endforeach ?>
        </div>

        <div class="right-menu tao-scope">


            <div>
                <a id="logout" href="<?=_url('logout', 'Main', 'tao')?>" title="<?=__('Log Out')?>">
                    <span class="icon-logout"></span>
                </a>
            </div>
            <?php if (tao_models_classes_accessControl_AclProxy::hasAccess(null, 'UserSettings', 'tao')): ?>
            <div class="vr">|</div>
            <div class="usersettings">
                <a id="usersettings"
                   href="<?=_url('index', 'Main', 'tao', array('structure' => 'user_settings', 'ext' => 'tao'))?>"
                   title="<?=__('My profile')?>">
                    <span class="icon-user"></span>
                    <span class="username"><?=get_data('userLabel')?></span>
                </a>
            </div>
            <?php endif ?>

            <div class="vr">|</div>

            <?php foreach(get_data('toolbar') as $action):?>
            <div>
                <a id="<?=$action['id']?>" <? if(isset($action['js'])): ?> href="#" data-action="<?=$action['js']?>"
                <?php else : ?>
                href="<?=$action['url']?>"
                <?php endif ?> title="<?=__($action['title'])?>">

                <?php if(isset($action['icon'])): ?>
                <span class="<?=$action['icon']?>"></span>
                <?php endif ?>

                <?php if(isset($action['text'])): ?>
                <?=__($action['text'])?>
                <?php endif ?>

                </a>
            </div>
            <? endforeach ?>

            <div class="breaker"></div>
        </div>
    </div>

    <? if(get_data('sections')):?>

    <div id="tabs">
        <ul>
            <?php foreach(get_data('sections') as $section):?>
            <li id="tab-<?=$section['id']?>"><a id="<?=$section['id']?>" href="<?=ROOT_URL . substr($section['url'], 1) ?>"
                   title="<?=$section['name']?>"><?=__($section['name'])?></a></li>
            <?php endforeach ?>
        </ul>

        <div id="sections-aside">
            <div id="section-trees"></div>
            <div id="section-actions"></div>
        </div>
        <div class="clearfix"></div>
        <div id="section-meta"></div>
    </div>
    <?php endif; ?>

</div>
<!-- /content-wrap -->
<?php Template::inc('layout_footer.tpl', 'tao') ?>
</body>
</html>
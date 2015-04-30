<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
?>
<!doctype html>
<html class="no-js">
<head>
    <script src="<?= Template::js('lib/modernizr-2.8/modernizr.js', 'tao')?>"></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Layout::getTitle() ?></title>
    <link rel="shortcut icon" href="<?= Template::img('img/favicon.ico') ?>"/>



    <link rel="stylesheet" href="<?= Template::css('preview.css','taoItems') ?>" />
    <?= tao_helpers_Scriptloader::render() ?>
    <?= Layout::getAmdLoader() ?>
    <?php if (($themeUrl = Layout::getThemeUrl()) !== null): ?>
    <link rel="stylesheet" href="<?= $themeUrl ?>" />
    <?php endif; ?>
</head>

<body>
<div id="requirement-check" class="feedback-error js-hide">
    <span class="icon-error"></span>
    <span class="requirement-msg-area"><?=__('You must activate JavaScript in your browser to run this application.')?></span>
</div>
<script src="<?= Template::js('layout/requirement-check.js', 'tao')?>"></script>

    <div class="content-wrap">

        <?php /* alpha|beta|sandbox message */
        if(empty($_COOKIE['versionWarning'])) {
            Template::inc('blocks/version-warning.tpl', 'tao');
        }?>

        <?php /* <header> + <nav> */
        Template::inc('blocks/header.tpl', 'tao'); ?>


        <div id="feedback-box"></div>

        <?php /* actual content */
        $contentTemplate = Layout::getContentTemplate();
        Template::inc($contentTemplate['path'], $contentTemplate['ext']); ?>
    </div>

<footer class="dark-bar">
    <?php
    if (!$val = Layout::getCopyrightNotice()):
    ?>
    © 2013 - <?= date('Y') ?> · <span class="tao-version"><?= TAO_VERSION_NAME ?></span> ·
    <a href="http://taotesting.com" target="_blank">Open Assessment Technologies S.A.</a>
    · <?= __('All rights reserved.') ?>
    <?php else: ?>
    <?= $val ?>
    <?php endif; ?>
    <?php $releaseMsgData = Layout::getReleaseMsgData();
    if ($releaseMsgData['is-unstable'] || $releaseMsgData['is-sandbox']): ?>
        <span class="rgt">
            <?php if ($releaseMsgData['is-unstable']): ?>
                <span class="icon-warning"></span>

            <?php endif; ?>
            <?=$releaseMsgData['version-type']?> ·
        <a href="<?=$releaseMsgData['link']?>" target="_blank"><?=$releaseMsgData['msg']?></a></span>

    <?php endif; ?>
</footer>
<div class="loading-bar"></div>
</body>
</html>

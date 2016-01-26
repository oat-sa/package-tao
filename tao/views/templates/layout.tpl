<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
use oat\tao\model\theme\Theme;

$releaseMsgData = Layout::getReleaseMsgData();

// yellow bar if
// never removed by user
// and version considered unstable resp. sandbox
$hasVersionWarning = empty($_COOKIE['versionWarning'])
    && !!$releaseMsgData['msg']
    && ($releaseMsgData['is-unstable']
    || $releaseMsgData['is-sandbox']);
?>
<!doctype html>
<html class="no-js<?php if (!$hasVersionWarning): ?> no-version-warning<?php endif;?>">
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
    <link rel="stylesheet" href="<?= Layout::getThemeStylesheet(Theme::CONTEXT_BACKOFFICE) ?>" />
</head>

<body>
<div id="requirement-check" class="feedback-error js-hide">
    <span class="icon-error"></span>
    <span class="requirement-msg-area"><?=__('You must activate JavaScript in your browser to run this application.')?></span>
</div>
<script src="<?= Template::js('layout/requirement-check.js', 'tao')?>"></script>

<div class="content-wrap">

    <?php /* alpha|beta|sandbox message */
    if($hasVersionWarning) {
        Template::inc('blocks/version-warning.tpl', 'tao');
    }?>

    <?php /* <header> + <nav> */
    Template::inc('blocks/header.tpl', 'tao'); ?>

    <div id="feedback-box"></div>

    <?php /* actual content */
    $contentTemplate = Layout::getContentTemplate();
    Template::inc($contentTemplate['path'], $contentTemplate['ext']); ?>
</div>

<?=Layout::renderThemeTemplate(Theme::CONTEXT_BACKOFFICE, 'footer')?>

<div class="loading-bar"></div>
</body>
</html>

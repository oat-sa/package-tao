<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
use oat\tao\model\theme\Theme;
?><!doctype html>
<html class="no-js" lang="<?= tao_helpers_I18n::getLangCode() ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo __("QTI 2.1 Test Driver"); ?></title>
    <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao') ?>"/>
    <link rel="stylesheet" href="<?= Template::css('test-runner.css', 'taoQtiTest') ?>"/>
    <link rel="stylesheet" href="<?= Layout::getThemeStylesheet(Theme::CONTEXT_FRONTOFFICE) ?>" />
    <script src="<?= Template::js('lib/require.js', 'tao') ?>"></script>
    <script>
        (function () {
            requirejs.config({waitSeconds: <?=get_data('client_timeout')?> });
            require(['<?=get_data('client_config_url')?>'], function () {
                require(['taoQtiTest/controller/runtime/testRunner', 'mathJax', '<?=get_data('client_session_state_service')?>'], function (testRunner, MathJax, sessionStateService) {
                    if (MathJax) {
                        MathJax.Hub.Configured();
                    }

                    var assessmentTestContext  = <?=json_encode(get_data('assessmentTestContext'), JSON_HEX_QUOT | JSON_HEX_APOS)?>;
                    assessmentTestContext.sessionStateService = sessionStateService;

                    testRunner.start(assessmentTestContext);
                });
            });
        }());
    </script>
</head>
<body class="qti-test-scope">
<div id="feedback-box"></div>
<div class="section-container">
    <div class="plain action-bar content-action-bar horizontal-action-bar top-action-bar">
        <div class="control-box size-wrapper">
            <div class="title-box truncate">
                <span data-control="qti-test-title" class="qti-controls"></span>
                <span data-control="qti-test-position" class="qti-controls"></span>
            </div>
            <div class="item-number-box">
                <div data-control="item-number" class="qti-controls lft"></div>
            </div>
            <div class="timer-box">
                <div data-control="qti-timers" class="qti-controls lft"></div>
            </div>
            <div class="progress-box">
                <div data-control="progress-bar" class="qti-controls"></div>
                <div data-control="progress-label" class="qti-controls"></div>
            </div>
        </div>
    </div>

    <div class="content-panel<?= get_data('review_screen') ? ' has-review-screen' : ''; ?><?= get_data('review_region') ? ' review-screen-region-' . get_data('review_region') : ''; ?>">
        <!--div class="test-sidebar test-sidebar-left flex-container-navi">
        </div-->
        <div class="test-item flex-container-remaining">
            <div id="qti-content">
            </div>
        </div>
        <!--div class="test-sidebar test-sidebar-right flex-container-navi">
            optional navi right
        </div-->
    </div>

    <div class="plain action-bar content-action-bar horizontal-action-bar bottom-action-bar">
        <div class="control-box size-wrapper">
            <div class="lft tools-box">
                <ul class="plain tools-box-list"></ul>
            </div>
            <div class="rgt navi-box">
                <ul class="plain navi-box-list">
                    <li data-control="move-backward" class="small btn-info action" title="<?= __("Submit and go to the previous item"); ?>">
                        <a class="li-inner" href="#">
                            <span class="icon-backward"></span>
                            <span class="text"><?= __("Previous"); ?></span>
                        </a>
                    </li>
                    <li data-control="move-forward" class="small btn-info action forward" title="<?= __("Submit and go to the next item"); ?>">
                        <a class="li-inner" href="#">
                            <span class="icon-forward"></span>
                            <span class="text"><?= __("Next"); ?></span>
                        </a>
                    </li>
                    <li data-control="move-end" class="small btn-info action forward" title="<?= __("Submit and go to the end of the test"); ?>">
                        <a class="li-inner" href="#">
                            <span class="icon-fast-forward"></span>
                            <span class="text"><?= __("End Test"); ?></span>
                        </a>
                    </li>
                    <li data-control="next-section" class="small btn-info action" title="<?= __("Skip to the next section"); ?>">
                        <a class="li-inner" href="#">
                            <span class="icon-external"></span>
                            <span class="text"><?= __("Next Section"); ?></span>
                        </a>
                    </li>
                    <li data-control="skip" class="small btn-info action skip" title="<?= __("Skip to the next item"); ?>">
                        <a class="li-inner" href="#">
                            <span class="icon-external"></span>
                            <span class="text"><?= __("Skip"); ?></span>
                        </a>
                    </li>
                    <li data-control="skip-end" class="small btn-info action skip" title="<?= __("Skip to the end of the test"); ?>">
                        <a class="li-inner" href="#">
                            <span class="icon-external"></span>
                            <span class="text"><?= __("Skip &amp; End Test"); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="timeout-modal-feedback modal">
    <div class="modal-body clearfix">
        <p><?= __('Time limit reached, this part of the test has ended.') ?></p>
        <div class="rgt">
            <button class="btn-info small js-timeout-confirm" type="button"><?= __('Ok') ?></button>
        </div>
    </div>
</div>
<div class="exit-modal-feedback modal">
    <div class="modal-body clearfix">
        <p class="message"></p>
        <div class="rgt">
            <button class="btn-warning small js-exit-confirm" type="button"><?= __('Yes') ?></button>
            <button class="btn-info small js-exit-cancel" type="button"><?= __('No') ?></button>
        </div>
    </div>
</div>
</body>
</html>

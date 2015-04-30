<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;

/* alpha|beta|sandbox message */
$releaseMsgData = Layout::getReleaseMsgData();
?>
<header class="dark-bar clearfix">
    <a href="<?= $releaseMsgData['link'] ?>" title="<?=$releaseMsgData['msg'] ?>" class="lft"
       target="_blank">
        <img src="<?= $releaseMsgData['logo']?>" alt="TAO Logo" id="tao-main-logo"/>
    </a>

    <?php /* main navigation bar */
    !common_session_SessionManager::isAnonymous()
        ? Template::inc('blocks/main-navi.tpl', 'tao')
        : '';
    ?>

</header>
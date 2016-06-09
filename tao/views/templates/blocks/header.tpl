<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
use oat\tao\model\theme\Theme;
?>
<header class="dark-bar clearfix">
    
    <?=Layout::renderThemeTemplate(Theme::CONTEXT_BACKOFFICE, 'header-logo')?>
    
    <?php /* main navigation bar */
    !common_session_SessionManager::isAnonymous()
        ? Template::inc('blocks/header-main-navi.tpl', 'tao')
        : '';
    ?>

</header>
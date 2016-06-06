<?php 
use oat\tao\helpers\Layout;
/* alpha|beta|sandbox message */
$releaseMsgData = Layout::getReleaseMsgData();
if($releaseMsgData['link']):?>
    <a href="<?=$releaseMsgData['link']?>" title="<?=$releaseMsgData['msg']?>" class="lft" target="_blank">
    <?php else:?>
        <div class="lft">
        <?php endif;?>
        <img src="<?=$releaseMsgData['logo']?>" alt="TAO Logo" id="tao-main-logo"/>
        <?php if($releaseMsgData['link']):?>
    </a>
<?php else:?>
    </div>
<?php endif;?>
<?php
use oat\tao\helpers\Template;
?>
<style>
.feedback-warning .icon-lock {
  color: rgb(216, 174, 91);
}

.feedback-warning th {
  padding-bottom: 20px;
}

.feedback-warning td.em {
  padding-top: 10px;
}
</style>
<header class="section-header flex-container-full">
    <h2><?=__('%s Locked', get_data('topclass-label'))?></h2>
</header>
<div class="main-container flex-container-main-form">
    <div class="feedback feedback-warning">
        <table>
    		<tr>
    		  <th colspan="2" class="txt-lft"><span class="icon-lock warning-color big"></span> <?=__('This %s is currently checked out', get_data('topclass-label'))?></th>
    	    </tr>
    		<tr>
    		    <td><?=__('User')?></td>
                <td class="b"><?=get_data('ownerHtml')?></td>
            </tr>
    		<tr>
    		    <td><?=__('Date')?></td>
                <td class="b"><?=tao_helpers_Date::displayeDate(get_data('lockDate'))?></td>
            </tr>
            <?php if (has_data('forceRelease') && get_data('forceRelease')) : ?>
    		<tr>
                <td colspan="2" class="em">
                    <?=__('Releasing the lock might cause the work in progress by %s to be lost.', get_data('ownerHtml'))?>
                </td>
            </tr>
    		<tr>
                <td colspan="2" class="txt-rgt button-container">
                    <button class="btn btn-warning small forcerelease" data-id="<?= get_data('id')?>"><span class="icon-unlock warning"></span><?=__('Force Release')?></button>
                </td>
    	    </tr>
            <?php else : ?>
            <tr>
    		    <td colspan="2" class="em"><?=__('Please contact %s or an administrator to release it', get_data('ownerHtml'))?></td>
      	    </tr>
    	    <?php endif;?>
        </table>
	</div>
</div>	

<?php Template::inc('footer.tpl', 'tao'); ?>

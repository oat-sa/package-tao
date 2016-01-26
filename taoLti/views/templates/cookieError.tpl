<?php
use oat\tao\helpers\Template;

Template::inc('layout_header.tpl', 'tao'); 
?>
<div class="main-container tao-scope" id="delivery-main-container">
    <div class="ui-widget-header ui-corner-top ui-state-default">
    <?=__('Third party cookies are not supported by your browser')?>
    </div>
    <div id="delivery-feedback" class="ui-widget ui-widget-content container-content">
        <div class="feedback-warning">
            <span class="icon-warning"></span><?=__('The LTI tool could not be opened in the current window. Please click on the button to open it in a new window.')?>
        </div>
        <div>
            <a class="btn-info" href="<?=_url('restoreSession', null, null, array('session' => get_data('session'), 'redirect' => get_data('redirect')))?>" target="_blank">
                <?=__('Open in a new window')?>
            </a>
        </div>
    </div>
</div>
<?php
Template::inc('footer.tpl','tao');
?>
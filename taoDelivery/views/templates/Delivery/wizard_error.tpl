<div class="main-container" data-tpl="taoSimpleDelivery/wizard_error.tpl">
    <div class="ui-widget-header ui-corner-top ui-state-default">
    <?=__('Create a new delivery')?>
    </div>
    <div id="delivery-feedback" class="ui-widget ui-widget-content container-content">
        <div class="feedback-warning">
            <span class="icon-warning"></span><?=__('There are currently no tests available to publish.')?>
        </div>
        <div>
            <a class="btn-info" href="<?=_url('index', 'Main', 'tao', array('structure' => 'tests', 'ext' => 'taoTests'))?>"><?=__('Create a test')?></a>
        </div>
    </div>
</div>
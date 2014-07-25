<link rel="stylesheet" href="<?= BASE_WWW ?>css/authoring.css" />
<div id="qti-test-authoring">

    <!-- item tree -->
    <div id="item-container" class="data-container">
            <div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
                    <?=__('Available Items')?>
            </div>
            <div class="ui-widget ui-widget-content container-content">
                    <span class="elt-info"><?=__('Select the items composing the test.')?></span>
                    <div id="item-tree"></div>
                    <div class="breaker"></div>
            </div>
            <div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
                    <input id="saver-action-item" type="button" value="<?=__('Save')?>" />
            </div>
    </div>


    <!-- Test Options and item sequence -->
    <div id="qti-test-container" class="data-container" style="width:74%">
        <form>
            <input type='hidden' name='uri' value="<?=get_data('uri')?>" />

            <div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
                <?=__('Test')?>
            </div>
            <div class="ui-widget ui-widget-content container-content xhtml_form">

                <fieldset>
                    <legend><?=__('Items sequence')?></legend>
                    <div id="item-list">
                        <div>
                            <label class="form_desc"><?=__('Shuffle')?></label>
                            <input type="checkbox" name="shuffle" value="true" 
                            <?if(get_data('option_shuffle')):?>
                                checked="checked"
                            <?endif?>
                            />
                        </div>
                        <div>
                            <span class="elt-info" <?php if (!count(get_data('itemSequence'))) echo ' style="display:none"' ?>><?=__('Drag and drop the items to order them')?></span>
                            <ul id="item-sequence" class="sortable-list">
                            <?foreach(get_data('itemSequence') as $index => $item):?>
                                    <li class="ui-state-default" id="item_<?=$item['uri']?>" >
                                            <span class='ui-icon ui-icon-arrowthick-2-n-s' ></span>
                                            <span class="ui-icon ui-icon-grip-dotted-vertical" ></span>
                                            <?=$index+1?>. <?=$item['label']?>
                                    </li>
                            <?endforeach?>
                            </ul>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><?=__('Time limits')?></legend>
                    <div>
                        <label class="form_desc"><?=__('Minimum Test Duration')?></label>
                        <input type="text" name="min-time" class="time" value="<?=get_data('option_min-time')?>" />
                    </div>
                    <div>
                        <label class="form_desc"><?=__('Maximum Test Duration')?></label>
                        <input type="text" name="max-time" class="time" value="<?=get_data('option_max-time')?>" />
                    </div>
                    <div>
                        <label class="form_desc"><?=__('Allow Late Submission')?></label>
                        <div class="form_radlst">
                            <input type="radio" id="allow-late-submission-true" name="allow-late-submission" value="true" 
                                   <?if (get_data('option_allow-late-submission')):?>
                                   checked="checked"
                                   <?endif;?>
                            />
                            <label class="elt_desc" for="allow-late-submission-true"><?=__('Yes')?></label>
                            <br />
                            <input type="radio" id="allow-late-submission-false" name="allow-late-submission" value="false" 
                                   <?if ( !get_data('option_allow-late-submission')  || !has_data('option_allow-late-submission')):?>
                                   checked="checked"
                                   <?endif;?>
                            />
                            <label class="elt_desc" for="allow-late-submission-false"><?=__('No')?></label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><?=__('Submission and Navigation')?></legend>
                    <div>
                        <label class="form_desc"><?=__('Submission mode')?></label>
                        <div class="form_radlst">
                            <input type="radio" id="submission-mode-individual" name="submission-mode" value="individual" 
                                   <?if (get_data('option_submission-mode') == 'individual' || !has_data('option_submission-mode')):?>
                                   checked="checked"
                                   <?endif;?>
                            />
                            <label class="elt_desc" for="submission-mode-individual"><?=__('Individual')?></label>
                            <br />
                            <input type="radio" id="submission-mode-simultaneous" name="submission-mode" value="simultaneous"
                                   <?if (get_data('option_submission-mode') == 'simultaneous'):?>
                                   checked="checked"
                                   <?endif;?>
                            />
                            <label class="elt_desc" for="submission-mode-simultaneous"><?=__('Simultaneous')?></label>
                    </div>
                    <div>
                        <label class="form_desc"><?=__('Navigation mode')?></label>
                        <div class="form_radlst">
                            <input type="radio" id="navigation-mode-linear" name="navigation-mode" value="linear" 
                                   <?if (get_data('option_navigation-mode') == 'linear' || !has_data('option_navigation-mode')):?>
                                   checked="checked"
                                   <?endif;?>
                            />
                            <label class="elt_desc" for="navigation-mode-linear"><?=__('Linear')?></label>
                            <br />
                            <input type="radio" id="navigation-mode-nonlinear" name="navigation-mode" value="nonlinear" 
                                   <?if (get_data('option_navigation-mode') == 'nonlinear'):?>
                                   checked="checked"
                                   <?endif;?>
                            />
                            <label class="elt_desc" for="navigation-mode-nonlinear"><?=__('Non Linear')?></label>
                    </div>
                </fieldset>
            </div>
            <div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
                    <input id="saver-action-item-sequence" type="button" value="<?=__('Save')?>" />
            </div>
        </form>
    </div>
                    
</div>


<script type="text/javascript">
(function(){
    
    var authoringOptions = {
        sequence : <?=get_data('relatedItems')?>,
        labels : <?=get_data('allItems')?>,
        saveUrl : '<?=get_data('saveUrl')?>',
        itemsTree: {
            itemsUrl : '<?=get_data('itemsUrl')?>',
            serverParameters : {
                openNodes : <?=json_encode(get_data('openNodes'))?>,
                rootNode : <?=json_encode(get_data('rootNode'))?>,
                itemModel : '<?=get_data('qtiItemModel')?>'
            }
        }
    };
    
    require(['taoQtiTest/qtiTestAuthoring'], function(QtiTestAuthoring){
        var $container = $('#qti-test-container');
        $container.on('saved.qtitestauthoring', function(saved){
            helpers.createInfoMessage("<?=__('Sequence saved successfully')?>");
        });
        QtiTestAuthoring.init($container, authoringOptions);
    });

    
    
}());
</script>
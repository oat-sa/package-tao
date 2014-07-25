<div id="feedback_rule_<?=get_data('serial')?>" class="feedback-rule-container">
    <div class="feedback-rule-if">
        <span class="feedback-desc">IF</span>
        <select class="feedback-condition">
            <?$conditions = get_data('conditions');
            foreach($conditions as $name => $desc):
                ?>
                <option value="<?=$name?>" <?if($name == get_data('condition')):?>selected="selected"<?endif;?>><?=$desc?></option>
        <?endforeach;?>
        </select>
        <input class="feedback-compared-value" type="text" value="<?=get_data('comparedValue')?>"/>
    </div>
    <div class="feedback-rule-then-else">
        <span class="feedback-desc">THEN show</span>
        <input id="<?=get_data('feedbackThen')?>" type="button" value="feedback" class="qtiAuthoring-feedback-link"/> 
    <?if(!get_data('feedbackElse')):?><a id="feedback_add_else_<?=get_data('serial')?>" title="add else condition" href="#" class="feedback-desc feedback-button-add-else">else</a><?endif;?>
    </div>
<?if(get_data('feedbackElse')):?>
        <div class="feedback-rule-then-else">
            <span class="feedback-desc">ELSE show</span>
            <input id="<?=get_data('feedbackElse')?>" type="button" value="feedback" class="qtiAuthoring-feedback-link"/>
            <span class="feedback-button-delete ui-icon ui-icon-circle-close" title="delete else feedback" id="feedback_delete_else_<?=get_data('serial')?>"></span>
        </div>
<?endif;?>
    <span class="feedback-button-delete ui-icon ui-icon-circle-close" title="delete feedback" id="feedback_delete_<?=get_data('serial')?>"></span>
</div>
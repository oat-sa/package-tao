<?php
use oat\tao\helpers\Template;
?>


<div class="main-container flex-container-main-form">
    <h2><?=__('%s Tool Provider', get_data('deliveryLabel'))?></h2>
    <div id="form-container" class="form-content">
        <div class="xhtml_form">
            <form>
                <?php if (has_data('warning')) :?>
                    <div class='feedback-warning'>
                        <?= get_data('warning')?>
                    </div>
                <?php endif;?>    
                <?php if (has_data('launchUrl')) :?>

                <div>
                    <label class="form_desc" for="copyPasteBox"><?= __('Launch URL')?></label>
                    <textarea id="copyPasteBox" name="copyPasteBox" rows="3"><?= get_data('launchUrl')?></textarea>
                <br>
                <em><?=__('Copy and paste the following URL into your LTI compatible tool consumer.')?></em>
                </div>
                <?php endif;?>
                <h3><?= __('Tool consumer(s)')?></h3>
                    <?php if (count(get_data('consumers')) > 0) :?>
                    <ul class="consumerList">
                        <?php foreach (get_data('consumers') as $consumer) :?>
                            <li><?= $consumer->getLabel()?></li>
                        <?php endforeach;?>
                    </ul>
                    <?php else:?>
                      <em><?= __('No LTI consumers defined')?></em>
                    <?php endif;?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
document.getElementById("copyPasteBox").select();
</script>
<?php
Template::inc('footer.tpl', 'tao')
?>

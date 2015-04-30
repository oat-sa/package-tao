<header class="section-header flex-container-full">
    <h2><?=__('Generate a skeleton for a student tool')?></h2>
</header>
<div class="main-container flex-container-main-form">
    <div class="form-content">
        <div class="xhtml_form">
            <form method="post" id="sts-form" name="sts-form" action="<?= _url('index')?>">
                <div>
                    <label class="form_desc" for="sts-client"><?=__('Prefix')?></label><input name="client" id="sts-client" type="text">
                </div>
                <div>
                    <label class="form_desc" for="sts-tool-title"><?=__('Tool name')?></label><input type="text" name="tool-title" id="sts-tool-title">
                </div>
                <div>
                    <label class="form_desc" for="sts-transparent"><?=__('Transparent canvas')?></label><select name="transparent" id="sts-transparent">
                        <option value="0"><?=__('No')?></option>
                        <option value="1"><?=__('Yes')?></option>
                    </select>
                </div>
                <div>
                    <label class="form_desc" for="sts-rotatable"><?=__('Rotatable')?></label><select name="rotatable" id="sts-rotatable">
                        <option value="0"><?=__('No')?></option>
                        <option value="1" selected><?=__('Yes')?></option>
                    </select>
                </div>
                <div>
                    <label class="form_desc" for="sts-movable"><?=__('Movable')?></label><select name="movable" id="sts-movable">
                        <option value="0"><?=__('No')?></option>
                        <option value="1" selected><?=__('Yes')?></option>
                    </select>
                </div>
                <div>
                    <label class="form_desc" for="sts-adjustable-x"><?=__('Adjustable on x-axis')?></label><select name="adjustable-x" id="sts-adjustx">
                        <option value="0"><?=__('No')?></option>
                        <option value="1" selected><?=__('Yes')?></option>
                    </select>
                </div>
                <div>
                    <label class="form_desc" for="sts-adjustable-y"><?=__('Adjustable on y-axis')?></label><select name="adjustable-y" id="sts-adjusty">
                        <option value="0"><?=__('No')?></option>
                        <option value="1" selected><?=__('Yes')?></option>
                    </select>
                </div>
                <div class="form-toolbar">
                    <a href="#" class="form-submitter btn-success small"><span class="icon-save"></span><?=__('Generate')?></a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="data-container-wrapper flex-container-remaining">
</div>
<script>
    <?php if(has_data('errorMessage') || has_data('message')): ?>
    require(['ui/feedback'], function(feedback){
        <?php if(has_data('errorMessage')): ?>
        feedback().error("<?= get_data('errorMessage') ?>");
        <?php endif; ?>

        <?php if(has_data('message')): ?>
        feedback().success("<?= get_data('message') ?>");
        <?php endif; ?>
    });
    <?php endif; ?>
</script>


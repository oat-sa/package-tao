<div id="login-box">
    <?php if (has_data('msg')) : ?>
        <span class="loginHeader">
		    <span class="hintMsg"><?= get_data('msg') ?></span>
		</span>
    <?php endif; ?>
    <?php if (get_data('errorMessage')): ?>
        <div class="ui-widget ui-corner-all ui-state-error error-message">
            <?= urldecode(get_data('errorMessage')) ?>
        </div>
    <?php endif ?>
    <?= get_data('form') ?>
</div>
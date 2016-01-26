<?php
use oat\tao\helpers\Layout;
?>
<div class="password-recovery-form entry-point entry-point-container">
    <h1><?= get_data('header') ?></h1>
    <?php if (get_data('info')): ?>
    <p class="feedback-info">
        <span class="icon-info"></span>
        <?= get_data('info') ?>
    </p>
    <?php endif; ?>
    <?php if (get_data('error')): ?>
    <p class="feedback-error">
        <span class="icon-error"></span>
        <?= get_data('error') ?>
    </p>
    <?php endif; ?>
    <a href="<?= _url('login', 'Main', 'tao') ?>"> <?= __("Return to sign in page") ?></a>
</div>
<script>
    requirejs.config({
        config: {
            'tao/controller/passwordRecovery': {
                'message' : {
                    'info': <?=json_encode(get_data('msg'))?>,
                    'error': <?=json_encode(urldecode(get_data('errorMessage')))?>
                }
            }
        }
    });
</script>

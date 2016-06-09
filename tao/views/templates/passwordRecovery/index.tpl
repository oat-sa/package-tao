<?php
use oat\tao\helpers\Layout;
?>
<div class="password-recovery-form entry-point entry-point-container">
    <h1><?= __("Forgot your password?") ?></h1>
    <?= get_data('form') ?>
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

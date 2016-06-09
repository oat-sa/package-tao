<p>
<?= __("Hi, %s<br>You indicated that you have forgotten your TAO password.", get_data('user_name'))?>
</p>
<p>
<?= __("To reset your password, click the link below, or paste it into your browser. You will then be prompted to create a new password.")?>
</p>
<p>
<a href="<?= get_data('link') ?>"><?= get_data('link') ?></a>
</p>
<p>
<?= __("If you do not wish to reset your password, just ignore this email and your password will remain the same.")?>
</p>
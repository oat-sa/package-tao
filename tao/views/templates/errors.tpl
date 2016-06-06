<?php if (get_data('errorMessage')): ?>
    <script>
            helpers.createErrorMessage("<?=get_data('errorMessage')?>");
    </script>
<?php endif ?>

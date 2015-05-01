<script>
requirejs.config({
    config: {
        'uiForm': {
            'action'    : "<?=get_data('action')?>",
            'module'    : "<?=get_data('module')?>",
            'extension' : "<?=get_data('extension')?>"
        }
    }
});
</script>

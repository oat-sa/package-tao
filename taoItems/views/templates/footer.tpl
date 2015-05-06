<script type="text/javascript">
requirejs.config({
    config: {
        'taoItems/controller/items': {
            'action'                : <?=json_encode(get_data('action'))?>,
            'uri'                   : <?=json_encode(get_data('uri'))?>,
            'classUri'              : <?=json_encode(get_data('classUri'))?>,
            'label'                 : <?=json_encode(get_data('label'))?>,
            'reload'                : <?=has_data('reload') ? 'true' : 'false' ?>,
            'message'               : <?=has_data('message') ? json_encode(get_data('message')) : 'false' ?>,
            'isAuthoringEnabled'    : <?=get_data('isAuthoringEnabled') ? 'true' : 'false'?>,
            'isPreviewEnabled'      : <?=get_data('isPreviewEnabled') ? 'true' : 'false'?>,
            'authoringUrl'          : <?=json_encode(get_data('authoringUrl'))?>,
            'previewUrl'            : <?=json_encode(get_data('previewUrl'))?>
        }
    }
});
</script>

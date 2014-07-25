<div id="qtiAuthoring_interaction_left_container">
    <div id="qtiAuthoring_interactionEditor">

        <div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default">
            <?=get_data('interactionType')?> <?=__('Interaction Editor')?>
        </div>
        <div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
            <div class="ext-home-container qti-form-container">
                <?=get_data('formInteraction')?>
            </div>

            <div>
                <div id="formInteraction_preview_container_title"><?=__('Media Preview')?>&nbsp;:<span class="qti-img-preview-label"></span></div>
                <div id="formInteraction_preview_container">
                    <div id="formInteraction_object_preview">
                        <?=__('No media selected').'. '.__('Please set the medial url then update the interaction').'.'?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    var myInteraction = null;
    $(document).ready(function(){
        //programatically add the tip:
        var $form = $('div#formInteraction_content_<?=get_data('interactionSerial')?>');
        $form.find('input#object_data').parent('div').prepend('<p class="qti-form-tip"></p>');
        $('p.qti-form-tip').html('<?=__('Please use mp4 (H.264+AAC) for video and mp3 for audio for maximum cross-browser compability.')?><br/>(<?=__('tips: youtube video works, e.g: http://youtu.be/YJWSVUPSQqw')?>)');
        
        $form.find('input#object_width').parent('div').append('<span class="qti-form-tip"></p>');
        $form.find('input#object_height').parent('div').append('<span class="qti-form-tip"></p>');
        $('span.qti-form-tip').text('(<?=__('optional')?>)');
        
        var $eltMaxPlay = $form.find('input#maxPlays');
        var $eltLoop = $form.find('input[name=loop]');
        $eltMaxPlay.parent('div').append('<span class="qti-form-tip">0 = <?=__('unlimited')?></p>');
        $eltMaxPlay.on('keyup', function(){
            var maxPlays = parseInt($(this).val());
            if(maxPlays>0){
                $eltLoop.parent().parent().show();
            }else{
                $form.find("input#loop_1").attr('checked', 'checked');
                $eltLoop.parent().parent().hide();
            }
        }).keyup();
        
        var options = {
            data:"<?=get_data('mediaFilePath')?>",
            width:parseInt("<?=get_data('mediaFileWidth')?>"),
            height:parseInt("<?=get_data('mediaFileHeight')?>"),
            type:"<?=get_data('mediaFileType')?>",
        };
        
        try{
            myInteraction = new interactionClass('<?=get_data('interactionSerial')?>', myItem.itemSerial, {
                "mediaPreviewerOptions" : options
            });
            myInteraction.setType('<?=get_data('interactionType')?>');
        }catch(err){
            CL('error creating interaction', err);
        }
    });
</script>

<div id="qtiAuthoring_interaction_right_container">
</div>
<div style="clear:both"/>

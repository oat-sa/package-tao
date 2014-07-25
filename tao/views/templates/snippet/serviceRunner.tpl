<script src="<?=TAOBASE_WWW?>js/serviceApi/StateStorage.js"></script>
<script src="<?=TAOBASE_WWW?>js/serviceApi/ServiceApi.js"></script>
<script type="text/javascript">

require(['require', 'jquery', 'json2'], function(req, $) {
	$(function(){
		$("#loader").css('display', 'none');

		var serviceApi = <?=get_data('serviceApi')?>;
		var $frame = $('#iframe_<?=get_data('renderId')?>');
		serviceApi.loadInto($frame[0]);

		$frame.load(function() {
    		var doc = this.contentWindow || this.contentDocument;
    		if (doc.document) {
    			doc = doc.document;
    		}
    		$(this).height($(doc).height());
		});
	});
});
</script>
<iframe id='iframe_<?=get_data('renderId')?>' class="toolframe" frameborder="0" style="width:100%"></iframe>

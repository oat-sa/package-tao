<?php
use oat\tao\helpers\Template;
Template::inc('form.tpl', 'tao');
?>

<?php if(has_data('importErrorTitle')):?>
    <?php if(get_data('importErrors')):?>
        <?php
        $msg = '<div>' . get_data('importErrorTitle') ?get_data('importErrorTitle') :__('Error during file import') . '</div>';
        $msg .= '<ul>';
        foreach(get_data('importErrors') as $ierror) {
            $msg .= '<li><?=$ierror->__toString()?></li>';
        }
        $msg .= '</ul>';
        ?>
    <?php endif?>
    <script>
        require(['ui/feedback'], function(feedback){
            feedback().error(<?=$msg?>);
        });
    </script>
<?php endif ?>


<script type="text/javascript">
require(['jquery'], function($) {

	//by changing the format, the form is sent
	$(":radio[name='importHandler']").change(function(){

		var form = $(this).parents('form');
		$(":input[name='"+form.attr('name')+"_sent']").remove();
		
		form.submit();
	});

	//for the csv import options
	$("#first_row_column_names_0").attr('checked', true).click(function(){
        if ( this.checked ){
            $("#column_order").attr('disabled','disabled');
        }else{
            $("#column_order").removeAttr('disabled');
        }
	});

        //show the csv fields mapping combos
    var $mapper =  $("#property_mapping > .property-edit-container");
    $mapper.show();

    if ($mapper.length) {
        $('#formats').hide();
    }
});
</script>
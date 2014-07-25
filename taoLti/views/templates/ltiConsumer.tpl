 <div id="ltiLaunchFormSubmitArea">
    <form action="<?=get_data('launchUrl')?>"
        name="ltiLaunchForm" id="ltiLaunchForm" method="post"
        encType="application/x-www-form-urlencoded">
<?php foreach (get_data('ltiData') as $key => $value) : ?>
    <input type="hidden" name="<?=$key?>" value="<?=$value?>"/>
<?php endforeach; ?>    
    <input type="submit" value="Press to continue to external tool"/>
</form>
</div>

<script>
    document.getElementById("ltiLaunchFormSubmitArea").style.display = "none";
    document.ltiLaunchForm.submit();
</script>
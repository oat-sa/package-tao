<?
$d = new DateTime();
$weekday = $d->format('w');
$weekNumber = $d->format('W');
$diff = 6 -($weekday == 0 ? 0 : $weekday);
$d->modify("+$diff day");
if($weekNumber % 2){
    $d->modify('+7 day');
}
$date = $d->format('Y-m-d');
$rem = strtotime($date) - time();
$day = floor($rem / 86400);
$hr  = floor(($rem % 86400) / 3600);
$min = floor(($rem % 3600) / 60);
$sec = ($rem % 60);
$stringDate = $day . ' Days ' . $hr . ' Hours ' . $min .' Minutes ' . $sec . ' Seconds'
?>
        <div id="footer">
			<div id="releasesWarning" class="ui-state-highlight ui-corner-all releasestatus">
				<img src="<?=TAOBASE_WWW?>img/warning.png" alt="!" />
				<strong>DEMO Sandbox : <?=__('All data will be removed  in ') . $stringDate ?></strong>
				<br/><a href="http://forge.taotesting.com/projects/tao" target="_blank"><?= __('Please report bugs, ideas, comments, any feedback on the TAO Forge') ?></a>
			</div>
			<div id="copyright">
	    	Copyright &copy; - <?=date('Y')?> - <?=TAO_VERSION_NAME?> - Open Assessment Technologies S.A. <?= __('All rights reserved.') ?>
		</div>
		</div>

<?
$d = new DateTime();
$weekday = $d->format('w');
$weekNumber = $d->format('W');
$diff = 6 - $weekday;
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
$stringDate = $day . ' '. (($day>1)?__('days'):__('day')) .' ' . $hr . ' '. (($hr>1)?__('hours'):__('hour')) .' '. __('and') .' ' . $min .' '. (($min>1)?__('minutes'):__('minute')) . '.'; // ' ' . $sec . ' '.__('seconds').''
?>
        <div id="footer">
			<div class="ui-state-highlight ui-corner-all releasestatus">
				<img src="<?=TAOBASE_WWW?>img/warning.png" alt="!" />
				<strong><?=__('DEMO Sandbox') .': '.__('All data will be removed in ') .'<br/>'. $stringDate ?></strong>
				<br/><a href="http://forge.taotesting.com/projects/tao" target="_blank"><?= __('Please report bugs, ideas, comments, any feedback on the TAO Forge') ?></a>
			</div>
			TAO<sup>&reg;</sup> - <?=date('Y')?> - <?= __('A joint initiative of CRP Henri Tudor and the University of Luxembourg') ?>
		</div>
	</body>
</html>

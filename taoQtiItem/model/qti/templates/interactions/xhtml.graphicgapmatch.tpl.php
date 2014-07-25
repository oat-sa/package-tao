<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<div id="<?=$identifier?>" class="qti_widget qti_<?=$_type?>_interaction <?=$class?>">

	<?if(!empty($prompt)):?>
    	<p class="prompt"><?=$prompt?></p>
    <?endif?>

	<?=$data?>
</div>
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
	qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
	<?if(isset($object['data'])):?>
	qti_initParam["<?=$serial?>"]['imagePath'] = "<?=$object['data']?>";
	<?endif?>
	<?if(isset($object['width'])):?>
	qti_initParam["<?=$serial?>"]['imageWidth'] = "<?=$object['width']?>";
	<?endif?>
	<?if(isset($object['height'])):?>
	qti_initParam["<?=$serial?>"]['imageHeight'] = "<?=$object['height']?>";
	<?endif?>
	qti_initParam["<?=$serial?>"]['matchMaxes'] = {
	<?$i=0;foreach($choices as $choice):?>
	<?=$choice->getIdentifier()?>: { 
		matchMax	: <?=($choice->getAttributeValue('matchMax') == '') ? 0 : $choice->getAttributeValue('matchMax')?>,
		matchGroup	: <?=($choice->getAttributeValue('matchGroup')) ? (is_array($choice->getAttributeValue('matchGroup'))) ? json_encode($choice->getAttributeValue('matchGroup')) : '["'.$choice->getAttributeValue('matchGroup').'"]' : "[]"?>,
		current		: "0"
	}<?=($i<count($choices)-1)?',':''?>
<?$i++;endforeach?>
<?foreach($groups as $group):?>
	,<?=$group->getIdentifier()?>: { 
		shape 		: "<?=$group->getAttributeValue('shape')?>",
		coords		: "<?=$group->getAttributeValue('coords')?>",
		matchMax	: <?=($group->getAttributeValue('matchMax') == '') ? 0 : $group->getAttributeValue('matchMax')?>,
		matchGroup	: <?=($group->getAttributeValue('matchGroup')) ? (is_array($group->getAttributeValue('matchGroup'))) ? json_encode($group->getAttributeValue('matchGroup')) :'["'.$group->getAttributeValue('matchGroup').'"]' : "[]"?>
	}
<?endforeach?>
	}
</script>
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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
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
	qti_initParam["<?=$serial?>"]['hotspotChoice'] = {
	<?$i=0; foreach($choices as $choice):?>
		<?=$choice->getIdentifier()?>: { 
			shape : "<?=$choice->getAttributeValue('shape')?>",
			coords: "<?=$choice->getAttributeValue('coords')?>"
		}<?=($i<count($choices)-1)?',':''?>
	<?$i++;endforeach?>
	}
</script>

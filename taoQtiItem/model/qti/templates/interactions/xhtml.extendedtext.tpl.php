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
<div class="qti_widget qti_<?=$_type?>_interaction <?=$class?>">
	
	<?if(!empty($prompt)):?>
    	<p class="prompt"><?=$prompt?></p>
    <?endif?>

	<?=$data?>
	
	<?if(isset($options['maxStrings']) && ($response->getAttributeValue('cardinality') == 'multiple' || $response->getAttributeValue('cardinality') == 'ordered')):?>
		
		<div id="<?=$identifier?>">
		<?for($i = 0; $i < $options['maxStrings']; $i++):?>
			<input id="<?=$identifier?>_<?=$i?>" name="<?=$identifier?>_<?=$i?>" /><br />
		<?endfor?>
		</div>
		
	<?else:?>
		<textarea id="<?=$identifier?>" name="<?=$identifier?>" ></textarea>
	<?endif?>
	<script type="text/javascript">
		qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
		qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
		qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
		<?if(isset($options['responseBaseType'])):?>
		qti_initParam["<?=$serial?>"]['baseType'] = "<?=$options['responseBaseType']?>";
		<?endif?>
	</script>
</div>
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
	<div class="qti_<?=$_type?>_container">
		<?if(!empty($prompt)):?>
	    	<p class="prompt"><?=$prompt?></p>
	    <?endif?>
	<?=$data?>
	</div>
</div>	
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = {
		id 					: "<?=$identifier?>",
		type 				: "qti_<?=$_type?>_interaction",
		responseIdentifier 	: "<?=$options['responseIdentifier']?>",
		maxAssociations		: <?=$options['maxAssociations']?>,
		responseBaseType	: "<?=$options['responseBaseType']?>",
		matchMaxes			: {
		<?$i=0;foreach($choices as $choice):?>
			<?=$choice->getIdentifier()?>: { 
				matchMax	: <?=($choice->getAttributeValue('matchMax') == '') ? 0 : $choice->getAttributeValue('matchMax')?>,
				matchGroup	: <?=($choice->getAttributeValue('matchGroup')) ? (is_array($choice->getAttributeValue('matchGroup'))) ? json_encode($choice->getAttributeValue('matchGroup')) : '["'.$choice->getAttributeValue('matchGroup').'"]' : "[]"?>,
				current		: "0"
			}<?=($i<count($choices)-1)?',':''?>
		<?$i++;endforeach?>
		}
	};
</script>
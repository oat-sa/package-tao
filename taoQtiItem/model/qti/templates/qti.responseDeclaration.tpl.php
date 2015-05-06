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
$correctResponses = get_data('correctResponses');
$mapping = get_data('mapping');
$areaMapping = get_data('areaMapping');
?>
<responseDeclaration <?=get_data('attributes')?><?if(!$correctResponses && !$mapping && !$areaMapping):?>/>
<?else:?>>
    <?if(is_array($correctResponses) && count($correctResponses) > 0):?>
	<correctResponse>
	    <?foreach($correctResponses as $value):?>
	        <value><?=$value?></value>
	    <?endforeach?>
	</correctResponse>
    <?endif?>
    <?if(!is_null($mapping) && count($mapping) > 0):?>
	<mapping <?=get_data('mappingAttributes')?>>
	    <?foreach($mapping as $key => $value):?>
	        <mapEntry mapKey="<?=$key?>" mappedValue="<?=$value?>"/>
	    <?endforeach?>
	</mapping>
    <?endif?>
    <?if(!is_null($areaMapping) && count($areaMapping) > 0):?>
	<areaMapping <?=get_data('mappingAttributes')?>>
	    <?foreach($areaMapping as $areaMapEntry):?>
	        <areaMapEntry <?foreach($areaMapEntry as $key => $value):?><?=$key?>="<?=$value?>" <?endforeach;?> />
	    <?endforeach?>
	</areaMapping>
    <?endif?>
</responseDeclaration>
<?endif?>

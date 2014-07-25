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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<label for="rdftpl_mode_container_namespaces" class="form_desc"><?=__('Namespaces')?></label>
	<span class="form-elt-container"><?=__('Select')?> : 
		<a href="#" id="ns_filter_all" title="<?=__('All (the complete TAO Module)')?>" ><?=__('All')?></a>
		<a href="#" id="ns_filter_local" title="<?=__('Local Data (the local namespace containing only the data inserted by the users)')?>"><?=__('Local')?></a>
		<a href="#" id="ns_filter_none" title="<?=__('Unselect all')?>"><?=__('None')?></a>
		</span>
	<table class="form-elt-container">
		<tbody>
	<?foreach($namespaces as $ns):?>
			<tr>
				<td>
					<input 
						type="checkbox" 
						name="rdftpl_ns_<?=$ns->getModelId()?>"  
						id="rdftpl_ns_<?=$ns->getModelId()?>"
					<?if($localNs == $ns->getModelId()):?>
						class="rdftpl_ns rdftpl_ns_local" 
					<?else:?>
						class="rdftpl_ns" 
					<?endif?>
						
					/>
				</td>
				<td><?=(string)$ns?></td>
			</tr>
	<?endforeach?>
			
		</tbody>
	</table>

<script type="text/javascript">
$(document).ready(function(){

	$('#ns_filter_all').click(function(){
		$('.rdftpl_ns').attr('checked', 'checked');
	});
	$('#ns_filter_local').click(function(){
		$('.rdftpl_ns').removeAttr('checked');
		$('.rdftpl_ns_local').attr('checked', 'checked');
	});
	$('#ns_filter_none').click(function(){
		$('.rdftpl_ns').removeAttr('checked');
	});
});
</script>
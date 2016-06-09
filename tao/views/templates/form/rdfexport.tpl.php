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

<div id="rdftpl_mode_container_instances" class="rdftpl_mode_container">
	<?php foreach($instances as $uri => $label):?>
		<input type="checkbox" name="rdftpl_instance_<?=$uri?>" id="rdftpl_instance_<?=$uri?>" checked="checked" /><label for="rdftpl_instance_<?=$uri?>"><?=$label?></label><br />
	<?php endforeach?>
	<span class="checker-container">
		<a href="#" class="box-checker box-checker-uncheck" id="rdftpl_instance_checker"><?=__('Uncheck all')?></a>
	</span>
</div>

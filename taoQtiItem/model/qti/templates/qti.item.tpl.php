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

echo '<?xml version="1.0" encoding="UTF-8"?>'?>
<assessmentItem 
    <?php foreach(get_data('namespaces') as $name => $uri):?>
    <?php if($name):?>
    xmlns:<?=$name?>="<?=$uri?>" 
    <?php else:?>
    xmlns="<?=$uri?>"
    <?php endif;?>
    <?php endforeach;?>
    <?=$xsi?>schemaLocation="<?=get_data('schemaLocations')?>"
    <?=get_data('attributes')?>>

    <?=get_data('responses')?>

    <?=get_data('outcomes')?>

    <?=get_data('stylesheets')?>

    <itemBody<?php if(get_data('class')): ?> class="<?=get_data('class')?>"<?php endif;?>>
	<?=get_data('body')?>
    </itemBody>
    
    <?=get_data('renderedResponseProcessing')?>
    
    <?=get_data('feedbacks')?>
    
    <?=get_data('apipAccessibility')?>
</assessmentItem>

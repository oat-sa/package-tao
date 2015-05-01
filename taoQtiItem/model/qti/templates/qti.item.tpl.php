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
    xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    <?php foreach(get_data('namespaces') as $name => $uri):?>xmlns:<?=$name?>="<?=$uri?>"<?php endforeach?>
    xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1  http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd"
    <?=get_data('attributes')?>>

    <?=get_data('responses')?>

    <?=get_data('outcomes')?>

    <?=get_data('stylesheets')?>

    <itemBody>
	<?=get_data('body')?>
    </itemBody>
    
    <?=get_data('renderedResponseProcessing')?>
    
    <?=get_data('feedbacks')?>
</assessmentItem>
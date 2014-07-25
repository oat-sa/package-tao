<?php
/**
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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
?>
<manifest 
	xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" 
	xmlns:imsqti="http://www.imsglobal.org/xsd/imsqti_v2p1" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsqti_v2p1 imsqti_v2p1.xsd"
	identifier="<?php echo $manifestIdentifier; ?>"
>
    <organizations/>
    <resources>
        <?php foreach ($qtiItems as $qtiItem): ?>
        <resource identifier="<?php echo $qtiItem['identifier']; ?>" type="imsqti_item_xmlv2p1" href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $qtiItem['filePath']); ?>">
            <metadata>
                <schema>IMS QTI Item</schema>
                <schemaversion>2.1</schemaversion>
                <imsqti:qtiMetadata>
                    <imsqti:timeDependent><?php echo ($qtiItem['timeDependent']) ? 'true': 'false'; ?></imsqti:timeDependent>
                    <?php foreach ($qtiItem['interactions'] as $interaction):?>
                    <imsqti:interactionType><?php echo $interaction['type']; ?></imsqti:interactionType>
                     <?php endforeach ?>
                    <imsqti:feedbackType><?php echo ($qtiItem['adaptive']) ? 'adaptive' : 'nonadaptive'; ?></imsqti:feedbackType>
                    <imsqti:solutionAvailable>false</imsqti:solutionAvailable>
                    <imsqti:toolName><?php echo $qtiItem['toolName']; ?></imsqti:toolName>
                    <imsqti:toolVersion><?php echo $qtiItem['toolVersion']; ?></imsqti:toolVersion>
                    <imsqti:toolVendor>Open Assessment Technologies S.A.</imsqti:toolVendor>
                </imsqti:qtiMetadata>
            </metadata>
            <file href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $qtiItem['filePath']);?>"/>
            <?php foreach ($qtiItem['medias'] as $media):?>
            <file href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $media);?>"/>
            <?php endforeach ?>
        </resource>
        <?php endforeach ?>
    </resources>
</manifest>
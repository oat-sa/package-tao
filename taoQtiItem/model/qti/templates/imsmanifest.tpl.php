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
<manifest xmlns="http://www.imsglobal.org/xsd/imscp_v1p1"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/qtiv2p1_imscpv1p2_v1p0.xsd"
          identifier="<?php echo $manifestIdentifier; ?>">
    <metadata>
        <schema>QTIv2.1 Package</schema>
        <schemaversion>1.0.0</schemaversion>
    </metadata>
    <organizations/>
    <resources>
        <?php foreach ($qtiItems as $qtiItem): ?>
        <resource identifier="<?php echo $qtiItem['identifier']; ?>" type="imsqti_item_xmlv2p1" href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $qtiItem['filePath']); ?>">
            <file href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $qtiItem['filePath']);?>"/>
            <?php foreach ($qtiItem['medias'] as $media):?>
            <file href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $media);?>"/>
            <?php endforeach ?>
        </resource>
        <?php endforeach ?>
    </resources>
</manifest>
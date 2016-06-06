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
 * Copyright (c) 2013-2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
?>
<manifest xmlns="http://www.imsglobal.org/xsd/apip/apipv1p0/imscp_v1p1"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xmlns:lomm="http://ltsc.ieee.org/xsd/apipv1p0/LOM/manifest"
          xsi:schemaLocation="http://ltsc.ieee.org/xsd/apipv1p0/LOM/resource http://www.imsglobal.org/profile/apip/apipv1p0/apipv1p0_lomresourcev1p0_v1p0.xsd http://ltsc.ieee.org/xsd/apipv1p0/LOM/manifest http://www.imsglobal.org/profile/apip/apipv1p0/apipv1p0_lommanifestv1p0_v1p0.xsd http://www.imsglobal.org/xsd/apip/apipv1p0/qtimetadata/imsqti_v2p1 http://www.imsglobal.org/profile/apip/apipv1p0/apipv1p0_qtimetadatav2p1_v1p0.xsd http://www.imsglobal.org/xsd/apip/apipv1p0/imscp_v1p1 http://www.imsglobal.org/profile/apip/apipv1p0/apipv1p0_imscpv1p2_v1p0.xsd"
          identifier="<?php echo $manifestIdentifier; ?>">
    <metadata>
        <schema>APIP Package</schema>
        <schemaversion>1.0.0</schemaversion>
        <lomm:lom/>
    </metadata>
    <organizations/>
    <resources>
        <?php foreach ($qtiItems as $qtiItem): ?>
        <resource identifier="<?php echo $qtiItem['identifier']; ?>" type="imsqti_apipitem_xmlv2p1" href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $qtiItem['filePath']); ?>">
            <file href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $qtiItem['filePath']);?>"/>
            <?php foreach ($qtiItem['medias'] as $media):?>
            <file href="<?php echo str_replace(DIRECTORY_SEPARATOR, '/', $media);?>"/>
            <?php endforeach ?>
        </resource>
        <?php endforeach ?>
    </resources>
</manifest>
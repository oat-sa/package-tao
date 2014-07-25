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
<customInteraction responseIdentifier="graph1">

    <portableCustomInteraction customInteractionTypeIdentifier="IW30MX6U48JF9120GJS">

        <templateVariableMapping templateIdentifier="X" configurationProperty="areaX" />
        <templateVariableMapping templateIdentifier="Y" configurationProperty="areaY" />

        <responseSchema href="http://imsglobal.org/schema/json/v1.0/response.json"/>
        <resources location="http://imsglobal.org/pci/1.0.15/sharedLibraries.xml">
            <libraries>
                <lib id="/IMSGlobal/raphael_2_0" />
            </libraries>
        </resources>

        <properties>
            <entry key=”x”>10</entry>
            <entry key=”y”>30</entry>
            <entry key=”title”>Your Awesome ${title}</entry>
            <properties key="border">
                <entry key=”width”>path.to.value</entry>
                <entry key=”color”>method(‘value’)</entry>
            </properties>
        </properties>

        <markup>
            <div id="graph1" class="graph"></div>
        </markup>

    </portableCustomInteraction>

</customInteraction>
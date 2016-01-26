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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class taoResultServer_scripts_update_Updater extends \common_ext_ExtensionUpdater {

	/**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {
        
        $currentVersion = $initialVersion;
        
        if ($currentVersion == '2.6' 
            || $currentVersion == '2.6.1' 
            || $currentVersion == '2.7.0'
            || $currentVersion == '2.8'
            || $currentVersion == '2.8.1') {
        	$currentVersion = '2.8.2';
        }
        return $currentVersion;
    }
}
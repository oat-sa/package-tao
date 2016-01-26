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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy as FuncProxy;
use oat\tao\model\accessControl\data\implementation\FreeAccess;


$impl = new oat\tao\model\accessControl\func\implementation\SimpleAccess();

$exts = common_ext_ExtensionsManager::singleton()->getInstalledExtensions();
foreach ($exts as $extension) {
    foreach ($extension->getManifest()->getAclTable() as $tableEntry) {
        $rule = new AccessRule($tableEntry[0], $tableEntry[1], $tableEntry[2]);
        $impl->applyRule($rule);
    }
}
FuncProxy::setImplementation($impl);
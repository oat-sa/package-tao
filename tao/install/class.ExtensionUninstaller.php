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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;

/**
 * Specification of the Generis ExtensionInstaller class to add a new behavior:
 * the Modules and Actions in the Ontology at installation time.
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @since 2.4
 
 */
class tao_install_ExtensionUninstaller
    extends common_ext_ExtensionUninstaller
{
    
    /**
     * Uninstall all access rules
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return void
     * @since 2.4
     */
    public function extendedUninstall() {
        foreach ($this->extension->getManifest()->getAclTable() as $tableEntry) {
            $rule = new AccessRule($tableEntry[0], $tableEntry[1], $tableEntry[2]);
            AclProxy::revokeRule($rule);
        }
    }

}
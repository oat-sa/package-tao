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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * mother class for access operations
 *
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 
 */
class funcAcl_models_classes_AccessService extends tao_models_classes_GenerisService
{
    const FUNCACL_NS = 'http://www.tao.lu/Ontologies/taoFuncACL.rdf';

    const PROPERTY_ACL_GRANTACCESS = 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#GrantAccess';
    
    public function grantExtensionAccess(core_kernel_classes_Resource $role, $ext) {
        $accessUri = $this->makeEMAUri($ext);
        funcAcl_models_classes_ExtensionAccessService::singleton()->add($role->getUri(), $accessUri);
    }

    public function grantModuleAccess(core_kernel_classes_Resource $role, $ext, $mod) {
        $accessUri = $this->makeEMAUri($ext, $mod);
        funcAcl_models_classes_ModuleAccessService::singleton()->add($role->getUri(), $accessUri);
    }
    
    public function grantActionAccess(core_kernel_classes_Resource $role, $ext, $mod, $act) {
        $accessUri = $this->makeEMAUri($ext, $mod, $act);
        funcAcl_models_classes_ActionAccessService::singleton()->add($role->getUri(), $accessUri);
    }

    public function revokeExtensionAccess(core_kernel_classes_Resource $role, $ext) {
        $accessUri = $this->makeEMAUri($ext);
        funcAcl_models_classes_ExtensionAccessService::singleton()->remove($role->getUri(), $accessUri);
    }
    
    public function revokeModuleAccess(core_kernel_classes_Resource $role, $ext, $mod) {
        $accessUri = $this->makeEMAUri($ext, $mod);
        funcAcl_models_classes_ModuleAccessService::singleton()->remove($role->getUri(), $accessUri);
    }
    
    public function revokeActionAccess(core_kernel_classes_Resource $role, $ext, $mod, $act) {
        $accessUri = $this->makeEMAUri($ext, $mod, $act);
        funcAcl_models_classes_ActionAccessService::singleton()->remove($role->getUri(), $accessUri);
    }
    
    /**
     * Short description of method makeEMAUri
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param string ext
     * @param string mod
     * @param string act
     * @return string
     */
    public function makeEMAUri($ext, $mod = null, $act = null)
    {
        $returnValue = (string) '';
        
        $returnValue = self::FUNCACL_NS . '#';
        if (! is_null($act)) {
            $type = 'a';
        } else {
            if (! is_null($mod)) {
                $type = 'm';
            } else {
                $type = 'e';
            }
        }
        $returnValue .= $type . '_' . $ext;
        if (! is_null($mod)) {
            $returnValue .= '_' . $mod;
        }
        if (! is_null($act)) {
            $returnValue .= '_' . $act;
        }
        return (string) $returnValue;
    }
}

?>
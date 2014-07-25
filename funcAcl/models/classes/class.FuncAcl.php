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
 */

/**
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class funcAcl_models_classes_FuncAcl
    implements tao_models_classes_accessControl_AccessControl
{
    public function __construct() {
        common_ext_ExtensionsManager::singleton()->getExtensionById('funcAcl');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_accessControl_AccessControl::hasAccess()
     */
    public function hasAccess($action, $controller, $extension, $parameters =array()) {
        return funcAcl_helpers_funcACL::hasAccess($action, $controller, $extension);
    }
    
    public function applyRule(tao_models_classes_accessControl_AccessRule $rule) {
        $filter = $rule->getMask();
        if ($rule->isGrant()) {
            $accessService = funcAcl_models_classes_AccessService::singleton();
            if (isset($filter['ext'])) {
                // verify model has been created
                $extensionModel = new core_kernel_classes_Resource($accessService->makeEMAUri($filter['ext']));
                if (!$extensionModel->exists()) {
                    $extension = common_ext_ExtensionsManager::singleton()->getExtensionById($filter['ext']);
                    funcAcl_helpers_Model::spawnExtensionModel($extension);
                }
            }
            if (isset($filter['act']) && isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->grantActionAccess($rule->getRole(), $filter['ext'], $filter['mod'], $filter['act']);
            } elseif (isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->grantModuleAccess($rule->getRole(), $filter['ext'], $filter['mod']);
            } elseif (isset($filter['ext'])) {
                $accessService->grantExtensionAccess($rule->getRole(), $filter['ext']);
            } else {
                common_Logger::w('Uninterpretable filter in '.__CLASS__);
            }
        } else {
            common_Logger::w('Only grant rules accepted in '.__CLASS__);
        }
    }
    
    public function revokeRule(tao_models_classes_accessControl_AccessRule $rule) {
        if ($rule->isGrant()) {
            $accessService = funcAcl_models_classes_AccessService::singleton();
            $filter = $rule->getMask();
            if (isset($filter['act']) && isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->revokeActionAccess($rule->getRole(), $filter['ext'], $filter['mod'], $filter['act']);
            } elseif (isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->revokeModuleAccess($rule->getRole(), $filter['ext'], $filter['mod']);
            } elseif (isset($filter['ext'])) {
                $accessService->revokeExtensionAccess($rule->getRole(), $filter['ext']);
            } else {
                common_Logger::w('Uninterpretable filter in '.__CLASS__);
            }
        } else {
            common_Logger::w('Only grant rules accepted in '.__CLASS__);
        }
    }
}
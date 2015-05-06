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
 * 
 * @author joel.bout, <joel@taotesting.com>
 *
 */
class taoLti_models_classes_LtiUtils
{

    const LIS_CONTEXT_ROLE_NAMESPACE = 'urn:lti:role:ims/lis/';

    /**
     * Maps a fuly qualified or abbreviated lti role
     * to an existing tao role
     * 
     * @param string $role
     * @throws common_Exception
     * @throws common_exception_Error
     * @return core_kernel_classes_Resource the tao role or null
     */
    public static function mapLTIRole2TaoRole($role)
    {
        $taoRole = null;
        if (filter_var($role, FILTER_VALIDATE_URL)) {
            // url found
            $taoRole = new core_kernel_classes_Resource($role);
        } else {
            // if not fully qualified prepend LIS context role NS
            if (strtolower(substr($role, 0, 4)) !== 'urn:') {
                $role = self::LIS_CONTEXT_ROLE_NAMESPACE . $role;
            }
            list ($prefix, $nid, $nss) = explode(':', $role, 3);
            if ($nid != 'lti') {
                common_Logger::w('Non LTI URN ' . $role . ' passed via LTI');
            }
            $urn = 'urn:' . strtolower($nid) . ':' . $nss;
            
            // search for fitting role
            $class = new core_kernel_classes_Class(CLASS_LTI_ROLES);
            $cand = $class->searchInstances(array(
                PROPERTY_LTI_ROLES_URN => $urn
            ));
            if (count($cand) > 1) {
                throw new common_exception_Error('Multiple instances share the URN ' . $urn);
            }
            if (count($cand) == 1) {
                $taoRole = current($cand);
            } else {
                common_Logger::w('Unknown LTI role with urn: ' . $urn);
            }
        }
        if (! is_null($taoRole) && $taoRole->exists()) {
            return $taoRole->getUri();
        } else {
            return null;
        }
    }
    
    /**
     * Adds the LTI roles to the tao roles
     * 
     * @param string $roleUri
     * @return array
     */
    public static function mapTaoRole2LTIRoles($roleUri)
    {
        $roles = array($roleUri);
        if ($roleUri == 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole') {
            $roles[] = 'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership#Learner';
        }
        return $roles;
    }

    /**
     * Returns the tao language code that corresponds to the code provided
     * not yet implemented, will always use default
     * 
     * @param string $code
     * @return string
     */
    public static function mapCode2InterfaceLanguage($code)
    {
        $returnValue = DEFAULT_LANG;
        return $returnValue;
    }
}
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_actions_form_Role
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_actions_form_Role
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    public function initElements()
    {
        
        parent::initElements();
        
        $encodedIncludesRolePropertyUri = tao_helpers_Uri::encode(PROPERTY_ROLE_INCLUDESROLE);
        $encodedInstanceUri = tao_helpers_Uri::encode($this->getInstance()->getUri());
        $rolesElement = $this->form->getElement($encodedIncludesRolePropertyUri);
        if (!is_null($rolesElement)) {
	        $rolesOptions = $rolesElement->getOptions();
	        
	        // remove the role itself in the list of includable roles
	        // to avoid cyclic inclusions (even if the system supports it).
	        if (array_key_exists($encodedInstanceUri, $rolesOptions)){
	        	unset($rolesOptions[$encodedInstanceUri]);
	        }
	        
	        $rolesElement->setOptions($rolesOptions);
        }
        
    }

    /**
     * Short description of method getTopClazz
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Class
     * @see tao_actions_form_Generis::getTopClazz()
     */
    public function getTopClazz()
    {
        $returnValue = null;

        
        $returnValue = new core_kernel_classes_Class(CLASS_ROLE);
        

        return $returnValue;
    }

}

?>
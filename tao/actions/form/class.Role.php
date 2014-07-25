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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/actions/form/class.Role.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.02.2013, 18:27:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 10-13-1-85--332f5c64:13cafb9ead5:-8000:0000000000003C79-includes begin
// section 10-13-1-85--332f5c64:13cafb9ead5:-8000:0000000000003C79-includes end

/* user defined constants */
// section 10-13-1-85--332f5c64:13cafb9ead5:-8000:0000000000003C79-constants begin
// section 10-13-1-85--332f5c64:13cafb9ead5:-8000:0000000000003C79-constants end

/**
 * Short description of class tao_actions_form_Role
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage actions_form
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
        // section 10-13-1-85--332f5c64:13cafb9ead5:-8000:0000000000003C7C begin
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
        // section 10-13-1-85--332f5c64:13cafb9ead5:-8000:0000000000003C7C end
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

        // section 10-30-1--78-5c081e8a:13cdee282e5:-8000:0000000000003C84 begin
        $returnValue = new core_kernel_classes_Class(CLASS_ROLE);
        // section 10-30-1--78-5c081e8a:13cdee282e5:-8000:0000000000003C84 end

        return $returnValue;
    }

} /* end of class tao_actions_form_Role */

?>
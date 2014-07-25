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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Manage the roles for the user workflow
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user roles management
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.RoleService.php');

/* user defined includes */
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-includes begin
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-includes end

/* user defined constants */
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-constants begin
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-constants end

/**
 * Manage the roles for the user workflow
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_RoleService
    extends tao_models_classes_RoleService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * initialize the roles of the service
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initRole()
    {
        // section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8D begin
        
    	$this->roleClass = new core_kernel_classes_Class(CLASS_ROLE);
    	
        // section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8D end
    }

} /* end of class wfEngine_models_classes_RoleService */

?>
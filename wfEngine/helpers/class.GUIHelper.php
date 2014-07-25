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
 * TAO - wfEngine/helpers/class.GUIHelper.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 14.09.2011, 11:01:34 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-bcacefe:1326704aac0:-8000:0000000000003016-includes begin
// section 127-0-1-1-bcacefe:1326704aac0:-8000:0000000000003016-includes end

/* user defined constants */
// section 127-0-1-1-bcacefe:1326704aac0:-8000:0000000000003016-constants begin
// section 127-0-1-1-bcacefe:1326704aac0:-8000:0000000000003016-constants end

/**
 * Short description of class wfEngine_helpers_GUIHelper
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers
 */
class wfEngine_helpers_GUIHelper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method sanitizeGenerisString
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string string
     * @return string
     */
    public static function sanitizeGenerisString($string)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-bcacefe:1326704aac0:-8000:0000000000003017 begin
		$returnValue = str_replace(array('&nbsp;'), ' ', $string);
        // section 127-0-1-1-bcacefe:1326704aac0:-8000:0000000000003017 end

        return (string) $returnValue;
    }

    /**
     * Short description of method buildStatusImageURI
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource status
     * @return string
     */
    public static function buildStatusImageURI( core_kernel_classes_Resource $status)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-bcacefe:1326704aac0:-8000:000000000000301A begin
		
		$baseURI = 'img/status_';
		$statusName = '';
		
		switch ($status->getUri()){
			case INSTANCE_PROCESSSTATUS_PAUSED:
				$statusName = 'paused';
				break;
			case INSTANCE_PROCESSSTATUS_RESUMED:
				$statusName = 'resumed';
				break;
			case INSTANCE_PROCESSSTATUS_FINISHED:
				$statusName = 'finished';
				break;
			case INSTANCE_PROCESSSTATUS_STARTED:
				$statusName = 'started';
				break;
			case INSTANCE_PROCESSSTATUS_CLOSED:
				$statusName = 'closed';
				break;
			case INSTANCE_PROCESSSTATUS_STOPPED:
				$statusName = 'stopped';
				break;
		}
		
		$returnValue = $baseURI.$statusName.'.png';
		
        // section 127-0-1-1-bcacefe:1326704aac0:-8000:000000000000301A end

        return (string) $returnValue;
    }

} /* end of class wfEngine_helpers_GUIHelper */

?>
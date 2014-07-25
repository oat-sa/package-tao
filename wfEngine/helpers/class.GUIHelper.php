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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class wfEngine_helpers_GUIHelper
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
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

        
		$returnValue = str_replace(array('&nbsp;'), ' ', $string);
        

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
		
        

        return (string) $returnValue;
    }

} /* end of class wfEngine_helpers_GUIHelper */

?>
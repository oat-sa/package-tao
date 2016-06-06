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

/**
 * Short description of class core_kernel_classes_ContainerComparator
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package generis
 
 */
class core_kernel_classes_ContainerComparator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method compare
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Container o1
     * @param  Container o2
     * @return int
     */
    public static function compare( core_kernel_classes_Container $o1,  core_kernel_classes_Container $o2)
    {
        $returnValue = (int) 0;

        
        if($o1 instanceof core_kernel_classes_Literal && $o2 instanceof core_kernel_classes_Literal) {
        	$returnValue = strcasecmp($o1->literal,$o2->literal);
        }
        else if($o1 instanceof core_kernel_classes_Resource && $o2 instanceof core_kernel_classes_Resource) {
        	$returnValue = strcasecmp($o1->getUri(),$o2->getUri());
        }
        else {
           	throw new common_Exception('try to compared not implemented type');
        }
        

        return (int) $returnValue;
    }

}
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\exception;

use oat\taoQtiItem\model\qti\exception\ExtractException;
use \common_Exception;
use \common_exception_UserReadableException;

/**
 * Exception during QTI package extraction.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class ExtractException
    extends common_Exception implements common_exception_UserReadableException
{
    /**
     * Returns a human-readable message describing the error that occured.
     *
     * @return string
     */
    public function getUserMessage() {
        return __('An error occured while extracting the ZIP archive representing the IMS Content Package.');
    }
} 

?>
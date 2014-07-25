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
 *
 */
/**
 * Generis Object Oriented API - common/exception/class.InvalidArgumentType.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 30.01.2012, 16:44:05 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 */

/**
 * Missing Parameter are thrown for missing parameters that are not strongly passed (sub protocols)
 * @access public
 * @author Patrick Plichart
 * @package generis
 
 */
class common_exception_MissingParameter
    extends common_exception_BadRequest
{
    // --- ASSOCIATIONS ---
    // --- ATTRIBUTES ---
    // --- OPERATIONS ---
    /**
     * Short description of method __construct
     *
     * @access public
     * @author patrick
     * @param  string class
     * @param  string function
     * @param  int position
     * @param  string expectedType
     * @param  object
     * @return mixed
     */
    public function __construct($parameterName = "",  $service = "")
    {
        
	$message = 'Expected parameter "'.$parameterName.'" passed to '.$service.' is missing';
        parent::__construct($message);

        
    }
    public function getUserMessage() {
	return __("At least one mandatory parameter was required but found missing in your request");
    }
    
} /* end of class common_exception_InvalidArgumentType */

?>
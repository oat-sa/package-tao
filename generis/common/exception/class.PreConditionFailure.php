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
 * @author patrick,
 * @package generis
 
 */



/* user defined includes */



/* user defined constants */



/**
 * a useful exception
 * @access public
 * @author Patrick Plichart
 * @package generis
 
 */
class common_exception_PreConditionFailure
    extends common_exception_ClientException
{
  
       public function getUserMessage() {
	return __("One of the precondition for this type of request was not satisfied");
    }
} 
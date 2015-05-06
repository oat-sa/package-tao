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
 * Generis Object Oriented API - common\class.Exception.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author lionel.lecaque@tudor.lu
 * @package generis
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class common_Exception extends Exception{
	
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code);
        common_Logger::singleton()->handleException($this);
    }
	
	public function __toString()
    {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n
                                    {$this->getTraceAsString()}";
    }
	
}
?>
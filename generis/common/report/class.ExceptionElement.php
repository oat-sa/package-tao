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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * An element of a report representing an exception that occured
 * These elements are interpreted as errors 
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package common
 * @subpackage report
 */
class common_report_ExceptionElement extends common_report_ErrorElement
{
    /**
     * @var common_exception_UserReadableException
     */
    private $exception;
    
    /**
     * 
     * Enter description here ...
     * @param common_exception_UserReadableException $exception
     */
    public function __construct($exception) {
        parent::__construct('');
        $this->exception = $exception;
    }
    
    public function __toString() {
        return $this->exception->getUserMessage();
    }
}
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
 * The Report allows to return a more detailed return value
 * then a simple boolean variable denoting the success
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package common
 * @subpackage report
 */
class common_report_Report
{
    /**
     * title of the report
     * @var string
     */
	private $title;

	/**
	 * elements of the report
	 * @see common_report_ReportElement
	 * @var array
	 */
	private $elements;
	
	/**
	 * convenience methode to create a simple success report
	 * 
	 * @param string $title
	 * @param mixed $data
	 * @return common_report_Report
	 */
	public static function createSuccess($title = '', $data = null) {
	    common_Logger::i($title);
		$report = new static($title);
	    $successElement = new common_report_SuccessElement($title, $data);
	    $report->add($successElement);
		return $report;
	}
	
	/**
	 * convenience methode to create a simple failure report
	 * 
	 * @param string $title
	 * @param mixed $errors
	 * @return common_report_Report
	 */
	public static function createFailure($title, $errors = array()) {
	    if (strlen($title) > 0) {
		    common_Logger::w($title);
	    }
	    
	    if ($title == '' && empty($errors)) {
	        throw new common_Exception('Cannot create failure report without error');
	    }
	    
		if (!empty($errors)) {
		    $report = new static($title);
		    $report->add($errors);
		} else {
		    $report = new static();
		    $report->add(new common_report_ErrorElement($title));
		}
		return $report;
	}
	
	public function __construct($title = '') {
	    $this->elements = array();
		$this->title = $title;
	}
	
	/**
	 * Change the title of the report
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * returns the tile of the report
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * returns all success elements
	 * @return array
	 */
	public function getSuccesses() {
        $successes = array();
		foreach ($this->elements as $element) {
		    if ($element instanceof common_report_SuccessElement) {
		        $successes[] = $element;
		    }
		}
		return $successes;
	}
	
	/**
	 * returns all error elements
	 * @return array
	 */
	public function getErrors() {
        $errors = array();
		foreach ($this->elements as $element) {
		    if ($element instanceof common_report_ErrorElement) {
		        $errors[] = $element;
		    }
		}
		return $errors;
	}
	
	/**
	 * Whenever or not teh report contains errors
	 * @return boolean
	 */
    public function containsError() {
	    $found = false;
		foreach ($this->elements as $element) {
		    if ($element instanceof common_report_ErrorElement) {
		        $found = true;
		        break;
		    }
		}
		return $found;
    }
    
	/**
	 * Whenever or not teh report contains successes
	 * @return boolean
	 */
    public function containsSuccess() {
	    $found = false;
		foreach ($this->elements as $element) {
		    if ($element instanceof common_report_SuccessElement) {
		        $found = true;
		        break;
		    }
		}
		return $found;
	}
	
	/**
	 * Add something to the report
	 * @param mixed $mixed accepts Arrays, Reports, ReportElements and Exceptions
	 */
	public function add($mixed) {
	    $mixedArray = is_array($mixed) ? $mixed : array($mixed);
		foreach ($mixedArray as $element) {
		    if ($element instanceof common_report_ReportElement) {
		        $this->elements[] = $element;
		    } elseif ($element instanceof common_report_Report) {
    		    foreach ($element->elements as $subElement) {
        	        $this->add($subElement);
        	    }
		    } elseif ($element instanceof common_exception_UserReadableException) {
		        $this->elements[] = new common_report_ExceptionElement($element);
		    } else {
		        throw new common_exception_Error('Tried to add '.(is_object($element) ? get_class($element) : gettype($element)).' to report');
		    }
		}
	}

}
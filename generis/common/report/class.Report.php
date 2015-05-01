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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

if (!interface_exists('JsonSerializable')) {
    // for php < 5.4
    eval('interface JsonSerializable {}');
}
/**
 * The Report allows to return a more detailed return value
 * then a simple boolean variable denoting the success
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package generis
 */
class common_report_Report implements IteratorAggregate, JsonSerializable
{
    const TYPE_SUCCESS = 1;
    
    const TYPE_INFO = 2;
    
    const TYPE_WARNING = 4;
    
    const TYPE_ERROR = 8;
    
    /**
     * type of the report
     * @var int
     */
	private $type;
	
    /**
     * message of the report
     * @var string
     */
	private $message;

	/**
	 * elements of the report
	 * @var array
	 */
	private $elements;
	
	/**
	 * Attached data
	 * @var mixed
	 */
    private $data = null;
	
	/**
	 * convenience methode to create a simple success report
	 * 
	 * @param string $title
	 * @param mixed $data
	 * @return common_report_Report
	 */
	public static function createSuccess($message = '', $data = null) {
	    return new static(self::TYPE_SUCCESS, $message, $data);
	}
	
	/**
	 * convenience methode to create a simple failure report
	 * 
	 * @param string $title
	 * @param mixed $errors
	 * @return common_report_Report
	 */
	public static function createFailure($message, $errors = array()) {
	    $report = new static(self::TYPE_ERROR, $message);
	    foreach ($errors as $error) {
	        $report->add($error);
	    }
	    return $report;
	}
	
	public function __construct($type, $message = '', $data = null) {
	    $this->type = $type;
		$this->message = $message;
	    $this->elements = array();
	    $this->data = $data;
	}
	
	/**
	 * Change the title of the report
	 * @deprecated
	 * @param string $title
	 */
	public function setTitle($message) {
		$this->setMessage($message);
	}
	
	/**
	 * please use getMessage instead
	 * 
	 * @deprecated
	 * @return string
	 */
	public function getTitle() {
		return $this->getMessage();
	}
	
	/**
	 * Change the message
	 * 
	 * @param string $title
	 */
	public function setMessage($message) {
	    $this->message = $message;
	}
	
	/**
	 * Get report message
	 * 
	 * @return string
	 */
	public function getMessage() {
	    return $this->message;
	}
	
	/**
	 * change the type of the report
	 * 
	 * @return int
	 */
	public function setType($type) {
	    $this->type = $type;
	}
	
	/**
	 * returns the type of the report
	 * @return int
	 */
	public function getType() {
	    return $this->type;
	}
	
	public function getData() {
	    return $this->data;
	}
	
	public function setData($data = null) {
	    $this->data = $data;
	}
	
	/**
	 * returns all success elements
	 * @return array
	 * @deprecated
	 */
	public function getSuccesses() {
        $successes = array();
		foreach ($this as $element) {
		    if ($element->getType() == self::TYPE_SUCCESS) {
		        $successes[] = $element;
		    }
		}
		return $successes;
	}
	
	/**
	 * returns all error elements
	 * @return array
	 * @deprecated
	 */
	public function getErrors() {
        $errors = array();
		foreach ($this as $element) {
    		if ($element->getType() == self::TYPE_ERROR) {
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
	    return $this->contains(self::TYPE_ERROR);
    }
    
	/**
	 * Whenever or not teh report contains successes
	 * @return boolean
	 */
    public function containsSuccess() {
	    return $this->contains(self::TYPE_SUCCESS);
	}
	
	/**
	 * Whenever or not the type can be found in the report
	 * 
	 * @param int $type
	 * @return boolean
	 */
	public function contains($type) {
	    foreach ($this as $child) {
	        if ($child->getType() == $type || $child->contains($type)) {
	            return true;
	        }
	    }
	    return false;
	}
	
	
	/**
	 * Add something to the report
	 * @param mixed $mixed accepts Arrays, Reports, ReportElements and Exceptions
	 */
	public function add($mixed) {
	    $mixedArray = is_array($mixed) ? $mixed : array($mixed);
		foreach ($mixedArray as $element) {
		    if ($element instanceof common_report_Report) {
		        $this->elements[] = $element;
		    } elseif ($element instanceof common_exception_UserReadableException) {
		        $this->elements[] = new static(self::TYPE_ERROR, $element->getUserMessage());
		    } else {
		        throw new common_exception_Error('Tried to add '.(is_object($element) ? get_class($element) : gettype($element)).' to report');
		    }
		}
	}
	
	/**
	 * Returns an iterator over the children 
	 * 
	 * @return ArrayIterator
	 */
	public function getIterator() {
	    return new ArrayIterator($this->elements);
	}
	
	/**
	 * Whenever or not there are child reports
	 * 
	 * @return boolean
	 */
	public function hasChildren() {
	    return count($this->elements) > 0;
	}

	/**
	 * user feedback message
	 * 
	 * @return string
	 */
	public function __toString() {
	    return $this->message;
	}
	
	public function JsonSerialize()
	{
	    switch ($this->getType()) {
	    
	    	case common_report_Report::TYPE_SUCCESS:
	    	    $type = 'success';
	    	    break;
	    
	    	case common_report_Report::TYPE_WARNING:
	    	    $type = 'warning';
	    	    break;
	    
	    	case common_report_Report::TYPE_ERROR:
	    	    $type = 'error';
	    	    break;
	    
	    	default:
	    	    $type = 'info';
	    	    break;
	    }
	    return array(
	        'type'      => $type,
	        'message'    => $this->message,
	        'data'      => $this->data,
	        'children'  => $this->elements
	    );
	}
	
}
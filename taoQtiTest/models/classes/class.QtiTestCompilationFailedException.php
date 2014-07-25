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
 * 
 */

/**
 * The User Readable exception to be used when an error occurs
 * at QTI Test compilation-time.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiTest
 
 */
class taoQtiTest_models_classes_QtiTestCompilationFailedException extends tao_models_classes_CompilationFailedException implements common_exception_UserReadableException {
	
    /**
     * Error code to use when the error is unknown.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Error code to use when no items are composing
     * the test to be compiled.
     * 
     * @var integer
     */
    const NO_ITEMS = 1;
    
    /**
     * Error code to use when a remote resource (e.g. image
     * referenced with absolute URL) cannot be retrieved.
     * 
     * @var integer
     */
    const REMOTE_RESOURCE = 2;
    
    /**
     * Error code to use when a dependent item failed to
     * be compiled.
     * 
     * @var integer
     */
    const ITEM_COMPILATION = 3;
    
    /**
     * The resource in database describing the test that failed
     * to be compiled.
     * 
     * @var core_kernel_classes_Resource
     */
    private $test;
    
	/**
	 * Create a new QtiTestCompilationFailedException object.
	 * 
	 * @param string $message A technical message to developers.
	 * @param core_kernel_classes_Resource A Resource object in database describing the test that failed to compiled.
	 * @param integer $code A code to explicitely identify the nature of the error.
	 */
	public function __construct($message, core_kernel_classes_Resource $test, $code = 0) {
		parent::__construct($message, $code);
		$this->setTest($test);
	}
	
	/**
	 * Get the resource in database describing the test that failed to
	 * be compiled.
	 * 
	 * @return core_kernel_classes_Resource A core_kernel_classes_Resource object.
	 */
	protected function getTest() {
	    return $this->test;
	}
	
	/**
	 * Set the resource in database describing the test that failed
	 * to be compiled.
	 * 
	 * @param core_kernel_classes_Resource $test a core_kernel_classes_Resource object.
	 */
	protected function setTest(core_kernel_classes_Resource $test) {
	    $this->test = $test;
	}
	
	/**
	 * Returns a translated human-readable message destinated to the end-user. The content
	 * of the message will depend on the $code given at instantiation-time.
	 *
	 * @return string A human-readable message.
	 */
	public function getUserMessage() {
	    $testLabel = $this->getTest()->getLabel();
	    
		switch ($this->getCode()) {
			case self::UNKNOWN:
				return sprintf(__("An unknown error occured while compiled QTI Test '%s'."), $testLabel);
		    break;
			
			case self::NO_ITEMS:
		        return sprintf(__("The QTI Test '%s' to be compiled must contain at least 1 QTI Item. None found."), $testLabel);
			break;
			
			case self::REMOTE_RESOURCE:
			    return sprintf(__("A remote resource referenced in QTI test '%s' could not be retrieved.", $testLabel));
			break;
			
			case self::ITEM_COMPILATION:
			    return sprintf(__("A QTI Item involved in the QTI Test '%s' could not be compiled.", $testLabel));
			break;
			
			default:
				return sprintf(__("An unknown error occured while compiling QTI test '%s'."), $testLabel);
			break;
		}
	}
}
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

use qtism\runtime\tests\AbstractAssessmentTestSessionFactory;
use qtism\runtime\storage\binary\BinaryStream;
use qtism\runtime\storage\binary\AbstractQtiBinaryStorage;
use qtism\data\Document;
use qtism\data\AssessmentTest;
use qtism\runtime\tests\AssessmentTestSession;

/**
 * A QtiSm AssessmentTestSession Storage Service implementation for TAO.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_helpers_TestSessionStorage extends AbstractQtiBinaryStorage {

   /**
    * The ServiceModule object which provides an access to the TAO Delivery Storage.
    * 
    * @var tao_actions_ServiceModule
    */
   private $serviceModule = null; 
    
   public function __construct(AbstractAssessmentTestSessionFactory $factory, taoQtiTest_actions_TestRunner $serviceModule) {
       parent::__construct($factory);
       $this->setServiceModule($serviceModule);
   }
   
   /**
    * Get the ServiceModule object which provides an access to the TAO Delivery Storage.
    * 
    * @return taoQtiTest_actions_TestRunner
    */
   protected function getServiceModule() {
       return $this->serviceModule;
   }
   
   /**
    * Set the ServiceModule object which provides an access to the TAO Delivery Storage.
    * 
    * @param taoQtiTest_actions_TestRunner $serviceModule
    */
   protected function setServiceModule(taoQtiTest_actions_TestRunner $serviceModule) {
       $this->serviceModule = $serviceModule;
   }
    
   protected function getRetrievalStream(Document $assessmentTest, $sessionId) {
    
       $reflectionObject = new ReflectionObject($this->getServiceModule());
       $getStateMethod = $reflectionObject->getMethod('getState');
       $getStateMethod->setAccessible(true);
       $data = $getStateMethod->invoke($this->getServiceModule());
       
       // Read 28 chars (the session ID) in order to position the file pointer correctly
       // if something is inside the state data.
       $stateEmpty = (empty($data) === true);
       $stream = new BinaryStream(($stateEmpty === true) ? '' : $data);
       $stream->open();
       
       if ($stateEmpty === false) {
           // Consume additional sessionID (plain string).
           $stream->read(28);
           
           // Consume additional error (short signed integer).
           $stream->read(2);
       }
       
       $stream->close();
       return $stream;
   }
   
   protected function persistStream(AssessmentTestSession $assessmentTestSession, BinaryStream $stream) {
       $reflectionObject = new ReflectionObject($this->getServiceModule());
       $setStateMethod = $reflectionObject->getMethod('setState');
       $getCurrentErrorMethod = $reflectionObject->getMethod('getCurrentError');
       $setStateMethod->setAccessible(true);
       $getCurrentErrorMethod->setAccessible(true);
       $data =  $assessmentTestSession->getSessionId() . pack('s', $getCurrentErrorMethod->invoke($this->getServiceModule())) . $stream->getBinary();
       $setStateMethod->invoke($this->getServiceModule(), $data);
   }
}
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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use qtism\common\storage\IStream;
use qtism\runtime\tests\AbstractSessionManager;
use qtism\common\storage\MemoryStream;
use qtism\runtime\storage\binary\BinaryAssessmentTestSeeker;
use qtism\runtime\storage\binary\AbstractQtiBinaryStorage;
use qtism\runtime\storage\common\StorageException;
use qtism\data\AssessmentTest;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\storage\binary\QtiBinaryStreamAccessFsFile;

/**
 * A QtiSm AssessmentTestSession Storage Service implementation for TAO.
 * 
 * It is able to retrieve test sessions related to a given user and a given
 * test definition.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_helpers_TestSessionStorage extends AbstractQtiBinaryStorage {
   
   /**
    * The last recorded error.
    * 
    * @var integer
    */
   private $lastError = -1;
   
   /**
    * The URI (Uniform Resource Identifier) of the user the Test Session belongs to.
    * 
    * @var string
    */
   private $userUri;
    
   /**
    * Create a new TestSessionStorage object.
    * 
    * @param AbstractSessionManager $manager The session manager to be used to create new AssessmentTestSession and AssessmentItemSession objects.
    * @param BinaryAssessmentTestSeeker $seeker The seeker making able the storage engine to index AssessmentTest's components.
    * @param string $userUri The URI (Uniform Resource Identifier) of the user the Test Session belongs to.
    */
   public function __construct(AbstractSessionManager $manager, BinaryAssessmentTestSeeker $seeker, $userUri) {
       parent::__construct($manager, $seeker);
       $this->setUserUri($userUri);
   }
   
   /**
    * Get the last retrieved error. -1 means
    * no error.
    * 
    * @return integer
    */
   public function getLastError() {
       return $this->lastError;
   }
   
   /**
    * Set the last retrieved error. -1 means
    * no error.
    * 
    * @param integer $lastError
    */
   public function setLastError($lastError) {
       $this->lastError = $lastError;
   }
   
   /**
    * Get the URI (Uniform Resource Identifier) of the user the Test Session belongs to.
    * 
    * @return string
    */
   public function getUserUri() {
       return $this->userUri;
   }
   
   /**
    * Set the URI (Uniform Resource Identifier) of the user the Test Session belongs to.
    * 
    * @param string $userUri
    */
   public function setUserUri($userUri) {
       $this->userUri = $userUri;
   }
   
   public function retrieve(AssessmentTest $test, $sessionId) {
       $this->setLastError(-1);
       
       return parent::retrieve($test, $sessionId);
   }
   
   protected function getRetrievalStream($sessionId) {
    
       $storageService = tao_models_classes_service_StateStorage::singleton();
       $userUri = $this->getUserUri();
       
       if (is_null($userUri) === true) {
           $msg = "Could not retrieve current user URI.";
           throw new StorageException($msg, StorageException::RETRIEVAL);
       }

       $data = $storageService->get($userUri, $sessionId);
       
       $stateEmpty = (empty($data) === true);
       $stream = new MemoryStream(($stateEmpty === true) ? '' : $data);
       $stream->open();
       
       if ($stateEmpty === false) {
           // Consume additional error (short signed integer).
           $this->setLastError($stream->read(2));
       }
       
       $stream->close();
       return $stream;
   }
   
   protected function persistStream(AssessmentTestSession $assessmentTestSession, MemoryStream $stream) {
       
       $storageService = tao_models_classes_service_StateStorage::singleton();
       $userUri = $this->getUserUri();
       
       if (is_null($userUri) === true) {
           $msg = "Could not retrieve current user URI.";
           throw new StorageException($msg, StorageException::RETRIEVAL);
       }
       
       $data = $this->getLastError() . $stream->getBinary();
       $storageService->set($userUri, $assessmentTestSession->getSessionId(), $data);
   }
   
   public function exists($sessionId) {
       $storageService = tao_models_classes_service_StateStorage::singleton();
       $userUri = $this->getUserUri();
       
       if (is_null($userUri) === true) {
           $msg = "Could not retrieve current user URI.";
           throw new StorageException($msg, StorageException::RETRIEVAL);
       }
       
       return $storageService->has($userUri, $sessionId);
   }
   
   protected function createBinaryStreamAccess(IStream $stream) {
       return new QtiBinaryStreamAccessFsFile($stream);
   }
}
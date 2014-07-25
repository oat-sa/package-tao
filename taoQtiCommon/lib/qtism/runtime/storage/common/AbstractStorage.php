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
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * @subpackage 
 *
 */
namespace qtism\runtime\storage\common;

use qtism\runtime\tests\AbstractAssessmentTestSessionFactory;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\data\AssessmentTest;
use \LogicException;

/**
 * The AbstractStorage class is extended by any class that claims to 
 * offer an AssessmentTestSession Storage Service.
 * 
 * An AssessmentTestSession Storage Service must be able to:
 * 
 * * Instantiate an AssessmentTestSession from its AssessmentTest definition.
 * * Persist an AssessmentTestSession for a later retrieval.
 * * Retrieve an AssessmentTestSession.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractStorage {
    
    /**
     * The factory to be used to instantiate AssessmentTestSession objects.
     * 
     * @var AbstractAssessmentTestSessionFactory
     */
    private $factory;
    
    /**
     * Create a new AbstracStorage object.
     * 
     * @param AbstractAssessmentTestSessionFactory The factory to be used to instantiate AssessmentTestSession objects.
     */
    public function __construct(AbstractAssessmentTestSessionFactory $factory) {
        $this->setFactory($factory);
    }
    
    /**
     * Set the factory to be used to instantiate AssessmentTestSession objects.
     * 
     * @param AbstractAssessmentTestSessionFactory
     */
    protected function setFactory(AbstractAssessmentTestSessionFactory $factory) {
        $this->factory = $factory;
    }
    
    /**
     * Get the factory to be used to instantiate AssessmentTestSession objects.
     *
     * @return AbstractAssessmentTestSessionFactory
     */
    protected function getFactory() {
        return $this->factory;
    }
    
    /**
     * Get the AssessmentTest definition used by the storage as a pattern test definition.
     * 
     * @return AssessmentTest An AssessmentTest object.
     */
    protected function getAssessmentTest() {
        return $this->getFactory()->getAssessmentTest();
    }
    
    /**
     * Instantiate an AssessmentTestSession from the $assessmentTest AssessmentTest
     * definition. An AssessmentTestSession object is returned, with a session ID that will
     * make client code able to retrive persisted AssessmentTestSession objects later on.
     * 
     * If $sessionId is not provided, the AssessmentTestSession Storage Service implementation
     * must generate its own session ID.
     * 
     * @param string $sessionId (optional) A wanted $sessionId to be used to identify the instantiated AssessmentTest.
     * @throws StorageException If an error occurs while instantiating the AssessmentTest definition.
     */
    abstract public function instantiate($sessionId = '');
    
    /**
     * Persist an AssessmentTestSession object for a later retrieval thanks to its
     * session ID.
     * 
     * @param AssessmentTestSession $assessmentTestSession An AssessmentTestSession object to be persisted.
     * @throws StorageException If an error occurs while persisting the $assessmentTestSession.
     * @throws LogicException 
     */
    public function persist(AssessmentTestSession $assessmentTestSession) {
        if ($assessmentTestSession->getAssessmentTest() !== $this->getAssessmentTest()) {
            $msg = "The AssessmentTestSession object to be persisted has not the same ";
            $msg.= "AssessmentTest definition than the one used by this AssessmentTestSession ";
            $msg.= "Storage Implementation.";
            
            throw new LogicException($msg);
        }
    }
    
    /**
     * Retrieve a previously persisted AssessmentTestSession object.
     * 
     * @param string $sessionId The Session ID of the AssessmentTestSession to be retrieved.
     * @throws StorageException If an error occurs while retrieving the AssessmentTestSession.
     */
    abstract public function retrieve($sessionId);
}
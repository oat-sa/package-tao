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
namespace qtism\runtime\storage\binary;

use qtism\runtime\tests\PendingResponseStore;
use qtism\runtime\tests\AbstractAssessmentTestSessionFactory;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\tests\AssessmentItemSessionStore;
use qtism\runtime\tests\Route;
use qtism\data\Document;
use qtism\runtime\storage\common\AssessmentTestSeeker;
use qtism\runtime\storage\common\StorageException;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\data\AssessmentTest;
use qtism\runtime\storage\common\AbstractStorage;
use \SplObjectStorage;
use \Exception;
use \RuntimeException;
use \InvalidArgumentException;

/**
 * An abstract Binary AssessmentTestSession Storage Service implementation.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractQtiBinaryStorage extends AbstractStorage {
    
    /**
     * The AssessmentTestSeeker object used by this implementation.
     * 
     * @var AssessmentTestSeeker
     */
    private $seeker;
    
    /**
     * Create a new AbstractQtiBinaryStorage.
     * 
     * @param AbstractAssessmentTestSessionFactory
     * @throws InvalidArgumentException If $assessmentTest does not implement the Document interface.
     */
    public function __construct(AbstractAssessmentTestSessionFactory $factory) {
        parent::__construct($factory);
        
        $seekerClasses = array('assessmentItemRef', 'assessmentSection', 'testPart', 'outcomeDeclaration',
                                'responseDeclaration', 'branchRule', 'preCondition', 'itemSessionControl');
        
        $this->setSeeker(new AssessmentTestSeeker($this->getAssessmentTest(), $seekerClasses));
    }
    
    /**
     * Get the AssessmentTestSeeker object used by this implementation.
     * 
     * @param AssessmentTestSeeker $seeker An AssessmentTestSeeker object.
     */
    protected function setSeeker(AssessmentTestSeeker $seeker) {
        $this->seeker = $seeker;
    }
    
    /**
     * Set the AssessmentTestSeeker object used by this implementation.
     * 
     * @return AssessmentTestSeeker An AssessmentTestSeeker object.
     */
    protected function getSeeker() {
        return $this->seeker;
    }
    
    /**
     * Instantiate a new AssessmentTestSession.
     * 
     * @param string $sessionId An session ID. If not provided, a new session ID will be generated and given to the AssessmentTestSession.
     * @return AssessmentTestSession An AssessmentTestSession object.  
     */
    public function instantiate($sessionId = '') {
        
        // If not provided, generate a session ID.
        if (empty($sessionId) === true) {
            $sessionId = uniqid('qtism', true);
        }
        
        try {
            $session = AssessmentTestSession::instantiate($this->getFactory());
            $session->setSessionId($sessionId);
            
            return $session;
        }
        catch (Exception $e) {
            $msg = "An error occured while instantiating the given AssessmentTest.";
            throw new StorageException($msg, StorageException::INSTANTIATION, $e);
        }
    }
    
    /**
     * Persist an AssessmentTestSession into binary data.
     * 
     * The QTI Binary Storage Version that will be used to persist the AssessmentTestSession
     * will be systematically the one defined in QtiBinaryConstants::QTI_BINARY_STORAGE_VERSION. 
     * 
     */
    public function persist(AssessmentTestSession $assessmentTestSession) {
        
        parent::persist($assessmentTestSession);
        
        try {
            
            $stream = new BinaryStream();
            $stream->open();
            $access = new QtiBinaryStreamAccess($stream);
            
            // write the QTI Binary Storage version in use to persist the test session.
            $access->writeTinyInt(QtiBinaryConstants::QTI_BINARY_STORAGE_VERSION);
            
            $access->writeTinyInt($assessmentTestSession->getState());
            
            $route = $assessmentTestSession->getRoute();
            $access->writeTinyInt($route->getPosition());
            
            // Persist the Route of the AssessmentTestSession and the related item sessions.
            $access->writeTinyInt($route->count());
            $itemSessionStore = $assessmentTestSession->getAssessmentItemSessionStore();
            $pendingResponseStore = $assessmentTestSession->getPendingResponseStore();
            
            foreach ($route as $routeItem) {
                $item = $routeItem->getAssessmentItemRef();
                $occurence = $routeItem->getOccurence();
                
                // Deal with RouteItem
                $access->writeRouteItem($this->getSeeker(), $routeItem);
                
                // Deal with ItemSession related to the previously written RouteItem.
                $itemSession = $itemSessionStore->getAssessmentItemSession($item, $occurence);
                $access->writeAssessmentItemSession($this->getSeeker(), $itemSession);
                
                // Deal with last occurence update.
                $access->writeBoolean($assessmentTestSession->isLastOccurenceUpdate($item, $occurence));
                
                // Deal with PendingResponses
                if (($pendingResponses = $pendingResponseStore->getPendingResponses($item, $occurence)) !== false) {
                    $access->writeBoolean(true);
                    $access->writePendingResponses($this->getSeeker(), $pendingResponses);
                }
                else {
                    $access->writeBoolean(false);
                }
            }
            
            // Deal with test session configuration.
            // -- AutoForward
            $access->writeBoolean($assessmentTestSession->mustAutoForward());
            
            // Persist the test-level global scope.
            foreach ($assessmentTestSession->getKeys() as $outcomeIdentifier) {
            	$outcomeVariable = $assessmentTestSession->getVariable($outcomeIdentifier);
            	$access->writeVariableValue($outcomeVariable);
            }
            
            $this->persistStream($assessmentTestSession, $stream);
            
            $stream->close();
        }
        catch (Exception $e) {
            $sessionId = $assessmentTestSession->getSessionId();
            $msg = "An error occured while persisting AssessmentTestSession with ID '${sessionId}'.";
            throw new StorageException($msg, StorageException::PERSITANCE, $e);
        }
    }
    
    /**
     * Retrieve an AssessmentTestSession object from storage by $sessionId.
     * 
     * @return AssessmentTestSession An AssessmentTestSession object.
     * @throws StorageException If the AssessmentTestSession could not be retrieved from storage.
     */
    public function retrieve($sessionId) {
        
        try {
            
            $stream = $this->getRetrievalStream($this->getAssessmentTest(), $sessionId);
            $stream->open();
            $access = new QtiBinaryStreamAccess($stream);
            
            $version = $access->readTinyInt();
            $assessmentTestSessionState = $access->readTinyInt();
            $currentPosition = $access->readTinyInt();
            
            // Build the route and the item sessions.
            $route = new Route();
            $lastOccurenceUpdate = new SplObjectStorage();
            $itemSessionStore = new AssessmentItemSessionStore();
            $pendingResponseStore = new PendingResponseStore();
            $routeCount = $access->readTinyInt();
            
           for ($i = 0; $i < $routeCount; $i++) {
                $routeItem = $access->readRouteItem($this->getSeeker());
                $itemSession = $access->readAssessmentItemSession($this->getSeeker());
                
                // last-update
                if ($access->readBoolean() === true) {
                    $lastOccurenceUpdate[$routeItem->getAssessmentItemRef()] = $routeItem->getOccurence();
                }
                
                // pending-responses
                if ($access->readBoolean() === true) {
                    $pendingResponseStore->addPendingResponses($access->readPendingResponses($this->getSeeker()));
                }
            
                $route->addRouteItemObject($routeItem);
                $itemSessionStore->addAssessmentItemSession($itemSession, $routeItem->getOccurence());
            }
            
            $route->setPosition($currentPosition);
            $factory = $this->getFactory();
            $factory->setRoute($route);
            $assessmentTestSession = $factory->createAssessmentTestSession();
            $assessmentTestSession->setAssessmentItemSessionStore($itemSessionStore);
            $assessmentTestSession->setSessionId($sessionId);
            $assessmentTestSession->setState($assessmentTestSessionState);
            $assessmentTestSession->setLastOccurenceUpdate($lastOccurenceUpdate);
            $assessmentTestSession->setPendingResponseStore($pendingResponseStore);

            // Deal with test session configuration.
            // -- AutoForward
            $assessmentTestSession->setAutoForward($access->readBoolean());
            
            // Build the test-level global scope, composed of Outcome Variables.
            foreach ($this->getAssessmentTest()->getOutcomeDeclarations() as $outcomeDeclaration) {
            	$outcomeVariable = OutcomeVariable::createFromDataModel($outcomeDeclaration);
            	$access->readVariableValue($outcomeVariable);
            	$assessmentTestSession->setVariable($outcomeVariable);
            }

            $stream->close();
            
            return $assessmentTestSession;
        }
        catch (Exception $e) {
            $assessmentTestUri = $this->getAssessmentTest()->getUri();
            $msg = "An error occured while retrieving AssessmentTestSession for AssessmentTest '${assessmentTestUri}'.";
            throw new StorageException($msg, StorageException::RETRIEVAL, $e);
        }
    }
    
    /**
     * Get the BinaryStream that has to be used to retrieve an AssessmentTestSession.
     * 
     * Be careful, the implementation of this method must not open the given $stream.
     * 
     * @param string $sessionId A test session identifier.
     * @throws RuntimeException If an error occurs.
     * @return BinaryStream A BinaryStream object.
     */
    abstract protected function getRetrievalStream(Document $assessmentTest, $sessionId);
    
    /**
     * Persist A BinaryStream that contains the binary data representing $assessmentTestSession
     * in an appropriate location.
     * 
     * Be careful, the implementation of this method must not close the given $stream.
     * 
     * @param AssessmentTestSession $assessmentTestSession An AssessmentTestSession object.
     * @param BinaryStream $stream An open BinaryStream object.
     */
    abstract protected function persistStream(AssessmentTestSession $assessmentTestSession, BinaryStream $stream);
}
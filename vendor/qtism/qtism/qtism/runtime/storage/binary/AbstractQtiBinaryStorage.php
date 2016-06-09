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
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\runtime\storage\binary;

use qtism\common\storage\IStream;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\tests\DurationStore;
use qtism\runtime\tests\PendingResponseStore;
use qtism\runtime\tests\AbstractSessionManager;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\tests\AssessmentItemSessionStore;
use qtism\runtime\tests\Route;
use qtism\runtime\storage\common\AssessmentTestSeeker;
use qtism\runtime\storage\common\StorageException;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\data\AssessmentTest;
use qtism\runtime\storage\common\AbstractStorage;
use qtism\common\storage\MemoryStream;
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
     * @param AbstractSessionManager $factory
     * @param BinaryAssessmentTestSeeker $seeker
     * @throws InvalidArgumentException If $assessmentTest does not implement the Document interface.
     */
    public function __construct(AbstractSessionManager $manager, BinaryAssessmentTestSeeker $seeker) {
        parent::__construct($manager);
        $this->setSeeker($seeker);
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
     * @param AssessmentTest $test
     * @param string $sessionId An session ID. If not provided, a new session ID will be generated and given to the AssessmentTestSession.
     * @return AssessmentTestSession An AssessmentTestSession object.  
     */
    public function instantiate(AssessmentTest $test, $sessionId = '') {
        
        // If not provided, generate a session ID.
        if (empty($sessionId) === true) {
            $sessionId = uniqid('qtism', true);
        }
        
        try {
            $session = $this->getManager()->createAssessmentTestSession($test);
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
     * @param AssessmentTestSession $assessmentTestSession
     * @throws StorageException
     */
    public function persist(AssessmentTestSession $assessmentTestSession) {
        
        try {
            
            $stream = new MemoryStream();
            $stream->open();
            $access = $this->createBinaryStreamAccess($stream);
            
            // write the QTI Binary Storage version in use to persist the test session.
            $access->writeTinyInt(QtiBinaryConstants::QTI_BINARY_STORAGE_VERSION);
            $access->writeTinyInt($assessmentTestSession->getState());
            
            $route = $assessmentTestSession->getRoute();
            $access->writeTinyInt($route->getPosition());
            
            // Persist the Route of the AssessmentTestSession and the related item sessions.
            $access->writeTinyInt($route->count());
            $itemSessionStore = $assessmentTestSession->getAssessmentItemSessionStore();
            $pendingResponseStore = $assessmentTestSession->getPendingResponseStore();

            $routeItems = $route->getAllRouteItems();
            foreach ($routeItems as $routeItem) {
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
            // -- AutoForward (not in use anymore, fake it).
            $access->writeBoolean(false);
            
            // Persist the test-level global scope.
            foreach ($assessmentTestSession->getKeys() as $outcomeIdentifier) {
            	$outcomeVariable = $assessmentTestSession->getVariable($outcomeIdentifier);
            	$access->writeVariableValue($outcomeVariable);
            }
            
            $durationStore = $assessmentTestSession->getDurationStore();
            $access->writeShort(count($durationStore));
            foreach ($durationStore->getKeys() as $k) {
                $access->writeString($k);
                $access->writeVariableValue($durationStore->getVariable($k));
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
     * @param AssessmentTest $test
     * @param string $sessionId
     * @return AssessmentTestSession An AssessmentTestSession object.
     * @throws StorageException If the AssessmentTestSession could not be retrieved from storage.
     */
    public function retrieve(AssessmentTest $test, $sessionId) {
        
        try {
            
            $stream = $this->getRetrievalStream($sessionId);
            $stream->open();
            $access = $this->createBinaryStreamAccess($stream);
            
            $version = $access->readTinyInt();
            $assessmentTestSessionState = $access->readTinyInt();
            $currentPosition = $access->readTinyInt();
            
            // Build the route and the item sessions.
            $route = new Route();
            $lastOccurenceUpdate = new SplObjectStorage();
            $itemSessionStore = new AssessmentItemSessionStore();
            $pendingResponseStore = new PendingResponseStore();
            $routeCount = $access->readTinyInt();
            
            // Create the item session factory that will be used to instantiate
            // new item sessions.
            
            for ($i = 0; $i < $routeCount; $i++) {
                $routeItem = $access->readRouteItem($this->getSeeker());
                $itemSession = $access->readAssessmentItemSession($this->getManager(), $this->getSeeker());
                
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
            $manager = $this->getManager();
            $assessmentTestSession = $manager->createAssessmentTestSession($test, $route);
            $assessmentTestSession->setAssessmentItemSessionStore($itemSessionStore);
            $assessmentTestSession->setSessionId($sessionId);
            $assessmentTestSession->setState($assessmentTestSessionState);
            $assessmentTestSession->setLastOccurenceUpdate($lastOccurenceUpdate);
            $assessmentTestSession->setPendingResponseStore($pendingResponseStore);

            // Deal with test session configuration.
            // -- AutoForward (not in use anymore, consume it anyway).
            $access->readBoolean();
            
            // Build the test-level global scope, composed of Outcome Variables.
            foreach ($test->getOutcomeDeclarations() as $outcomeDeclaration) {
            	$outcomeVariable = OutcomeVariable::createFromDataModel($outcomeDeclaration);
            	$access->readVariableValue($outcomeVariable);
            	$assessmentTestSession->setVariable($outcomeVariable);
            }
            
            // Build the duration store.
            $durationStore = new DurationStore();
            
            if ($version >= 4) {
                $durationCount = $access->readShort();
                for ($i = 0; $i < $durationCount; $i++) {
                    $varName = $access->readString();
                    $durationVariable = new OutcomeVariable($varName, Cardinality::SINGLE, BaseType::DURATION);
                    $access->readVariableValue($durationVariable);
                    $durationStore->setVariable($durationVariable);
                }
                
                $assessmentTestSession->setDurationStore($durationStore);
            }

            $stream->close();
            
            return $assessmentTestSession;
        }
        catch (Exception $e) {
            $msg = "An error occured while retrieving AssessmentTestSession.";
            throw new StorageException($msg, StorageException::RETRIEVAL, $e);
        }
    }
    
    /**
     * Get the MemoryStream that has to be used to retrieve an AssessmentTestSession.
     * 
     * Be careful, the implementation of this method must not open the given $stream.
     * 
     * @param string $sessionId A test session identifier.
     * @throws RuntimeException If an error occurs.
     * @return MemoryStream A MemoryStream object.
     */
    abstract protected function getRetrievalStream($sessionId);
    
    /**
     * Persist A MemoryStream that contains the binary data representing $assessmentTestSession
     * in an appropriate location.
     * 
     * Be careful, the implementation of this method must not close the given $stream.
     * 
     * @param AssessmentTestSession $assessmentTestSession An AssessmentTestSession object.
     * @param MemoryStream $stream An open MemoryStream object.
     */
    abstract protected function persistStream(AssessmentTestSession $assessmentTestSession, MemoryStream $stream);
    
    abstract protected function createBinaryStreamAccess(IStream $stream);
}
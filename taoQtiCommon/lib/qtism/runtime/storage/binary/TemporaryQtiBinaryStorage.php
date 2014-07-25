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

use qtism\data\Document;
use qtism\runtime\tests\AssessmentTestSession;
use \RuntimeException;

/**
 * A Binary AssessmentTestSession Storage Service implementation which stores the binary data related
 * to AssessmentTestSession objects in the temporary directory of the host file system.
 * 
 * This implementation was created for test purpose and should not be used for production.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TemporaryQtiBinaryStorage extends AbstractQtiBinaryStorage {
    
    /**
     * Persist the binary stream $stream which contains the binary equivalent of $assessmentTestSession in
     * the temporary directory of the file system.
     * 
     * @param AssessmentTestSession The AssessmentTestSession to be persisted.
     * @param BinaryStream The BinaryStream to be stored in the temporary directory of the host file system.
     * @throws RuntimeException If the binary stream cannot be persisted.
     */
    protected function persistStream(AssessmentTestSession $assessmentTestSession, BinaryStream $stream) {
        
        $assessmentTestUri = $assessmentTestSession->getAssessmentTest()->getUri();
        $sessionId = $assessmentTestSession->getSessionId();
        
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($assessmentTestUri . $sessionId) . '.bin';
        $written = @file_put_contents($path, $stream->getBinary());
        
        if ($written === false || $written === 0) {
            $msg = "An error occured while persisting the binary stream at '${path}'.";
            throw new RuntimeException($msg);
        }
    }
    
    /**
     * Retrieve the binary representation of the AssessmentTestSession identified by $sessionId which was
     * instantiated from $assessmentTest from the temporary directory of the file system.
     * 
     * @param Document $assessmentTest The AssessmentTest the retrieved AssessmentTestSession was instantiated from.
     * @param string $sessionId The session ID of the AssessmentTestSession to retrieve.
     * @return BinaryStream A BinaryStream object.
     * @throws RuntimeException If the binary stream cannot be persisted.
     */
    protected function getRetrievalStream(Document $assessmentTest, $sessionId) {
        
        $assessmentTestUri = $assessmentTest->getUri();
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($assessmentTestUri . $sessionId) . '.bin';
        
        $read = @file_get_contents($path);
        
        if ($read === false || strlen($read) === 0) {
            $msg = "An error occured while retrieving the binary stream at '${path}'.";
            throw new RuntimeException($msg);
        }
        
        return new BinaryStream($read);
    }
}
<?php

use qtism\data\storage\php\marshalling\PhpMarshallingContext;
use qtism\common\storage\MemoryStream;
use qtism\data\storage\php\PhpStreamAccess;

require_once(dirname(__FILE__) . '/../qtism/qtism.php');
require_once(dirname(__FILE__) . '/QtiSmTestCase.php');

abstract class QtiSmPhpMarshallerTestCase extends QtiSmTestCase {
    
    /**
     * An access to an open PHP source code stream.
     * 
     * @var PhpStreamAccess
     */
    private $streamAccess;
    
    /**
     * A stream
     * 
     * @var MemoryStream
     */
    private $stream;
    
	public function setUp() {
	    parent::setUp();
	    
	    $stream = new MemoryStream();
	    $stream->open();
	    $this->setStream($stream);
	    $this->setStreamAccess(new PhpStreamAccess($this->getStream()));
	}
	
	public function tearDown() {
	    parent::tearDown();
	    
	    $streamAccess = $this->getStreamAccess();
	    unset($streamAccess);
	    
	    $stream = $this->getStream();
	    unset($stream);
	}
	
	public function createMarshallingContext() {
	    $ctx = new PhpMarshallingContext($this->getStreamAccess());
	    $ctx->setFormatOutput(true);
	    return $ctx;
	}
	
	protected function setStream(MemoryStream $stream) {
	    $this->stream = $stream;
	}
	
	/**
	 * 
	 * @return MemoryStream
	 */
	protected function getStream() {
	    return $this->stream;
	}
	
	/**
	 * 
	 * @return PhpStreamAccess
	 */
	protected function getStreamAccess() {
	    return $this->streamAccess;
	}
	
	protected function setStreamAccess(PhpStreamAccess $streamAccess) {
	    $this->streamAccess = $streamAccess;
	}
}
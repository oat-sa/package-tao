<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\storage\MemoryStream;
use qtism\common\storage\MemoryStreamException;

class BinaryStreamTest extends QtiSmTestCase {
	
    private $basicStream;
    
    private $emptyStream;
    
    public function getBasicStream() {
        return $this->basicStream;
    }
    
    public function getEmptyStream() {
        return $this->emptyStream;
    }
    
    public function setUp() {
        parent::setUp();
        $this->basicStream =  new MemoryStream('binary-data');
        $this->emptyStream = new MemoryStream();
    }
    
    public function tearDown() {
        parent::tearDown();
        unset($this->basicStream);
        unset($this->emptyStream);
    }
    
    public function testInstantiate() {
        $stream = $this->getBasicStream();
        $this->assertInstanceOf('qtism\\common\\storage\\MemoryStream', $stream);
        
        $this->assertEquals('binary-data', $stream->getBinary());
        $this->assertFalse($stream->isOpen());
        $this->assertInternalType('integer', $stream->getPosition());
        $this->assertEquals(0, $stream->getPosition());
        $this->assertEquals(strlen('binary-data'), $stream->getLength());
    }
    
    public function testCloseOnClosedStream() {
        $stream = $this->getBasicStream();
        
        try {
            $stream->close();
            // An exception must be thrown.
            $this->assertTrue(false);
        }
        catch (MemoryStreamException $e) {
            $this->assertEquals(MemoryStreamException::NOT_OPEN, $e->getCode());
        }
    }
    
    public function testRewindOnClosedStream() {
        $stream = $this->getBasicStream();
        
        try {
            $stream->rewind();
            // An exception must be thrown.
            $this->assertTrue(false);
        }
        catch (MemoryStreamException $e) {
            $this->assertEquals(MemoryStreamException::NOT_OPEN, $e->getCode());
        }
    }
    
    public function testOpen() {
        $stream = $this->getBasicStream();
        $stream->open();
        
        $this->assertTrue($stream->isOpen());
    }
    
    public function testClose() {
        $stream = $this->getBasicStream();
        $stream->open();
        $stream->close();
        $this->assertFalse($stream->isOpen());
    }
    
    public function testRead() {
        $stream = $this->getBasicStream();
        $stream->open();
        
        $data = $stream->read(0);
        $this->assertInternalType('string', $data);
        $this->assertEquals('', $data);
        $this->assertFalse($stream->eof());
        
        $data = $stream->read(6);
        $this->assertEquals('binary', $data);
        $this->assertEquals(6, $stream->getPosition());
        $this->assertFalse($stream->eof());
        
        $data = $stream->read(1);
        $this->assertEquals('-', $data);
        $this->assertEquals(7, $stream->getPosition());
        $this->assertFalse($stream->eof());
        
        $data = $stream->read(4);
        $this->assertEquals('data', $data);
        $this->assertEquals(11, $stream->getPosition());
        $this->assertTrue($stream->eof());
        
        try {
            // EOF is reached... cannot read more.
            $data = $stream->read(1);
            $this->assertTrue(false);
        }
        catch (MemoryStreamException $e) {
            $this->assertTrue(true);
        }
        
        $stream->close();
    }
    
    public function testWrite() {
        // test writing in an empty stream.
        $stream = $this->getEmptyStream();
        $stream->open();
        
        $this->assertInternalType('string', $stream->getBinary());
        $this->assertEquals('', $stream->getBinary());
        
        $toWrite = 'binary';
        $this->assertEquals(strlen($toWrite), $stream->write($toWrite));
        $this->assertEquals(strlen($toWrite), $stream->getLength());
        $this->assertEquals($toWrite, $stream->getBinary());
        $this->assertEquals(strlen($toWrite), $stream->getPosition());
        
        $this->assertEquals(0, $stream->write(''));
        $this->assertEquals(strlen($toWrite), $stream->getLength());
        $this->assertEquals($toWrite, $stream->getBinary());
        $this->assertEquals(strlen($toWrite), $stream->getPosition());
        
        $this->assertEquals(1, $stream->write('-'));
        $this->assertEquals(7, $stream->getLength());
        $this->assertEquals('binary-', $stream->getBinary());
        $this->assertEquals(7, $stream->getPosition());
        
        $this->assertEquals(4, $stream->write('data'));
        $this->assertEquals(11, $stream->getLength());
        $this->assertEquals('binary-data', $stream->getBinary());
        $this->assertEquals(11, $stream->getPosition());
        
        $stream->close();
        
        // test writing in a non-empty stream.
        $stream = $this->getBasicStream();
        $stream->open();
        
        $this->assertEquals(5, $stream->write('-1337'));
        $this->assertEquals(16, $stream->getLength());
        $this->assertEquals('-1337binary-data', $stream->getBinary());
        
        $this->assertEquals('binary', $stream->read(6));
        
        $stream->close();
    }
    
    public function testOpenOnOpenStream() {
        $stream = $this->getBasicStream();
        
        try {
            $stream->open();
            $stream->open();
            // An exception must be thrown.
            $this->assertTrue(false);
        }
        catch (MemoryStreamException $e) {
            $this->assertEquals(MemoryStreamException::ALREADY_OPEN, $e->getCode());
        }
    }
}
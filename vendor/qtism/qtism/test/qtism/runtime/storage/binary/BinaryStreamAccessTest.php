<?php

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\storage\MemoryStream;
use qtism\common\storage\BinaryStreamAccess;
use qtism\common\storage\BinaryStreamAccessException;

class BinaryStreamAccessTest extends QtiSmTestCase {
	
    private $emptyStream;
    
    public function setUp() {
        parent::setUp();
        
        $this->emptyStream = new MemoryStream();
        $this->emptyStream->open();
    }
    
    public function tearDown() {
        parent::tearDown();
        
        unset($this->emptyStream);
    }
    
    /**
     * Get an open empty stream
     * 
     * @return MemoryStream
     */
    public function getEmptyStream() {
        return $this->emptyStream;
    }
    
    public function testReadTinyInt() {
        $stream = new MemoryStream("\x00\x01\x0A");
        $stream->open();
        
        $reader = new BinaryStreamAccess($stream);
        $tinyInt = $reader->readTinyInt();
        $this->assertInternalType('integer', $tinyInt);
        $this->assertEquals(0, $tinyInt);
        
        $tinyInt = $reader->readTinyInt();
        $this->assertInternalType('integer', $tinyInt);
        $this->assertEquals(1, $tinyInt);
        
        $tinyInt = $reader->readTinyInt();
        $this->assertInternalType('integer', $tinyInt);
        $this->assertEquals(10, $tinyInt);
        
        try {
            // EOF reached.
            $tinyInt = $reader->readTinyInt();
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::TINYINT, $e->getCode());
        }
    }
    
    public function testWriteTinyInt() {
        $stream = $this->getEmptyStream();
        $access = new BinaryStreamAccess($stream);
        
        $access->writeTinyInt(0);
        $access->writeTinyInt(1);
        $access->writeTinyInt(255);
        $stream->rewind();
        
        $reader = new BinaryStreamAccess($stream);
        
        $val = $reader->readTinyInt();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(0, $val);
        
        $val = $reader->readTinyInt();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(1, $val);
        
        $val = $reader->readTinyInt();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(255, $val);
    }
    
    public function testReadDateTime() {
        $date = new DateTime('2013:09:04 09:37:09', new DateTimeZone('Europe/Luxembourg'));
        $stream = new MemoryStream(pack('l', $date->getTimestamp()));
        $stream->open();
        $access = new BinaryStreamAccess($stream);
        
        $date = $access->readDateTime();
        $this->assertEquals(1378280229, $date->getTimestamp());
        
        try {
            // EOF
            $date = $access->readDateTime();
            $this->assertTrue(false);
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::DATETIME, $e->getCode());
        }
    }
    
    public function testWriteDateTime() {
        $stream = $this->getEmptyStream();
        $access = new BinaryStreamAccess($stream);
        
        $access->writeDateTime(new DateTime('2013:09:04 09:37:09', new DateTimeZone('Europe/Luxembourg')));
        $stream->rewind();
        
        $date = $access->readDateTime();
        $this->assertEquals(1378280229, $date->getTimestamp());
    }
    
    public function testReadShort() {
        $stream = new MemoryStream(pack('S', 0) . pack('S', 1) . pack ('S', 65535));
        $stream->open();
        $reader = new BinaryStreamAccess($stream);
        
        $short = $reader->readShort();
        $this->assertInternalType('integer', $short);
        $this->assertEquals(0, $short);
        
        $short = $reader->readShort();
        $this->assertInternalType('integer', $short);
        $this->assertEquals(1, $short);
        
        $short = $reader->readShort();
        $this->assertInternalType('integer', $short);
        $this->assertEquals(65535, $short);
        
        // go beyond EOF.
        try {
            $short = $reader->readShort();
            $this->assertTrue(false);
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::SHORT, $e->getCode());
        }
        
        // try to read on a closed stream.
        try {
            $stream = $this->getEmptyStream();
            $stream->close();
            $reader = new BinaryStreamAccess($stream);
            $short = $reader->readShort();
            $this->assertTrue(false);
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::NOT_OPEN, $e->getCode());
        }
    }
    
    public function testWriteShort() {
        $stream = $this->getEmptyStream();
        $access = new BinaryStreamAccess($stream);
        
        $access->writeShort(0);
        $access->writeShort(1);
        $access->writeShort(65535);
        $stream->rewind();
        
        $val = $access->readShort();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(0, $val);
        
        $val = $access->readShort();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(1, $val);
        
        $val = $access->readShort();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(65535, $val);
    }
    
    public function testReadInt() {
        $stream = new MemoryStream(pack('l', 0) . pack('l', 1) . pack('l', -1) . pack('l', 2147483647) . pack('l', -2147483648));
        $stream->open();
        $reader = new BinaryStreamAccess($stream);
        
        $int = $reader->readInteger();
        $this->assertInternalType('integer', $int);
        $this->assertEquals(0, $int);
        
        $int = $reader->readInteger();
        $this->assertInternalType('integer', $int);
        $this->assertEquals(1, $int);
        
        $int = $reader->readInteger();
        $this->assertInternalType('integer', $int);
        $this->assertEquals(-1, $int);
        
        $int = $reader->readInteger();
        $this->assertInternalType('integer', $int);
        $this->assertEquals(2147483647, $int);
        
        $int = $reader->readInteger();
        $this->assertInternalType('integer', $int);
        $this->assertEquals(-2147483648, $int);
        
        // reach EOF.
        try {
            $int = $reader->readInteger();
            $this->assertTrue(false);
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::INT, $e->getCode());
        }
    }
    
    public function testWriteInt() {
        $stream = $this->getEmptyStream();
        $access = new BinaryStreamAccess($stream);
        
        $access->writeInteger(0);
        $access->writeInteger(1);
        $access->writeInteger(-1);
        $access->writeInteger(2147483647);
        $access->writeInteger(-2147483648);
        $stream->rewind();
        
        $val = $access->readInteger();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(0, $val);
        
        $val = $access->readInteger();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(1, $val);
        
        $val = $access->readInteger();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(-1, $val);
        
        $val = $access->readInteger();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(2147483647, $val);
        
        $val = $access->readInteger();
        $this->assertInternalType('integer', $val);
        $this->assertEquals(-2147483648, $val);
    }
    
    public function testReadBool() {
        $stream = new MemoryStream("\x00\x01");
        $stream->open();
        $reader = new BinaryStreamAccess($stream);
        
        $bool = $reader->readBoolean();
        $this->assertInternalType('boolean', $bool);
        $this->assertFalse($bool);
        
        $bool = $reader->readBoolean();
        $this->assertInternalType('boolean', $bool);
        $this->assertTrue($bool);
        
        try {
            $bool = $reader->readBoolean();
            $this->assertTrue(false);
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::BOOLEAN, $e->getCode());
        }
    }
    
    public function testWriteBool() {
        $stream = $this->getEmptyStream();
        $access = new BinaryStreamAccess($stream);
        
        $access->writeBoolean(true);
        $access->writeBoolean(false);
        $stream->rewind();
        
        $val = $access->readBoolean();
        $this->assertInternalType('boolean', $val);
        $this->assertTrue($val);
        
        $val = $access->readBoolean();
        $this->assertInternalType('boolean', $val);
        $this->assertFalse($val);
    }
    
    public function testReadString() {
        $stream = new MemoryStream(pack('S', 0) . '' . pack('S', 1) . 'A' . pack ('S', 6) . 'binary');
        $stream->open();
        $reader = new BinaryStreamAccess($stream);
        
        $string = $reader->readString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('', $string);
        
        $string = $reader->readString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('A', $string);
        
        $string = $reader->readString();
        $this->assertInternalType('string', $string);
        $this->assertEquals('binary', $string);
        
        try {
            $string = $reader->readString();
            $this->assertTrue(false);
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::STRING, $e->getCode());
        }
    }
    
    public function testWriteString() {
        $stream = $this->getEmptyStream();
        $access = new BinaryStreamAccess($stream);
        
        $access->writeString('');
        $access->writeString('A');
        $access->writeString('binary');
        $stream->rewind();
        
        $val = $access->readString();
        $this->assertInternalType('string', $val);
        $this->assertEquals('', $val);
        
        $val = $access->readString();
        $this->assertInternalType('string', $val);
        $this->assertEquals('A', $val);
        
        $val = $access->readString();
        $this->assertInternalType('string', $val);
        $this->assertEquals('binary', $val);
    }
    
    public function testReadFloat() {
        $stream = new MemoryStream(pack('d', 0.0) . pack('d', -M_PI) . pack('d', M_2_PI));
        $stream->open();
        $reader = new BinaryStreamAccess($stream);
        
        $float = $reader->readFloat();
        $this->assertInternalType('float', $float);
        $this->assertEquals(round(0.0, 3), round($float, 3));
        
        $float = $reader->readFloat();
        $this->assertInternalType('float', $float);
        $this->assertEquals(round(-M_PI, 3), round($float, 3));
        
        $float = $reader->readFloat();
        $this->assertInternalType('float', $float);
        $this->assertEquals(round(M_2_PI, 3), round($float, 3));
        
        try {
            $float = $reader->readFloat();
        }
        catch (BinaryStreamAccessException $e) {
            $this->assertEquals(BinaryStreamAccessException::FLOAT, $e->getCode());
        }
    }
    
    public function testWriteFloat() {
        $stream = $this->getEmptyStream();
        $access = new BinaryStreamAccess($stream);
        
        $access->writeFloat(0.0);
        $access->writeFloat(-M_PI);
        $access->writeFloat(M_2_PI);
        $stream->rewind();
        
        $val = $access->readFloat();
        $this->assertInternalType('float', $val);
        $this->assertEquals(round(0.0, 3), round($val, 3));
        
        $val = $access->readFloat();
        $this->assertInternalType('float', $val);
        $this->assertEquals(round(-M_PI, 3), round($val, 3));
        
        $val = $access->readFloat();
        $this->assertInternalType('float', $val);
        $this->assertEquals(round(M_2_PI, 3), round($val, 3));
    }
}

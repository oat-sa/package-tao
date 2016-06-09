<?php

use qtism\common\datatypes\files\FileSystemFile;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class FileSystemFileTest extends QtiSmTestCase {
    
    /**
     * @dataProvider retrieveProvider
     * 
     * @param string $path The path to the QTI file instance.
     */
    public function testRetrieve($path, $expectedFilename, $expectedMimeType, $expectedData) {
        $pFile = FileSystemFile::retrieveFile($path);
        $this->assertEquals($expectedFilename, $pFile->getFilename());
        $this->assertEquals($expectedMimeType, $pFile->getMimeType());
        $this->assertEquals($expectedData, $pFile->getData());
    }
    
    /**
     * @dataProvider createFromExistingFileProvider
     * 
     * @param string $source
     * @param string $destination
     * @param string $mimeType
     * @param boolean|string $withFilename
     */
    public function testCreateFromExistingFile($source, $mimeType, $withFilename = true) {
        $destination = tempnam('/tmp', 'qtism');
        $pFile = FileSystemFile::createFromExistingFile($source, $destination, $mimeType, $withFilename);
        
        $expectedContent = file_get_contents($source);
        
        if ($withFilename === true) {
            // Check if the name is the original one.
            $pathinfo = pathinfo($source);
            $this->assertEquals($pathinfo['basename'], $pFile->getFilename());
        }
        else {
            $this->assertEquals($withFilename, $pFile->getFilename());
        }
        
        $this->assertEquals($expectedContent, $pFile->getData());
        $this->assertEquals($mimeType, $pFile->getMimeType());
        
        unlink($destination);
    }
    
    /**
     * @dataProvider getStreamProvider
     * @depends testRetrieve
     * 
     * @param string $path
     * @param string $expectedData
     */
    public function testGetStream($path, $expectedData) {
        $pFile = FileSystemFile::retrieveFile($path);
        $stream = $pFile->getStream();
        
        $data = '';
        
        while (!feof($stream)) {
            $data .= fread($stream, 2048);
        }
        
        @fclose($fp);
        
        $this->assertEquals($expectedData, $data);
    }
    
    public function testInstantiationWrongPath() {
        $this->setExpectedException('\\RuntimeException');
        $pFile = new FileSystemFile('/qtism/test');
        $pFile->getFilename();
    }
    
    public function retrieveProvider() {
        return array(
            array(self::samplesDir() . 'datatypes/file/text-plain_name.txt', 'yours.txt', 'text/plain', ''),
            array(self::samplesDir() . 'datatypes/file/text-plain_noname.txt', '', 'text/plain', ''),
            array(self::samplesDir() . 'datatypes/file/text-plain_text_data.txt', 'text.txt', 'text/plain', 'Some text...'),
        );
    }
    
    public function getStreamProvider() {
        return array(
            array(self::samplesDir() . 'datatypes/file/text-plain_name.txt', ''),          
            array(self::samplesDir() . 'datatypes/file/text-plain_noname.txt', ''),
            array(self::samplesDir() . 'datatypes/file/text-plain_text_data.txt', 'Some text...'),
        );
    }
    
    public function createFromExistingFileProvider() {
        return array(
            array(self::samplesDir() . 'datatypes/file/raw/text.txt', 'text/plain', true),
            array(self::samplesDir() . 'datatypes/file/raw/text.txt', 'text/plain', 'new-name.txt'),
        );
    }
}

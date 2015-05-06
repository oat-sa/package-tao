<?php


use qtism\common\datatypes\files\FileSystemFileManager;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class FileSystemFileManagerTest extends QtiSmTestCase {
    
    public function testCreateFromFile() {
        $manager = new FileSystemFileManager();
        $mFile = $manager->createFromFile(self::samplesDir() . 'datatypes/file/raw/text.txt', 'text/plain', 'newname.txt');
        
        // Created in temp dir?
        $this->assertTrue(strpos($mFile->getPath(), sys_get_temp_dir()) !== false);
        
        $this->assertEquals('I contain some text...', $mFile->getData());
        $this->assertEquals('text/plain', $mFile->getMimeType());
        $this->assertEquals('newname.txt', $mFile->getFilename());
        
        unlink($mFile->getPath());
    }
    
    public function testCreateFromData() {
        $manager = new FileSystemFileManager();
        $file = $manager->createFromData('Some <em>text</em>...', 'text/html');
        
        $this->assertEquals('Some <em>text</em>...', $file->getData());
        $this->assertEquals('text/html', $file->getMimeType());
        
        $manager->delete($file);
    }
    
    public function testDelete() {
        $manager = new FileSystemFileManager();
        $mFile = $manager->createFromFile(self::samplesDir() . 'datatypes/file/raw/text.txt', 'text/plain', 'newname.txt');
        
        $this->assertTrue(is_file($mFile->getPath()));
        $manager->delete($mFile);
        $this->assertFalse(is_file($mFile->getPath()));
    }
    
    /**
     * @depends testDelete
     * @depends testCreateFromFile
     */
    public function testRetrieve() {
        $manager = new FileSystemFileManager();
        $mFile = $manager->createFromFile(self::samplesDir() . 'datatypes/file/raw/text.txt', 'text/plain', 'newname.txt');
        $mFile = $manager->retrieve($mFile->getIdentifier());
        $this->assertEquals('text/plain', $mFile->getMimeType());
        $this->assertEquals('newname.txt', $mFile->getFilename());
        $this->assertEquals('I contain some text...', $mFile->getData());
        $manager->delete($mFile);
    }
}
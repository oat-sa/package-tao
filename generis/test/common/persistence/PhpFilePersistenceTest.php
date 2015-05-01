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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */
namespace oat\generis\test\common\persistence;

use oat\generis\test\GenerisPhpUnitTestRunner;
use \common_persistence_Persistence;
use \common_persistence_PhpFileDriver;
use org\bovigo\vfs\vfsStream;


class PhpFilePersistenceTest extends GenerisPhpUnitTestRunner
{
    private $root;
    
    public function setUp()
    {
        $this->root = vfsStream::setup('data');
    }
    
    public function testGetPersistence(){
        $driver = common_persistence_Persistence::getPersistence('cache');
        $this->assertInstanceOf('common_persistence_KeyValuePersistence', $driver);
        
    }
    
    
    public function testConnect()
    {
        $params = array(
            'dir' => vfsStream::url('data'),
            'humanReadable' => true
        );
        $driver  = new common_persistence_PhpFileDriver(); 
        $persistence = $driver->connect('test',$params);
        $this->assertInstanceOf('common_persistence_KeyValuePersistence', $persistence);
        return $persistence;
    }
    /**
     * @depends testConnect
     * @expectedException     common_exception_NotImplemented
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param common_persistence_KeyValuePersistence $persistence
     */
    public function testSetException($persistence)
    {
        $persistence->set('emtpy','empty',6);
    }
    
    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param common_persistence_KeyValuePersistence $persistence
     */
    public function testSet($persistence)
    {
        $return = $persistence->set('fakeKeyName','value');
        $this->assertTrue($this->root->hasChild('fakeKeyName.php'));
        $content = $this->root->getChild('fakeKeyName.php')->getContent();
        $this->assertEquals("<?php return 'value';\n", $content);
        $this->assertTrue($return);
        
    }
    
    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param common_persistence_KeyValuePersistence $persistence
     */
    public function testGet($persistence)
    {
        $this->assertTrue($persistence->set('fakeKeyName','value'));
        $this->assertEquals('value', $persistence->get('fakeKeyName'));
    }
    
    
    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param common_persistence_KeyValuePersistence $persistence
     */
    public function testExists($persistence)
    {
        $this->assertFalse($persistence->exists('fakeKeyName'));
        $this->assertTrue($persistence->set('fakeKeyName','value'));
        $this->assertTrue($persistence->exists('fakeKeyName'));
        
    }
    
    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param common_persistence_KeyValuePersistence $persistence
     */
    public function testDel($persistence)
    {
       
        $this->assertTrue($persistence->set('fakeKeyName','value'));
        $this->assertTrue($persistence->exists('fakeKeyName'));
        $this->assertTrue($persistence->del('fakeKeyName'));
        $this->assertFalse($persistence->exists('fakeKeyName'));
    }
    
    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param common_persistence_KeyValuePersistence $persistence
     */
    public function testPurge($persistence)
    {
         
        $this->assertTrue($persistence->set('fakeKeyName','value'));
        $this->assertTrue($persistence->set('fakeKeyName2','value'));
        $this->assertTrue($persistence->exists('fakeKeyName'));
        $this->assertTrue($persistence->exists('fakeKeyName2'));
        $this->assertTrue($persistence->purge());
        $this->assertFalse($this->root->hasChildren());
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testNotHumanReadable(){
        $vfStream = vfsStream::setup('cache');
        $params = array(
            'dir' => vfsStream::url('cache'),
        );
        $driver = new common_persistence_PhpFileDriver();
        $persistence = $driver->connect('test',$params);
        
        $persistence->set('fakeKeyName', 'value');
        $this->assertEquals('value', $persistence->get('fakeKeyName'));
       
    }
}

?>
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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */
namespace oat\generis\test\common\cache;

use oat\generis\test\GenerisPhpUnitTestRunner;
use \common_cache_FileCache;

class CacheTest extends GenerisPhpUnitTestRunner {
	
    /**
     * @dataProvider keyProvider
     */
	public function testFileCache($key)
	{
	    $cache = common_cache_FileCache::singleton();
	    $this->assertFalse($cache->has($key));
	    $this->assertTrue($cache->put('data', $key));
	    $this->assertTrue($cache->has($key));
	    $this->assertEquals('data', $cache->get($key));
	    $this->assertTrue($cache->remove($key));
	    $this->setExpectedException('common_cache_NotFoundException');
	    $cache->get($key);
	    $this->assertFalse($cache->has($key));
	}
	
	
	public function keyProvider()
	{
	    return array(
	        array('normal'),
	        array('\' " {} \\ / and other strange chars : ~!@#$%^&*()_+?'),
	        array('_'),
	        array(':'),
	        array(' '),
	        array('')
	    );
	}
    
}
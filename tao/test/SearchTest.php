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
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\tao\model\search\SearchService;
use oat\tao\model\search\IndexService;
use oat\tao\model\search\tokenizer\RawValue;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class SearchTestCase extends TaoPhpUnitTestRunner {
    
    private $class;
    
    private $property;
    
    public function __construct() {
    }
	
    public function setUp()
    {		
        parent::setUp();
		TaoPhpUnitTestRunner::initTest();
		$rdfClass = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$this->class = $rdfClass->createSubClass('test class');
		$this->property = $this->class->createProperty('test property');
	}
    
    public function tearDown() {
        parent::tearDown();
        $this->class->delete();
        $this->property->delete();
    }
    
    public function testSearchService()
    {
        $implementation = SearchService::getSearchImplementation();
        $this->assertIsA($implementation, 'oat\tao\model\search\Search');
    }
    
    public function testRunIndex()
    {
        $count = SearchService::runIndexing();
        $this->assertTrue(is_numeric($count), 'Indexing did not return a numeric value');
    }
    
    public function testCreateIndex()
    {
        $tokenizer = new core_kernel_classes_Resource(RawValue::URI);
        $id = 'test_index_'.helpers_Random::generateString(8);
        
        $index = IndexService::createIndex($this->property, $id, $tokenizer, true, true);
        
        $this->assertIsA($index, 'oat\tao\model\search\Index');
        $this->assertTrue($index->exists());
        
        $indexToo = IndexService::getIndexById($id);
        $this->assertIsA($indexToo, 'oat\tao\model\search\Index');
        $this->assertTrue($index->equals($indexToo));
        
        $this->assertEquals($id, $index->getIdentifier());
        $this->assertTrue($index->isDefaultSearchable());
        $this->assertTrue($index->isFuzzyMatching());
        
        $tokenizer = $index->getTokenizer();
        $this->assertIsA($tokenizer, 'oat\tao\model\search\tokenizer\Tokenizer');
        
        $indexes = IndexService::getIndexes($this->property);
        $this->assertTrue(is_array($indexes));
        $this->assertEquals(1, count($indexes));
        
        $indexToo = reset($indexes);
        $this->assertIsA($indexToo, 'oat\tao\model\search\Index');
        $this->assertTrue($index->equals($indexToo));

        return $index;
    }

    /**
     * @expectedException common_Exception
     * @depends testCreateIndex
     */
    public function testDublicateCreate($index)
    {
        $this->assertIsA($index, 'oat\tao\model\search\Index');
        
        $tokenizer = new core_kernel_classes_Resource(RawValue::URI);
        IndexService::createIndex($this->property, $index->getIdentifier(), $tokenizer, true, true);
    }
    
    /**
     * @depends testCreateIndex
     */
    public function testCreateSimilar($index)
    {
        $this->assertIsA($index, 'oat\tao\model\search\Index');
        
        $tokenizer = new core_kernel_classes_Resource(RawValue::URI);
        $similar = IndexService::createIndex($this->property, substr($index->getIdentifier(), 0, -2), $tokenizer, true, true);
        $this->assertIsA($similar, 'oat\tao\model\search\Index');
        
        return $similar;
    }
    
    /**
     * @depends testCreateSimilar
     */
    public function testDeleteSimilar($index)
    {
        $this->assertIsA($index, 'oat\tao\model\search\Index');
        $this->assertTrue($index->exists());
        $index->delete();
        $this->assertFalse($index->exists());
    }
        
    
    /**
     * @depends testCreateIndex
     * @depends testCreateSimilar
     * @depends testDublicateCreate
     */
    public function testDeleteIndex($index)
    {
        $this->assertIsA($index, 'oat\tao\model\search\Index');
        $this->assertTrue($index->exists());
        $index->delete();
        $this->assertFalse($index->exists());
    }
}
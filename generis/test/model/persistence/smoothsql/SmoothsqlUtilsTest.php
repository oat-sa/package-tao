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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2014 (update and modification) 2012-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\generis\test\model\persistence\smoothsql;

use oat\generis\test\GenerisPhpUnitTestRunner;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Literal;
use \core_kernel_persistence_smoothsql_Utils;
use oat\generis\model\data\ModelManager;

class SmootsqlUtilsTest extends GenerisPhpUnitTestRunner {
    
    /**
     * @dataProvider buildSearchPatternProvider
     * 
     * @param unknown_type $pattern
     * @param unknown_type $like
     * @param unknown_type $expected
     */
	public function testBuildSearchPattern($pattern, $like, $expected)
	{
	    $this->assertSame($expected, core_kernel_persistence_smoothsql_Utils::buildSearchPattern($this->getModel()->getPersistence(), $pattern, $like));
	}
	
	public function buildSearchPatternProvider()
	{
	    return array(
	        array('hello', false, '= ' . $this->quote('hello')),
	        array('hello', true, 'LIKE ' . $this->quote('%hello%')),
	        array('*hello', true, 'LIKE ' . $this->quote('%hello')),
	        array('*hello*', true, 'LIKE ' . $this->quote('%hello%')),
	        array('*hel*lo*', true, 'LIKE ' . $this->quote('%hel%lo%')),
	        array('*hel*lo*', false, '= ' . $this->quote('*hel*lo*')),
	        array(25, false, '= ' . $this->quote('25')),
	        array(25.123, false, '= ' . $this->quote('25.123')),
	        array(true, false, '= ' . $this->quote('1')),
	        array(false, false, '= ' . $this->quote('')),
	        array(false, true, 'LIKE ' . $this->quote('%%')),
	        array('', true, 'LIKE ' . $this->quote('%%')),
	        array(new core_kernel_classes_Resource('http://www.13.com/ontology#toto'), false, '= ' . $this->quote('http://www.13.com/ontology#toto')),
	        array(new core_kernel_classes_Resource('http://www.13.com/ontology#toto'), true, '= ' . $this->quote('http://www.13.com/ontology#toto')),
	    );
	}
	
	/**
	 * @dataProvider buildPropertyQueryProvider
	 * 
	 * @param unknown_type $propertyUri
	 * @param unknown_type $values
	 * @param unknown_type $like
	 * @param unknown_type $lang
	 */
	public function testBuildPropertyQuery($expected, $propertyUri, $values, $like, $lang = '')
	{
	    $query = core_kernel_persistence_smoothsql_Utils::buildPropertyQuery($this->getModel(), $propertyUri, $values, $like, $lang);
	    $queryWithoutModelRestrictions = substr($query, 0, strlen($expected));
	    $this->assertSame($expected, $queryWithoutModelRestrictions);
	    $this->assertSame(' AND modelid IN (', substr($query, strlen($expected), strlen(' AND modelid IN (')));
	}
	
	public function buildPropertyQueryProvider()
	{
	    return array(
	        array(
	            "SELECT DISTINCT subject FROM statements WHERE (predicate = " . $this->quote('http://www.13.com/ontology#prop') . ") AND (object = " . $this->quote('hello') . ")",
	            'http://www.13.com/ontology#prop', 
	            'hello',
	            false
	        ),
	        array(
	            "SELECT DISTINCT subject FROM statements WHERE (predicate = " . $this->quote('http://www.13.com/ontology#prop') . ") AND (object = " . $this->quote('hello') . " OR object = " . $this->quote('world') . ")",
	            'http://www.13.com/ontology#prop',
	            array('hello', 'world'), 
	            false
	       ),
	        array(
	            "SELECT DISTINCT subject FROM statements WHERE (predicate = " . $this->quote('http://www.13.com/ontology#prop') . ") AND (object LIKE " . $this->quote('%hello%') . " OR object LIKE " . $this->quote('%world%') . ")",
	            'http://www.13.com/ontology#prop',
	            array('hello', 'world'), 
	            true
	       ),
	        array(
	            "SELECT DISTINCT subject FROM statements WHERE (predicate = " . $this->quote('http://www.13.com/ontology#prop') . ") AND (object = " . $this->quote('hello') . " AND (l_language = " . $this->quote('') . " OR l_language = " . $this->quote('en-US') . "))",         
	            'http://www.13.com/ontology#prop',
	            'hello', 
	            false,
	            'en-US'
	        ),
	    );
	}
	
	/**
	 * @dataProvider buildUnionQueryProvider
	 * 
	 * @param array $queries
	 * @param unknown_type $expected
	 */
	public function testBuildUnionQuery(array $queries, $expected)
	{
	    $this->assertSame($expected, core_kernel_persistence_smoothsql_Utils::buildUnionQuery($queries));
	}
	
	public function buildUnionQueryProvider()
	{
	    return array(
	        array(
	            array(
	                core_kernel_persistence_smoothsql_Utils::buildPropertyQuery($this->getModel(), 'http://www.13.com/ontology#prop1', 'toto', false),
	                core_kernel_persistence_smoothsql_Utils::buildPropertyQuery($this->getModel(), 'http://www.13.com/ontology#prop2', 'tata', false),
	            ),
	            '(' . core_kernel_persistence_smoothsql_Utils::buildPropertyQuery($this->getModel(), 'http://www.13.com/ontology#prop1', 'toto', false) . ') UNION ALL (' . core_kernel_persistence_smoothsql_Utils::buildPropertyQuery($this->getModel(), 'http://www.13.com/ontology#prop2', 'tata', false) . ')'
	        ),
	        array(
	            array(
	                core_kernel_persistence_smoothsql_Utils::buildPropertyQuery($this->getModel(), 'http://www.13.com/ontology#prop1', 'toto', false)       
	            ),
	            core_kernel_persistence_smoothsql_Utils::buildPropertyQuery($this->getModel(), 'http://www.13.com/ontology#prop1', 'toto', false)
	        ),
	        array(array(), false)
	    );
	}
	
	public function buildFilterQueryProvider() {
	    return array(
	        array(
	            'proot',
	            'http://www.taotesting.com/movies.rdf#Movie',
	            array(
	                'http://www.w3.org/2000/01/rdf-schema#label' => new core_kernel_classes_Literal('Dallas')
	            )                
	        ),
            array(
                'proot',
                'http://www.taotesting.com/movies.rdf#Movie',
                array(
                    'http://www.w3.org/2000/01/rdf-schema#label' => 'Dallas',
                    'http://www.taotesting.com/movies.rdf#year' => '2013'
                ),
                true, false, '', 0, 10
            ),
            array(
                'proot',
                'http://www.taotesting.com/movies.rdf#Movie',
                array(
                    'http://www.taotesting.com/movies.rdf#year' => '2013'
                ),
                true, true, 'en-US', 0, 15, 'http://www.w3.org/2000/01/rdf-schema#label', 'DESC'
            )
	    );
	}
	
	/**
	 * @return \core_kernel_persistence_smoothsql_SmoothModel
	 */
	private function getModel() {
	    return ModelManager::getModel();
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	private function quote($string) {
	    return $this->getModel()->getPersistence()->quote($string);
	}
}
<?php
/*  
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
 *               2013      (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
use oat\generis\test\GenerisPhpUnitTestRunner;

/**
 * Test of the DbWrappers.
 * 
 * @author Jerome Bogaerts <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class DbWrapperTest extends GenerisPhpUnitTestRunner {

    protected function setUp(){
        GenerisPhpUnitTestRunner::initTest();
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        //TODO need to connect to a dbWrapper a function createTable that currently not exists
        $dbWrapper->exec('
                CREATE TABLE "dbTestCase" (
                    "id" INT,
                    "uri" VARCHAR(255) NOT NULL,
                    "coluMn1" VARCHAR(255) )'
        );
        for($i = 0;$i<4;$i++){
            $dbWrapper->exec('INSERT INTO  "dbTestCase" (id,uri,"coluMn1") VALUES (?,?,?) ;',array($i,'http://uri'.$i,'value'.$i));
        }
	}


    public function testGetRowCount(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$rowCount = $dbWrapper->getRowCount('dbTestCase');
		$this->assertTrue(is_int($rowCount));
		$this->assertTrue($rowCount == 4);
        $dbWrapper->exec('INSERT INTO "dbTestCase" (id,uri,"coluMn1") VALUES (?,?,?) ;',array('12','http://uri','value'));
        $rowCount = $dbWrapper->getRowCount('dbTestCase');
        $this->assertTrue(is_int($rowCount));
        $this->assertTrue($rowCount == 5);
	}

	public function testGetColumnNames(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$columns = $dbWrapper->getColumnNames('dbTestCase');
        $this->assertEquals(count($columns),3);
        $possibleValues = array('id','uri','coluMn1');
        foreach ($columns as $col){
            if($col instanceof Doctrine\DBAL\Schema\Column){          
                $this->assertTrue(in_array($col->getName(),$possibleValues),$col->getName() . ' is not a correct value');
            }
            else {
                //legacy mode
                $this->assertTrue(in_array($col,$possibleValues));
            }
        }

	}

    public function testGetTables(){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $tables = $dbWrapper->getTables();
        $this->assertTrue(count($tables) > 1);
        $this->assertTrue(in_array('statements', $tables));
    }

    public function testLimitStatements(){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $query = 'SELECT * from "dbTestCase" ';
        $results = $dbWrapper->query($query);
        $count = $results->rowCount();
        $this->assertTrue($count==4);

        // limit to 2 offset from 1
        $query2 = $dbWrapper->limitStatement($query, 2, 1);
        $results2 = $dbWrapper->query($query2);
        $count2 = $results2->rowCount();

        $this->assertTrue($count2==2);
        $i =0;
        foreach($results2 as $value){
            // let's go to offset 1
            $i++;
            $this->assertTrue(is_array($value));
            $this->assertTrue($value['id'] == $i);
            $this->assertTrue($value['uri'] == 'http://uri'.$i);
            $this->assertTrue($value['coluMn1'] == 'value'.$i);
        }



    }
    

    public function testCreateIndex(){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $dbWrapper->getSchemaManager();
        
        $schema = new \Doctrine\DBAL\Schema\Schema();
        $table = $schema->createTable('dbtestcase2');
        $table->addColumn("id", "integer",array("notnull" => true,"autoincrement" => true));
        $table->setPrimaryKey(array("id"));
        $table->addColumn("uri", "string",array("length" => 255, "notnull" => true));
        $table->addColumn("content", "text",array("notnull" => false));
        
        
        $sql = $dbWrapper->getPlatform()->schemaToSql($schema);
        

        foreach ($sql as $q){
            $dbWrapper->exec($q);    
        }
        $dbWrapper->createIndex('idx_content', $table->getName(), array("content" => 255));

        $indexes = $dbWrapper->getSchemaManager()->getTableIndexes('dbtestcase2');
        foreach($indexes as $index){
            $this->assertTrue(in_array($index->getName(),array('idx_content','dbtestcase2_pkey','PRIMARY')),$index->getName() . 'is missing');
            
        }
        
        $dbWrapper->exec('DROP TABLE dbtestcase2');
    }
    
    protected function tearDown(){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        //TODO need to connect to a dbWrapper a function dropTable that currently not exists
        $dbWrapper->exec('DROP TABLE "dbTestCase";');
    }
}

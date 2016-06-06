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
 * 
 */

// THIS FILE MUST BE UTF-8 encoded to get the TestCase working !!!
// PLEASE BE CAREFULL.

use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

class AdaptersTestCase extends TaoPhpUnitTestRunner {

	const CSV_FILE_USERS_HEADER_UNICODE = '/samples/csv/users1-header.csv';
	const CSV_FILE_USERS_HEADER_UNICODE_WITH_MULTI_FIELD_VALUE = '/samples/csv/users1-header-multi-field-values.csv';
	const CSV_FILE_USERS_NO_HEADER_UNICODE = '/samples/csv/users1-no-header.csv';
	
	public function testGenerisAdapterCsv() {
		// First test: instantiate a generis CSV adapter and load a file.
		// Let the default options rule the adapter.
		
	}
	
	public function testCsvFileParsing() {
		// + Subtest 1: Unicode CSV file with header row.
		// --------------------------------------------------------------------------------
		$path = dirname(__FILE__) . self::CSV_FILE_USERS_HEADER_UNICODE;
		$csvFile = new tao_helpers_data_CsvFile();
		$csvFile->load($path);
		
		// - test column mapping.
		$expectedMapping = array('label', 'First Name', 'Last Name',
								 'Login', 'Mail', 'password', 'UserUILg');
		$this->assertEquals($expectedMapping, $csvFile->getColumnMapping(), 'The column mapping should be ' . var_export($expectedMapping, true) . '.');
		$this->assertEquals($csvFile->count(), 16, 'The CSV file contains ' . $csvFile->count() . ' rows instead of 16.');
		$this->assertEquals($csvFile->getColumnCount(), 7, 'The CSV file contains ' . $csvFile->getColumnCount() . ' columns instead of 7.');
		
		// - test some row retrievals.
		$expectedRow = array('TAO Jérôme Bogaerts',
							 'Jérôme',
							 'Bogaerts',
							 'jbogaerts',
							 'jerome.bogaerts@tudor.lu',
							 'jbogaerts',
							 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
		$this->assertEquals($csvFile->getRow(0), $expectedRow);
		
		$expectedRow = array('label' => 'TAO Isabelle Jars',
							 'First Name' => 'Isabelle',
							 'Last Name' => 'Jars',
							 'Login' => 'ijars',
							 'Mail' => 'isabelle.jars@tudor.lu',
							 'password' => 'ijars',
							 'UserUILg' => 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
		$this->assertEquals($csvFile->getRow(4, true), $expectedRow);
		
		
		// + Subtest 2: Unicode CSV file withouth header row.
		// --------------------------------------------------------------------------------
		$path = dirname(__FILE__) . self::CSV_FILE_USERS_NO_HEADER_UNICODE;
		$csvFile = new tao_helpers_data_CsvFile($options = array('first_row_column_names' => false));
		$csvFile->load($path);
		
		// - test column mapping.
		$expectedMapping = array();
		$this->assertEquals($expectedMapping, $csvFile->getColumnMapping(), 'The column mapping should be ' . var_export($expectedMapping, true) . '.');
		$this->assertEquals($csvFile->count(), 16, 'The CSV file contains ' . $csvFile->count() . ' rows instead of 16.');
		$this->assertEquals($csvFile->getColumnCount(), 7, 'The CSV file contains ' . $csvFile->getColumnCount() . ' columns instead of 7.');
		
		// - test some row retrievals.
		$expectedRow = array('TAO Jérôme Bogaerts',
							 'Jérôme',
							 'Bogaerts',
							 'jbogaerts',
							 'jerome.bogaerts@tudor.lu',
							 'jbogaerts',
							 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
		$this->assertEquals($csvFile->getRow(0), $expectedRow);
		
		$expectedRow = array('TAO Matteo Mellis',
							 'Matteo',
							 'Mellis',
							 'mmellis',
							 'matteo.mellis@tudor.lu',
							 'mmellis',
							 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
		$this->assertEquals($csvFile->getRow(15), $expectedRow);
	}

	public function testCSVFileParsingWithMultipleFieldValues() {

        $path = dirname(__FILE__) . self::CSV_FILE_USERS_HEADER_UNICODE_WITH_MULTI_FIELD_VALUE;
        // Without multi values
        //---------------------------------------------------------------------------------
        $csvFile = new tao_helpers_data_CsvFile();
        $csvFile->load($path);

        // - test column mapping.
        $expectedMapping = array('label', 'First Name', 'Last Name',
            'Login', 'Mail', 'password', 'UserUILg');
        $this->assertEquals($expectedMapping, $csvFile->getColumnMapping(), 'The column mapping should be ' . var_export($expectedMapping, true) . '.');
        $this->assertEquals($csvFile->count(), 16, 'The CSV file contains ' . $csvFile->count() . ' rows instead of 16.');
        $this->assertEquals($csvFile->getColumnCount(), 7, 'The CSV file contains ' . $csvFile->getColumnCount() . ' columns instead of 7.');

        // - test some row retrievals.
        $expectedRow = array('TAO Jérôme Bogaerts',
            'Jérôme',
            'Bogaerts',
            'jbogaerts',
            'jerome.bogaerts@tudor.lu|jerome@example.com',
            'jbogaerts',
            'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
        $this->assertEquals($csvFile->getRow(0), $expectedRow);

        $expectedRow = array('label' => 'TAO Isabelle Jars',
            'First Name' => 'Isabelle',
            'Last Name' => 'Jars',
            'Login' => 'ijars',
            'Mail' => 'isabelle.jars@tudor.lu',
            'password' => 'ijars',
            'UserUILg' => 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
        $this->assertEquals($csvFile->getRow(4, true), $expectedRow);

        $expectedRow = array('label' => 'TAO Igor Ribassin',
            'First Name' => 'Igor',
            'Last Name' => 'Ribassin',
            'Login' => 'iribassin',
            'Mail' => 'igor.ribassin@tudor.lu',
            'password' => 'iribassin',
            'UserUILg' => 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN|http://www.tao.lu/Ontologies/TAO.rdf#LangAR|http://www.tao.lu/Ontologies/TAO.rdf#LangDE');
        $this->assertEquals($csvFile->getRow(13, true), $expectedRow);


        // With multi values
        //---------------------------------------------------------------------------------
        $csvFile = new tao_helpers_data_CsvFile(['multi_values_delimiter' => '|']);
        $csvFile->load($path);

        // - test column mapping.
        $expectedMapping = array('label', 'First Name', 'Last Name',
            'Login', 'Mail', 'password', 'UserUILg');
        $this->assertEquals($expectedMapping, $csvFile->getColumnMapping(), 'The column mapping should be ' . var_export($expectedMapping, true) . '.');
        $this->assertEquals($csvFile->count(), 16, 'The CSV file contains ' . $csvFile->count() . ' rows instead of 16.');
        $this->assertEquals($csvFile->getColumnCount(), 7, 'The CSV file contains ' . $csvFile->getColumnCount() . ' columns instead of 7.');

        // - test some row retrievals.
        $expectedRow = array('TAO Jérôme Bogaerts',
            'Jérôme',
            'Bogaerts',
            'jbogaerts',
            ['jerome.bogaerts@tudor.lu', 'jerome@example.com'],
            'jbogaerts',
            'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
        $this->assertEquals($csvFile->getRow(0), $expectedRow);

        $expectedRow = array('label' => 'TAO Isabelle Jars',
            'First Name' => 'Isabelle',
            'Last Name' => 'Jars',
            'Login' => 'ijars',
            'Mail' => 'isabelle.jars@tudor.lu',
            'password' => 'ijars',
            'UserUILg' => 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
        $this->assertEquals($csvFile->getRow(4, true), $expectedRow);

        $expectedRow = array('label' => 'TAO Igor Ribassin',
            'First Name' => 'Igor',
            'Last Name' => 'Ribassin',
            'Login' => 'iribassin',
            'Mail' => 'igor.ribassin@tudor.lu',
            'password' => 'iribassin',
            'UserUILg' => ['http://www.tao.lu/Ontologies/TAO.rdf#LangEN','http://www.tao.lu/Ontologies/TAO.rdf#LangAR','http://www.tao.lu/Ontologies/TAO.rdf#LangDE']);
        $this->assertEquals($csvFile->getRow(13, true), $expectedRow);

    }
}

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

require_once dirname(__FILE__) . '/GenerisPhpUnitTestRunner.php';

class RdfExportTest extends GenerisPhpUnitTestRunner {
    
	public function testFullExport(){
	    
	    $dbWrapper = core_kernel_classes_DbWrapper::singleton();
	    $result = $dbWrapper->query('SELECT count(*) FROM "statements"')->fetch();
	    $triples = $result[0];

		$result = $dbWrapper->query('SELECT modelid FROM "models"');
	    $modelIds = array();
	    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
	        $modelIds[] = $row['modelid'];
	    }
	    $xml = core_kernel_api_ModelExporter::exportModels($modelIds);
	    
	    $doc = new DOMDocument();
	    $doc->loadXML($xml);
	    
	    $count = 0;
	    $descriptions = $doc->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'Description');
	    foreach ($descriptions as $description){
	        foreach ($description->childNodes as $child) {
	            if ($child instanceof DOMElement) {
	                $count++;
	            }
	        }
	    }
	    
	    $this->assertEquals($triples, $count);
	}

}
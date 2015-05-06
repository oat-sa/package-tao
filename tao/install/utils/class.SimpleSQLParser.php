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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * A simple XML parser that is able to parse SQL files with Simple statements.
 * Please note that complex statements like Procedure or Functions are not
 * supported by this implementation.
 * 
 * @author Jerome BOGAERTS <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_install_utils_SimpleSQLParser extends tao_install_utils_SQLParser{
	
	/**
	 * Parses a SQL file containing simple statements.
	 * @return void
	 * @throws tao_install_utils_SQLParsingException
	 */
	public function parse(){
		$this->setStatements(array());
		
		//common file checks
		$file = $this->getFile();
		if (!file_exists($file)){
			throw new tao_install_utils_SQLParsingException("SQL file '${file}' does not exist.");
		}
		else if (!is_readable($file)){
			throw new tao_install_utils_SQLParsingException("SQL file '${file}' is not readable.");
		}
		else if(!preg_match("/\.sql$/", basename($file))){
			throw new tao_install_utils_SQLParsingException("File '${file}' is not a valid SQL file. Extension '.sql' not found.");
		}
		
		if ($handler = fopen($file, "r")){
			
			//parse file and get only usefull lines
			$ch = "";
			while (!feof ($handler)){
				$line = utf8_decode(fgets($handler));
		
				if (isset($line[0]) && ($line[0] != '#') && ($line[0] != '-')){
					$ch = $ch.$line;
				}
			}
			
			//explode and execute
			$requests = explode(";", $ch);
			
			try{
				foreach($requests as $index => $request){
					$requestTrim = trim($request);
					if(!empty($requestTrim)){
						$this->addStatement($request);
					}
				}
			}
			catch(Exception $e){
				throw new tao_install_utils_SQLParsingException("Error executing query #$index : $request . ".$e->getMessage());
			}
			fclose($handler);
		}
	}
}
?>
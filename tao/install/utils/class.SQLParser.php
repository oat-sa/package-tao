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
 * An abstract SQL Parser.
 * Concrete SQL Parser for installation should implement this class.
 * 
 * @author Jerome BOGARTS <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
abstract class tao_install_utils_SQLParser implements tao_install_utils_Parser {
	
	private $file;
	private $statements;
	
	/**
	 * Creates a new instance of tao_install_utils_SQLParser.
	 */
	public function __construct($file = 'unknown_path'){
		$this->setFile($file);
		$this->setStatements(array());
	}
	
	/**
	 * Sets the path to the SQL file that has to be parsed.
	 * @param string $file The file path to the SQL file to parse.
	 * @return void
	 */
	public function setFile($file){
		$this->file = $file;
	}
	
	/**
	 * Gets the path to the SQL file that has to be parsed.
	 * @return string The the path the SQL file that has to be parsed.
	 */
	public function getFile(){
		return $this->file;
	}
	
	/**
	 * Sets the array of string that represents the parsed SQL statements.
	 * @param array $statements an array of SQL statements as strings.
	 * @return void
	 */
	protected function setStatements(array $statements){
		$this->statements = $statements;
	}
	
	/**
	 * Gets the array of SQL statements parsed by the parser. It is empty
	 * until the first call of the parse() method or if no SQL statements
	 * were found.
	 * @return array an array of SQL statements as string.
	 */
	public function getStatements(){
		return $this->statements;
	}
	
	/**
	 * Adds a statement to the collection of parsed SQL statements.
	 * @param string $statement The SQL statement to add.
	 * @return void
	 */
	protected function addStatement($statement){
		array_push($this->statements, $statement);
	}
}
?>
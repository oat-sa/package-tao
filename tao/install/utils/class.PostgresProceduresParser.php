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
 * This SQL Parser is able to deal with Functions
 * for PostgresSQL in a compliant SQL file.
 * 
 * @author Jerome BOGAERTS <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_install_utils_PostgresProceduresParser extends tao_install_utils_SQLParser{
	
	/**
	 * Parse a SQL file containing mySQL compliant Procedures or Functions.
	 * @return void
	 * @throws tao_install_utils_SQLParsingException
	 */
	public function parse(){
		$this->setStatements(array());
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
		
		$content = @file_get_contents($file);
		if ($content !== false){
			$matches = array();
			$patterns = array('CREATE\s*(?:OR\s+REPLACE){0,1}\s*FUNCTION\s+(?:.*\s)*?END\s*.*\s*;(?:\s*.*?\s*LANGUAGE\s+.*\s*;)*');
							
			if (preg_match_all('/' . implode($patterns, '|') . '/i', $content, $matches)){
				foreach ($matches[0] as $match){
					$this->addStatement($match);
				}
			}
		}
		else{
			throw new tao_install_utils_SQLParsingException("SQL file '${file}' cannot be read. An unknown error occured while reading it.");	
		}
	}
}
?>
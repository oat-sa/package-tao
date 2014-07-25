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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * This class contains some helpers in order to facilitate the creation of complex tests
 *
 * @author Joel Bout, <taosupport@tudor.lu>
 * @author Jehan Bihin
 * @package ClearFw
 * @subpackage core
 * @subpackage helpers
 */


class TestCasePrototype extends UnitTestCase {

	private $files = array();

	/**
	 * Create a new temporary file
	 * @param string $pContent
	 */
	public function createFile($pContent = '', $name = null) {
		if (is_null($name)) {
			$tmpfname = tempnam(sys_get_temp_dir(), "tst");
		} else {
			$tmpfname = sys_get_temp_dir().DIRECTORY_SEPARATOR.$name;	
		}
		$this->files[] = $tmpfname;

		if (!empty($pContent)) {
			$handle = fopen($tmpfname, "w");
			fwrite($handle, $pContent);
			fclose($handle);
		}

		return $tmpfname;
	}

	/**
	 * Cleanup of files
	 * @see SimpleTestCase::after()
	 */
	public function after($method) {
		parent::after($method);
		foreach ($this->files as $file) {
			@unlink($file);
		}
		$this->files = array();
	}

}
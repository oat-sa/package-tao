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
class FillVars extends Module
{
	public function index()
	{
		$this->setData('str_val', 'A string appears');
		$this->setData('int_val', 10);
		$this->setData('array_val', array('banana', 'apple', 'orange'));
		
		$this->setView('fillVarsIndex.tpl');
	}
	
	public function get($a, $b, $c)
	{
		$this->setData('get_val_1', $a);
		$this->setData('get_val_2', $b);
		$this->setData('get_val_3', $c);
		
		$this->setView('fillVarsGet.tpl');
	}
}
?>
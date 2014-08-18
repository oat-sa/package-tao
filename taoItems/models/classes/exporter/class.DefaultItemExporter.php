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
?>
<?php
//TODO : to be generated !
class taoItems_models_classes_exporter_DefaultItemExporter extends taoItems_models_classes_ItemExporter {

	public function export($options = array()) {
		
		$zipToRoot = isset($options['zipToRoot'])?(bool)$options['zipToRoot']:false;
		
		$location = $this->getItemLocation();
		if(is_dir(realpath($location))){
			if($zipToRoot){
				$this->addFile($location, '');
			}else{
				$this->addFile($location, basename($location));
			}
		}
	}
	
}
?>
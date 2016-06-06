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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 * 
 */

use oat\generis\model\kernel\persistence\file\FileIterator;

/**
 * Model of the extension
 *
 * @author Joel Bout
 * @package generis
 * @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class common_ext_ExtensionModel extends AppendIterator
{
    public function __construct(common_ext_Extension $extension) {
        parent::__construct();
        $this->addModelFiles($extension);
    }
    
    public function addModelFiles($extension) {
        foreach ($extension->getManifest()->getInstallModelFiles() as $rdfpath) {
            if (!file_exists($rdfpath)) {
                throw new common_ext_InstallationException("Unable to load ontology in '${rdfpath}' because the file does not exist.");
            }
        
            if (!is_readable($rdfpath)) {
                throw new common_ext_InstallationException("Unable to load ontology in '${rdfpath}' because the file is not readable.");
            }
            
            $iterator = new FileIterator($rdfpath);
            $this->append($iterator->getIterator());
        }
    }
}

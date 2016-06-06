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
 *
 */

namespace oat\taoTests\scripts\update;


class Updater extends \common_ext_ExtensionUpdater 
{
	/**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion)
    {
        
        if ($this->isBetween('0', '2.7')){
            $this->setVersion('2.7');
        }
		// remove active prop
		if ($this->isVersion('2.7')){
		    $deprecatedProperty = new \core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#active');
		    $iterator = new \core_kernel_classes_ResourceIterator(array(\taoTests_models_classes_TestsService::singleton()->getRootClass()));
		    foreach ($iterator as $resource) {
		        $resource->removePropertyValues($deprecatedProperty);
		    }
		    $this->setVersion('2.7.1');
		}

		$this->skip('2.7.1','2.15.0');
        
		return null;
	}
}

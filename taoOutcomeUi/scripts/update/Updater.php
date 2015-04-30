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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoOutcomeUi\scripts\update;

use oat\generis\model\data\ModelManager;

/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater {
    
    /**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {

        $currentVersion = $initialVersion;

        // move ResultsManagerRole to model 1
        if ($currentVersion == '2.6') {
            $rdf = ModelManager::getModel()->getRdfInterface();
            $toChange = array();
            foreach ($rdf as $triple) {
                if ($triple->subject == 'http://www.tao.lu/Ontologies/TAOResult.rdf#ResultsManagerRole') {
                    $toChange[] = $triple;
                }
            }
            foreach ($toChange as $triple) {
                $rdf->remove($triple);
                $triple->modelid = 1;
                $rdf->add($triple);
            }
            $currentVersion = '2.6.1';
        }

        return $currentVersion;
    }

}

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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\taoResultServer\models\classes\CrudResultsService;

class taoResultServer_actions_RestResults extends \tao_actions_CommonRestModule {

    public function __construct(){
        parent::__construct();
        $this->service = CrudResultsService::singleton();
    }

    /**
     * Optionnaly a specific rest controller may declare
     * aliases for parameters used for the rest communication
     */
    protected function getParametersAliases(){
        return array_merge(parent::getParametersAliases(), array(

            ));
    }
    /**
     * Optionnal Requirements for parameters to be sent on every service
     *
     */
    protected function getParametersRequirements() {
        return array(
            /** you may use either the alias or the uri, if the parameter identifier
             *  is set it will become mandatory for the operation in $key
             * Default Parameters Requirents are applied
             * type by default is not required and the root class type is applied
             */
        );
    }

}
?>

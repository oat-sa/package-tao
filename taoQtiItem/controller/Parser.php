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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\taoQtiItem\controller;

use \tao_actions_CommonModule;
use \common_Exception;
use oat\taoQtiItem\helpers\Authoring;
use oat\taoQtiItem\model\qti\Parser as QtiParser;

/**
 * QtiCreator Controller provide actions to edit a QTI item
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQtiItem

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Parser extends tao_actions_CommonModule
{

    public function getJson(){

        $returnValue = array(
            'itemData' => null
        );

        $xml = file_get_contents('php://input');
        $xml = Authoring::validateQtiXml($xml);

        $qtiParser = new QtiParser($xml);
        $item = $qtiParser->load();

        if(!is_null($item)){
            $returnValue['itemData'] = $item->toArray();
        }else{
            throw new common_Exception('invalid qti xml');
        }

        $this->returnJson($returnValue);
    }

}
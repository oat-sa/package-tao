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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model;

use common_Logger;
use common_report_Report;
use core_kernel_classes_Resource;
use oat\taoQtiItem\model\pack\QtiItemPacker;
use oat\taoQtiItem\model\qti\exception\XIncludeException;
use oat\taoQtiItem\model\qti\Parser;
use oat\taoQtiItem\model\qti\Service;

/**
 * The QTI Json Item Compiler
 *
 * @access public
 * @author Antoine Robin
 * @package taoItems
 */
class QtiJsonItemCompiler extends QtiItemCompiler
{

    const ITEM_FILE_NAME = 'item.json';
    const VAR_ELT_FILE_NAME = 'variableElements.json';

    /**
     * @var string json from the item packed
     */
    private $itemJson;

    /**
     * Desploy all the required files into the provided directories
     *
     * @param core_kernel_classes_Resource $item
     * @param string $language
     * @param string $publicDirectory
     * @param string $privateFolder
     * @return common_report_Report
     */
    protected function deployQtiItem(core_kernel_classes_Resource $item, $language, $publicDirectory, $privateFolder)
    {
        //start debugging here
        common_Logger::d('destination original ' . $publicDirectory . ' ' . $privateFolder);

        $qtiService = Service::singleton();


        // retrieve the media assets
        try {
            $qtiItem = $this->retrieveAssets($item, $language, $publicDirectory);

            //store variable qti elements data into the private directory
            $variableElements = $qtiService->getVariableElements($qtiItem);
            $serializedVarElts = json_encode($variableElements);
            file_put_contents($privateFolder . self::VAR_ELT_FILE_NAME, $serializedVarElts);

            //create the item.json file in private directory
            $itemPacker = new QtiItemPacker();
            $itemPacker->setReplaceXinclude(false);
            $itemPack = $itemPacker->packQtiItem($item, $language, $qtiItem);
            $this->itemJson = $itemPack->JsonSerialize();
            //get the filtered data to avoid cheat
            $data = $qtiItem->getDataForDelivery();
            $this->itemJson['data'] = $data['core'];

            file_put_contents($privateFolder . self::ITEM_FILE_NAME, json_encode($this->itemJson));

            return new common_report_Report(
                common_report_Report::TYPE_SUCCESS, __('Successfully compiled "%s"', $language)
            );

        } catch (\tao_models_classes_FileNotFoundException $e) {
            return new common_report_Report(
                common_report_Report::TYPE_ERROR, __('Unable to retrieve asset "%s"', $e->getFilePath())
            );
        } catch (XIncludeException $e) {
            return new common_report_Report(
                common_report_Report::TYPE_ERROR, $e->getUserMessage()
            );
        } catch (\Exception $e) {
            return new common_report_Report(
                common_report_Report::TYPE_ERROR, $e->getMessage()
            );
        }
    }
}

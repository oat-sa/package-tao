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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoQtiTest\models\pack;

use oat\taoTests\models\pack\Packable;
use oat\taoTests\models\pack\TestPack;
use oat\taoQtiItem\model\pack\QtiItemPacker;
use \taoQtiTest_models_classes_QtiTestService;
use \taoItems_models_classes_ItemsService;
use \core_kernel_classes_Resource;
use \InvalidArgumentException;
use \common_Exception;

/**
 * This class pack a QTI Test. Packing instead of compiling, aims
 * to extract the only data of an test. Those data are used by the
 * test runner to render the test.
 *
 * @package taoQtiTest
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class QtiTestPacker implements Packable
{

    /**
     * The test type identifier
     * @var string
     */
    private static $testType = 'qti';

    /**
     * packTest implementation for QTI
     * @see {@link Packable}
     * @throws InvalidArgumentException
     * @throws common_Exception
     */
    public function packTest(core_kernel_classes_Resource $test)
    {
        $testPack = null;

        try {

            $qtiTestService = taoQtiTest_models_classes_QtiTestService::singleton();
            $itemService    = taoItems_models_classes_ItemsService::singleton();


            $doc            = $qtiTestService->getDoc($test);
            $converter      = new \taoQtiTest_models_classes_QtiTestConverter($doc);
            $itemPacker     = new QtiItemPacker();
            $items          = array();
            foreach($qtiTestService->getItems($test) as $item){
                $items[$item->getUri()] = $itemPacker->packItem($item, $itemService->getItemFolder($item));
            }
            $testPack       = new TestPack(self::$testType, $converter->toArray(), $items);
        } catch(common_Exception $e){
            throw new common_Exception('Unable to pack test '. $test->getUri() . ' : ' . $e->getMessage());
        }

        return $testPack;
    }
}

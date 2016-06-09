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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoQtiTest\test\runner\time;

use oat\taoQtiTest\models\runner\time\QtiTimeStorage;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * Test the {@link QtiTimeStorage.php}
 *
 * @author Aleh hutnikau, <hutnikau@1pt.com>
 */
class QtiTimeStorageTest extends TaoPhpUnitTestRunner
{
    /**
     * @var string
     */
    protected $testSessionId = 'fake_session_id';

    /**
     * @throws \common_ext_ExtensionException
     */
    public function setUp()
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    }

    /**
     * Test the QtiTimeStorage instantiation
     */
    public function testConstructor()
    {
        $storage = new QtiTimeStorage($this->testSessionId);
        $this->assertInstanceOf('\oat\taoTests\models\runner\time\TimeStorage', $storage);
        $this->assertEquals($this->testSessionId, $this->getSessionId($storage));
    }

    /**
     * Test the QtiTimeStorage::store()
     */
    public function testStore()
    {
        $storage = new QtiTimeStorage($this->testSessionId);
        $result = $storage->store('string value');
        $this->assertEquals($storage, $result);
        $this->assertEquals('string value', $result->load());
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function testStoreInvalidDataException()
    {
        $storage = new QtiTimeStorage($this->testSessionId);
        $storage->store(null);
    }

    /**
     * Test the QtiTimeStorage::load()
     */
    public function testLoad()
    {
        $storage = new QtiTimeStorage($this->testSessionId);
        $storage->store('string value');
        $this->assertEquals('string value', $storage->load());
    }

    /**
     * Get test session id from QtiTimeStorage instance
     *
     * @param QtiTimeStorage $storage
     * @return string
     */
    protected function getSessionId(QtiTimeStorage $storage)
    {
        $reflectionClass = new \ReflectionClass('oat\taoQtiTest\models\runner\time\QtiTimeStorage');
        $reflectionProperty = $reflectionClass->getProperty('testSessionId');
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($storage);
    }
}

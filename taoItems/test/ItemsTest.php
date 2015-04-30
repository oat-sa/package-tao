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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2015 (update and modification) Open Assessment Technologies SA
 */
namespace oat\taoItems\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use core_kernel_classes_Property;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use taoItems_models_classes_ItemsService;
use Prophecy\Argument;
use oat\taoOpenWebItem\model\OwiItemModel;


/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class ItemsTestCase extends TaoPhpUnitTestRunner
{

    /**
     *
     * @var \taoItems_models_classes_ItemsService
     */
    protected $itemsService = null;

    /**
     * tests initialization
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->itemsService = \taoItems_models_classes_ItemsService::singleton();
    }

    /**
     * Test the user service implementation
     * @see tao_models_classes_ServiceFactory::get
     * @see taoItems_models_classes_ItemsService::__construct
     */
    public function testService()
    {

        $this->assertIsA($this->itemsService, '\tao_models_classes_Service');
        $this->assertIsA($this->itemsService, '\taoItems_models_classes_ItemsService');

    }

    /**
     * @return \core_kernel_classes_Class|null
     */
    public function testClassCreate()
    {
        $this->assertTrue(defined('TAO_ITEM_CLASS'));
        $ItemClass = $this->itemsService->getRootClass();
        $this->assertIsA($ItemClass, 'core_kernel_classes_Class');
        $this->assertEquals(TAO_ITEM_CLASS, $ItemClass->getUri());

        return $ItemClass;
    }

    /**
     * @depends testClassCreate
     * @param $ItemClass
     * @return \core_kernel_classes_Class
     */
    public function testSubClassCreate($ItemClass)
    {
        $subItemClassLabel = 'subItem class';
        $subItemClass = $this->itemsService->createSubClass($ItemClass, $subItemClassLabel);
        $this->assertIsA($subItemClass, 'core_kernel_classes_Class');
        $this->assertEquals($subItemClassLabel, $subItemClass->getLabel());

        return $subItemClass;
    }

    /**
     * @depends testClassCreate
     * @param $class
     * @return \core_kernel_classes_Resource
     */
    public function testInstantiateClass($class)
    {
        $ItemInstanceLabel = 'Item instance';
        return $this->instantiateClass($class, $ItemInstanceLabel);
    }

    /**
     * @depends testSubClassCreate
     * @param $class
     * @return \core_kernel_classes_Resource
     */
    public function testInstantiateSubClass($class)
    {
        $subItemInstanceLabel = 'subItem instance';
        return $this->instantiateClass($class, $subItemInstanceLabel);
    }

    /**
     * @param $class
     * @param $label
     * @return \core_kernel_classes_Resource
     */
    protected function instantiateClass($class, $label)
    {
        $instance = $this->itemsService->createInstance($class, $label);
        $this->assertIsA($instance, 'core_kernel_classes_Resource');
        $this->assertEquals($label, $instance->getLabel());

        $this->assertTrue(defined('RDFS_LABEL'));
        $instance->removePropertyValues(new \core_kernel_classes_Property(RDFS_LABEL));
        $instance->setLabel($label);


        $this->assertIsA($instance, 'core_kernel_classes_Resource');
        $this->assertEquals($label, $instance->getLabel());
        return $instance;
    }


    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testItemContent($instance)
    {
        $this->assertFalse($this->itemsService->hasItemModel($instance, array(OwiItemModel::ITEMMODEL_URI)));
        $this->assertFalse($this->itemsService->hasItemContent($instance));

        $this->itemsService->setDefaultItemContent($instance);
        $this->assertFileExists($this->itemsService->getDefaultItemFolder($instance));

        $instance->setPropertyValue(new \core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), OwiItemModel::ITEMMODEL_URI);

        //is really empty
        $this->assertFalse($this->itemsService->hasItemContent($instance));
        //we can set
        $this->assertNotNull($this->itemsService->setItemContent($instance, 'test'));
        $this->assertEquals('test', $this->itemsService->getItemContent($instance));
//        and overwrite
        $this->assertNotNull($this->itemsService->setItemContent($instance, 'test2'));
        $this->assertEquals('test2', $this->itemsService->getItemContent($instance));

        //if no itemContent is set get the default one and copy it into a new repository
        $this->assertEquals('test2', $this->itemsService->getItemContent($instance, 'BY'));

        $this->assertTrue($this->itemsService->hasItemContent($instance));

        $this->assertStringStartsWith(LOCAL_NAMESPACE, $instance->getUri());
        $this->assertTrue($this->itemsService->hasItemModel($instance, array(OwiItemModel::ITEMMODEL_URI)));

        $this->assertStringStartsWith(ROOT_URL, $this->itemsService->getPreviewUrl($instance));

        $this->assertEquals('taoItems_models_classes_ItemCompiler', $this->itemsService->getCompilerClass($instance));

        $this->assertEquals(count($this->itemsService->getAllByModel($instance)), 0);
        $this->assertEquals(count($this->itemsService->getAllByModel(null)), 0);

        $this->assertContains('test2',$this->itemsService->render($instance, $this->itemsService->getSessionLg()));

        $this->assertFalse($this->itemsService->hasModelStatus($instance, array(TAO_ITEM_MODEL_STATUS_DEPRECATED)));

        $dataPath = FILES_PATH . 'taoItems' . DIRECTORY_SEPARATOR. 'itemData' . DIRECTORY_SEPARATOR;
        $source = \tao_models_classes_FileSourceService::singleton()->addLocalSource('itemDirectory', $dataPath);
        $this->assertNotFalse($this->itemsService->setDefaultFilesource($source));
    }

    public function testIsItemClass()
    {
        $clazz = $this->prophesize('core_kernel_classes_Class');
        $clazz->getUri()->willReturn(TAO_ITEM_CLASS);   
        $this->assertTrue($this->itemsService->isItemClass($clazz->reveal()));
        
        
        $clazz = $this->prophesize('core_kernel_classes_Class');
        $clazz->getUri()->willReturn('uri');
        
        $parent = $this->prophesize('core_kernel_classes_Class');
        $parent->getUri()->willReturn(TAO_ITEM_CLASS);
        
        $clazz->getParentClasses(true)->willReturn(array($parent->reveal()));
        $this->assertTrue($this->itemsService->isItemClass($clazz->reveal()));
    }
    
    public function testGetModelRuntime()
    {
        $item = $this->prophesize('core_kernel_classes_Resource');
        $itemModel = $this->prophesize('core_kernel_classes_Resource');
        $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY))
            ->willReturn('returnValue');
        $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY))
            ->willReturn($itemModel->reveal());
        
        $this->assertEquals('returnValue', $this->itemsService->getModelRuntime($item->reveal()));
    }
    
    public function testGetItemModel()
    {
        $item = $this->prophesize('core_kernel_classes_Resource');
        $itemModelProphecy = $this->prophesize('core_kernel_classes_Resource');
        $itemModel = $itemModelProphecy->reveal();
        $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY))
        ->willReturn($itemModel);
        $this->assertEquals($itemModel, $this->itemsService->getItemModel($item->reveal()));
    }
    
    
    public function testGetPreviewUrl()
    {
        $item = $this->prophesize('core_kernel_classes_Resource');
        $itemModelProphecy = $this->prophesize('core_kernel_classes_Resource');
        $itemModelProphecy->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ITEM_MODEL_SERVICE))
        ->willReturn(array());
        $itemModel = $itemModelProphecy->reveal();
        $this->assertNull($this->itemsService->getPreviewUrl($item->reveal()));
                
    }
    

    public function testGetItemModelImplementation()
    {
        $itemModelProphecy = $this->prophesize('core_kernel_classes_Resource');
        $itemModelProphecy->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ITEM_MODEL_SERVICE))
            ->willReturn(array('#fakeUri','#toto'));
        $itemModelProphecy->getLabel()->willReturn('foo');
        
        try {
            $this->itemsService->getItemModelImplementation($itemModelProphecy->reveal());
            $this->fail('an exception should have been raised');
        }
        catch (\common_Exception $e) {
            $this->assertInstanceOf('common_exception_Error', $e);
            $this->assertEquals('Conflicting services for itemmodel foo', $e->getMessage());         
        }
        
        $itemModelProphecy->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ITEM_MODEL_SERVICE))
        ->willReturn(array('#fakeUri'));
        $itemModelProphecy->getLabel()->willReturn('foo');
        
        try {
            $this->itemsService->getItemModelImplementation($itemModelProphecy->reveal());
            $this->fail('an exception should have been raised');
        }
        catch (\common_Exception $e) {
            $this->assertInstanceOf('common_exception_Error', $e);
            $this->assertEquals('Item model service #fakeUri not found, or not compatible for item model foo', $e->getMessage());
        
        }
        
        $itemModelProphecy->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ITEM_MODEL_SERVICE))
            ->willReturn(array());
        $this->assertNull($this->itemsService->getItemModelImplementation($itemModelProphecy->reveal()));
    }
    
    
    
    
    public function testGetDefaultFileSource()
    {
        $this->assertInstanceOf('core_kernel_versioning_Repository', $this->itemsService->getDefaultFileSource());
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
        $oldConfig = $ext->getConfig(taoItems_models_classes_ItemsService::CONFIG_DEFAULT_FILESOURCE);
        try {
            $this->assertTrue(
                $ext->setConfig(
                    taoItems_models_classes_ItemsService::CONFIG_DEFAULT_FILESOURCE,
                    array()
                )
            );
            
            $this->itemsService->getDefaultFileSource();
        }
        catch (\common_Exception $e) {
            $this->assertEquals('No default repository defined for Items storage.', $e->getMessage());
            
        }
        $this->assertTrue(
            $ext->setConfig(
                taoItems_models_classes_ItemsService::CONFIG_DEFAULT_FILESOURCE,
                $oldConfig)
        );
        
        
    }
    
    
    public function testIsItemVersioned()
    {
        $item = $this->prophesize('core_kernel_classes_Resource');
        $file = $this->prophesize('core_kernel_classes_Resource');
        $file->hasType(new core_kernel_classes_Class(CLASS_GENERIS_FILE))
            ->willReturn(true);
        $item->getPropertyValues(Argument::which('getUri' , TAO_ITEM_CONTENT_PROPERTY))
            ->willReturn(array($file->reveal()));
        
        $this->assertTrue($this->itemsService->isItemVersioned($item->reveal()));
    }
    
        
    public function testIsItemModelDefined()
    {
        $item = $this->prophesize('core_kernel_classes_Resource');
        
        $this->assertFalse($this->itemsService->isItemModelDefined($item->reveal()));
        
        $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY))
            ->willReturn('notnull');        
        $this->assertTrue($this->itemsService->isItemModelDefined($item->reveal()));
        
        $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY))
        ->willReturn(new \core_kernel_classes_Literal('notnull'));
        $this->assertTrue($this->itemsService->isItemModelDefined($item->reveal()));
    }

    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testClone($instance)
    {
        $clone = $this->itemsService->cloneInstance($instance);
        $this->assertNotSame($clone, $instance);
        $this->assertTrue($this->itemsService->deleteItem($clone));
    }


    /**
     * @depends testSubClassCreate
     * @param $class
     */
    public function testDeleteClass($class)
    {
        $this->assertTrue($this->itemsService->deleteClass($class));
    }

    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testDeleteInstance($instance)
    {
        $this->assertTrue($this->itemsService->deleteItem($instance));
        $this->assertFalse($instance->exists());
    }

}
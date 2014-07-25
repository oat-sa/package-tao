<?php

use qtism\data\storage\xml\XmlCompactAssessmentTestDocument;
use qtism\common\beans\Bean;
use qtism\common\beans\BeanException;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');
require_once (dirname(__FILE__) . '/mocks/SimpleBean.php');
require_once (dirname(__FILE__) . '/mocks/NotStrictConstructorBean.php');
require_once (dirname(__FILE__) . '/mocks/NotStrictMissingSetterBean.php');
require_once (dirname(__FILE__) . '/mocks/StrictBean.php');

class BeanTest extends QtiSmTestCase {
	
    public function testSimpleBean() {
        $mock = new SimpleBean('Mister Bean', 'Mini Cooper');
        $bean = new Bean($mock);
        $this->assertInstanceOf('qtism\\common\\beans\\Bean', $bean);
        
        // --- Try to get information about property existence.
        $this->assertTrue($bean->hasProperty('name'));
        // This property simply does not exist.
        $this->assertFalse($bean->hasProperty('miniCooper'));
        // This property exists but is not annotated with @qtism-bean-property.
        $this->assertFalse($bean->hasProperty('uselessProperty'));
        
        // --- Try to retrieve some bean properties.
        $this->assertInstanceOf('qtism\\common\\beans\\BeanProperty', $bean->getProperty('name'));
        
        // The property does not exist.
        try {
            $beanProperty = $bean->getProperty('miniCooper');
            $this->assertFalse(true, "An exception must be thrown because the property does not exist in the bean.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NO_PROPERTY, $e->getCode());
        }
        
        // The property exists but is not annotated.
        try {
            $beanProperty = $bean->getProperty('uselessProperty');
            $this->assertFalse(true, "An exception must be thrown because the property is not annotated.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NO_PROPERTY, $e->getCode());
        }
        
        // The annotated properties are ['name', 'car'].
        $names = array('name', 'car');
        $beanProperties = $bean->getProperties();
        for ($i = 0; $i < count($names); $i++) {
            $this->assertEquals($names[$i], $beanProperties[$i]->getName());
        }
        
        // --- Try to get information about getter existence.
        $this->assertTrue($bean->hasGetter('name') !== false);
        // Simply does not exist.
        $this->assertFalse($bean->hasGetter('miniCooper'));
        // Exists but not related to an annotated property.
        $this->assertFalse($bean->hasGetter('uselessProperty'));
        
        // --- Try to retrieve some bean methods.
        $this->assertInstanceOf('qtism\\common\\beans\\BeanMethod', $bean->getGetter('name'));
        
        // The getter does not exist.
        try {
            $beanMethod = $bean->getGetter('miniCooper');
            $this->assertTrue(false, "An exception must thrown because the getter does not exist in the bean.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NO_METHOD, $e->getCode());
        }
        
        // The getter exists but is not related to a valid bean property.
        try {
            $beanMethod = $bean->getGetter('uselessProperty');
            $this->assertTrue(false, "An exception must be thrown because the property targeted by the getter is not an annotated bean property.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NO_METHOD, $e->getCode());
        }
        
        $beanGetters = $bean->getGetters();
        for ($i = 0; $i < count($names); $i++) {
            $this->assertEquals('get' . ucfirst($names[$i]), $beanGetters[$i]->getName());
        }
        
        // --- Try to get information about setter existence.
        $this->assertTrue($bean->hasSetter('name'));
        // Simply does not exist.
        $this->assertFalse($bean->hasSetter('miniCooper'));
        // Exists but not related to an annotated property.
        $this->assertFalse($bean->hasSetter('uselessProperty'));
        
        // --- Try to retrieve some bean methods.
        $this->assertInstanceOf('qtism\\common\\beans\\BeanMethod', $bean->getSetter('name'));
        
        // The getter does not exist.
        try {
            $beanMethod = $bean->getSetter('miniCooper');
            $this->assertTrue(false, "An exception must thrown because the setter does not exist in the bean.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NO_METHOD, $e->getCode());
        }
        
        // The getter exists but is not related to a valid bean property.
        try {
            $beanMethod = $bean->getSetter('uselessProperty');
            $this->assertTrue(false, "An exception must be thrown because the property targeted by the getter is not an annotated bean property.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NO_METHOD, $e->getCode());
        }
        
        $beanGetters = $bean->getSetters();
        for ($i = 0; $i < count($names); $i++) {
            $this->assertEquals('set' . ucfirst($names[$i]), $beanGetters[$i]->getName());
        }
        
        // --- Play with the constructor
        $beanParams = $bean->getConstructorParameters();
        // The constructor has 3 parameters but only parameters with the same 
        // name as a valid bean property are returned.
        $this->assertEquals(2, count($beanParams));
        
        for ($i = 0; $i < count($names); $i++) {
            $this->assertEquals($names[$i], $beanParams[$i]->getName());
        }
        
        $ctorGetters = $bean->getConstructorGetters();
        $this->assertEquals(2, count($ctorGetters));
        
        for ($i = 0; $i < count($names); $i++) {
            $this->assertEquals('get' . ucfirst($names[$i]), $ctorGetters[$i]->getName());
        }
        
        $ctorSetters = $bean->getConstructorSetters();
        $this->assertEquals(2, count($ctorSetters));
        
        for ($i = 0; $i < count($names); $i++) {
            $this->assertEquals('set' . ucfirst($names[$i]), $ctorSetters[$i]->getName());
        }
        
        // The SimpleBean class cannot be considered as a strict bean.
        try {
            $bean = new Bean($mock, true);
            $this->assertTrue(false, "An exception must be thrown because the SimpleBean class is not a strict bean implementation.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NOT_STRICT, $e->getCode());
        }
    }
    
    public function testNotStrictBeanBecauseOfConstructor() {
        
        // must work in unstrict mode.
        $mock = new NotStrictConstructorBean('John', 'Dunbar', 'red');
        $bean = new Bean($mock);
        $this->assertInstanceOf('qtism\\common\\beans\\Bean', $bean);
        
        try {
            $bean = new Bean($mock, true);
            $this->assertFalse(true, "An exception must be thrown because the NotStrictConstructorBean class provides an invalid constructor.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NOT_STRICT, $e->getCode());
        }
    }
    
    public function testNotStrictBeanBecauseOfMissingSetter() {
        
        // must work if no strict mode.
        $mock = new NotStrictMissingSetterBean('John', 'Dunbar', 'brown');
        $bean = new Bean($mock);
        $this->assertInstanceOf('qtism\\common\\beans\\Bean', $bean);
        
        try {
            $bean = new Bean($mock, true);
            $this->assertTrue(false, "An exception must be thrown because the NotStrictMissingSetterBean class has a protected bean setter that should be public.");
        }
        catch (BeanException $e) {
            $this->assertEquals(BeanException::NOT_STRICT, $e->getCode());
        }
    }
    
    public function testStrictBean() {
        $mock = new StrictBean('John', 'Dunbar', 'blond', true);
        $bean = new Bean($mock, true);
        $this->assertInstanceOf('qtism\\common\\beans\\Bean', $bean);
        
        $this->assertTrue($bean->hasConstructorParameter('firstName'));
        $this->assertTrue($bean->hasConstructorParameter('lastName'));
        $this->assertTrue($bean->hasConstructorParameter('hair'));
        $this->assertTrue($bean->hasConstructorParameter('cool'));
        $this->assertTrue($bean->hasProperty('firstName'));
        $this->assertTrue($bean->hasProperty('lastName'));
        $this->assertTrue($bean->hasProperty('hair'));
        $this->assertTrue($bean->hasProperty('cool'));
        $this->assertTrue($bean->hasGetter('firstName') !== false);
        $this->assertTrue($bean->hasGetter('lastName') !== false);
        $this->assertTrue($bean->hasGetter('hair') !== false);
        $this->assertTrue($bean->hasGetter('cool') !== false);
        $this->assertTrue($bean->hasSetter('firstName'));
        $this->assertTrue($bean->hasSetter('lastName'));
        $this->assertTrue($bean->hasSetter('hair'));
        $this->assertTrue($bean->hasSetter('cool'));
        
        $this->assertEquals('isCool', $bean->getGetter('cool')->getName());
        
        $this->assertEquals(4, count($bean->getGetters()));
        $this->assertEquals(0, count($bean->getGetters(true)));
        
        $this->assertEquals(4, count($bean->getSetters()));
        $this->assertEquals(0, count($bean->getSetters(true)));
    }
    
    public function testGetGetterByBeanProperty() {
        $mock = new StrictBean('John', 'Dunbar', 'white', false);
        $bean = new Bean($mock, true);
        
        $property = $bean->getProperty('cool');
        $getter = $bean->getGetter($property);
        $this->assertEquals('isCool', $getter->getName());
    }
    
    public function testGetSetterByBeanProperty() {
        $mock = new StrictBean('John', 'Dunbar', 'white', false);
        $bean = new Bean($mock, true);
    
        $property = $bean->getProperty('cool');
        $setter = $bean->getSetter($property);
        $this->assertEquals('setCool', $setter->getName());
    }
    
    public function testHasGetterByBeanProperty() {
        $mock = new StrictBean('Mickael', 'Dundie', 'black', true);
        $bean = new Bean($mock);
        
        $property = $bean->getProperty('firstName');
        $this->assertTrue($bean->hasGetter($property) !== false);
    }
    
    public function testHasSetterByBeanProperty() {
        $mock = new StrictBean('Mickael', 'Dundie', 'black', true);
        $bean = new Bean($mock);
    
        $property = $bean->getProperty('firstName');
        $this->assertTrue($bean->hasSetter($property) !== false);
    }
}
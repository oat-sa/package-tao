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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
use oat\tao\test\TaoPhpUnitTestRunner;
use \oat\generis\model\user\PasswordConstraintsService;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * This class enable you to test the validators
 *
 * @author Joel Bout, <taosupport@tudor.lu>
 * @package tao
 
 */
class ValidatorTest extends TaoPhpUnitTestRunner {

	public static function staticMirror($value) {
		return $value;
	}

	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoPhpUnitTestRunner::initTest();
	}

	/**
	 * Test the service factory: dynamical instantiation and single instance serving
	 * @see tao_models_classes_ServiceFactory::get
	 */
	public function testAlphaNum(){

		//@todo  fix "\n" in validator and add to test

		// test getValidator
		$alphanum = tao_helpers_form_FormFactory::getValidator('AlphaNum');
		$this->assertIsA($alphanum, 'tao_helpers_form_validators_AlphaNum');

		$alphanum		= new tao_helpers_form_validators_AlphaNum();
		$this->exec($alphanum,
				array('abc123', '', 'Ab1Cd2Ef3', 50),
				array(null, 'a_1', '!', '&auml;', " ", array(), 12.3),
				'AlphaNum without punctuation'
				);

		$alphanumpunct	= new tao_helpers_form_validators_AlphaNum(array('allow_punctuation' => true));

		$this->exec($alphanumpunct,
			array('abc123', '', 'Ab1Cd2Ef3','a_1','1-2-3-4', 12),
			array(null, '!', '&auml;', '/root/test/why', '1.23', '2,5', array()),
			'AlphaNum with punctuation'
		);
	}

	public function exec(tao_helpers_form_Validator $pValidator, $pValid, $pInvalid = array(), $pHint = '') {
		$this->validValues($pValidator, is_array($pValid) ? $pValid : array($pValid), $pHint);
		$this->invalidValues($pValidator, is_array($pInvalid) ? $pInvalid : array($pInvalid), $pHint);
	}

	public function validValues(tao_helpers_form_Validator $pValidator, $pValues, $pHint = '') {
		$desc = empty($pHint) ? get_class($pValidator) : $pHint;
		foreach ($pValues as $val) {
			$nfo = $val;
			if (is_array($val) && isset($val['name'])) $nfo = $val['name'];
			$this->assertTrue($pValidator->evaluate($val), $desc.' evaluated \''.$nfo.'\' as false');
		}
	}

	public function invalidValues(tao_helpers_form_Validator $pValidator, $pValues, $pHint = '') {
		$desc = empty($pHint) ? get_class($pValidator) : $pHint;
		foreach ($pValues as $val) {
			$nfo = $val;
			if (is_array($val) && isset($val['uploaded_file'])) $nfo = $val['uploaded_file'];
			if (is_array($nfo)) $nfo = implode('-', $nfo);
			$this->assertFalse($pValidator->evaluate($val), $desc.' evaluated \''.$nfo.'\' as true');
		}
	}

	public function testCallback(){

		// global function
		// wrong parameters
		$callback = new tao_helpers_form_validators_Callback(array(
			'function' => 'aFunctionThatDoesntExist'
		));
		$this->setExpectedException('common_Exception');
		$this->assertFalse($callback->evaluate(''));

		// global function
		// simple parameters
		$callback = new tao_helpers_form_validators_Callback(array(
			'function' => 'ValidatorTestCaseGlobalMirror'
		));
		$this->assertTrue($callback->evaluate(true));
		$this->assertFalse($callback->evaluate(false));

		// global function
		// complex parameters
		$callback		= new tao_helpers_form_validators_Callback(array(
			'function' => 'ValidatorTestCaseGlobalInstanceOf'
		));
		$this->assertTrue($callback->evaluate(array(
				'tao_helpers_form_validators_Callback' => $callback
		)));
		$this->assertFalse($callback->evaluate(array(
				'tao_helpers_form_validators_AlphaNum' => $callback
		)));

		// static function
		$callback = new tao_helpers_form_validators_Callback(array(
			'class'		=> 'ValidatorTestCasePrototype',
			'method'	=> 'staticMirror'
		));
		$this->assertTrue($callback->evaluate(true));
		$this->assertFalse($callback->evaluate(false));

		// static function
		$callback = new tao_helpers_form_validators_Callback(array(
			'object'	=> $this,
			'method'	=> 'instanceMirror'
		));
		$this->assertTrue($callback->evaluate(true));
		$this->assertFalse($callback->evaluate(false));

	}

	public function testDateTime(){

		//@todo:  doublecheck empty string and null treatment

		$dateTime = new tao_helpers_form_validators_DateTime();
		$this->exec($dateTime,
			array('April 17, 1790', '2008-07-01T22:35:17.03+08:00', '10/Oct/2000:13:55:36 -700', 'today', '04:08', 'a week ago', 'yesterday', 'tomorrow'),
			array('abc'),
			'simple Datetimes'
		);

		$formelement = new tao_helpers_form_elements_xhtml_Calendar('testelement');
		$formelement->setValue('today');

		// config sanity tests
// 		$this->expectException();
// 		$dateTime = new tao_helpers_form_validators_DateTime(array(
// 				'comparator'	=> 'nonsense',
// 				'datetime2_ref'	=> $formelement
// 		));
// 		$dateTime->evaluate('today');

// 		$this->expectException();
// 		$dateTime = new tao_helpers_form_validators_DateTime(array(
// 				'datetime2_ref'	=> 'test'
// 		));
// 		$dateTime->evaluate('today');

// 		$this->expectException();
// 		$dateTime = new tao_helpers_form_validators_DateTime(array(
// 				'comparator'	=> 'less',
// 		));
// 		$dateTime->evaluate('today');

		$dateTime = new tao_helpers_form_validators_DateTime(array(
			'comparator'	=> 'after',
			'datetime2_ref'	=> $formelement
		));
		$this->exec($dateTime, 'tomorrow', 'yesterday', 'Compare After');

		$dateTime = new tao_helpers_form_validators_DateTime(array(
			'comparator'	=> '<',
			'datetime2_ref'	=> $formelement
		));
		$this->exec($dateTime, 'yesterday', 'tomorrow', 'Compare After');

	}

	public function testFileMimeType(){
		//XML
		$val = array('uploaded_file' => dirname(__FILE__).'/samples/events.xml');
		$filemime = new tao_helpers_form_validators_FileMimeType(array(
				'mimetype' => array('text/xml', 'application/xml', 'application/x-xml'),
				'extension' => array('xml')
		));
		$this->assertTrue($filemime->evaluate($val));

		//ZIP
		$val = array('uploaded_file' => dirname(__FILE__).'/samples/zip/test.zip');
		$filemime = new tao_helpers_form_validators_FileMimeType(array(
				'mimetype' => array('application/zip'),
				'extension' => array('zip')
		));
		$this->assertTrue($filemime->evaluate($val));

		//CSS
		$val = array('uploaded_file' => dirname(__FILE__).'/samples/css/test.css');
		$filemime = new tao_helpers_form_validators_FileMimeType(array(
				'mimetype' => array('text/css', 'text/plain'),
				'extension' => array('css')
		));
		$this->assertTrue($filemime->evaluate($val));

		//Error
		$val = array('uploaded_file' => dirname(__FILE__).'/samples/sample_sort.po');
		$filemime = new tao_helpers_form_validators_FileMimeType(array(
				'mimetype' => array('text/css'),
				'extension' => array('po')
		));
		$this->assertFalse($filemime->evaluate($val));
	}

	public function testFileSize(){

		$smallfile = array(
				'name'     => 'testname',
				'tmp_name' => '/tmp/doesnotexists',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 500,
		);
		$mediumfile = array(
				'name'     => 'testname',
				'tmp_name' => '/tmp/doesnotexists',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 1000000,
		);
		$bigfile = array(
				'name'     => 'testname',
				'tmp_name' => '/tmp/doesnotexists',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 50000000,
		);
		$errorfile = array(
				'error'    => UPLOAD_ERR_NO_FILE,
		);

		//option test
		$this->setExpectedException('common_Exception');
		$filemime = new tao_helpers_form_validators_FileSize(array());

		$filesize = new tao_helpers_form_validators_FileSize(array('min' => 1000));
		$this->exec($filemime, array($mediumfile, $bigfile), array($errorfile, $smallfile), 'Filesize Minimum Validation');

		$filesize = new tao_helpers_form_validators_FileSize(array('max' => 1000));
		$this->exec($filemime,
				array($smallfile),
				array($errorfile, $mediumfile, $bigfile),
				'Filesize Maximum Validation');

		$filesize = new tao_helpers_form_validators_FileSize(array('min' => 1000, 'max' => 5000000));
		$this->exec($filemime,
				array($mediumfile),
				array($errorfile, $smallfile, $bigfile),
				'Filesize Range Validation');
	}

	public function testNumeric(){
		$num = tao_helpers_form_FormFactory::getValidator('Numeric');
		$this->assertIsA($num, 'tao_helpers_form_validators_Numeric');

		$num = new tao_helpers_form_validators_Numeric();
		$this->exec($num,
				array('10', '10.1', 12, 12.1),
				array('a_1', '!', '&auml;'), //TODO null, " ", array() with a refactoring to include noempty as a mother class
				'Numeric validation'
			);

		$num = new tao_helpers_form_validators_Integer();
		$this->exec($num,
				array('10', 12),
				array('10.1', 12.1),
				'Integer validation'
			);

		$num = tao_helpers_form_FormFactory::getValidator('Integer', array('min' => 10));
		$this->assertFalse($num->evaluate(5));
		$this->assertTrue($num->evaluate(11));

		$elt = tao_helpers_form_FormFactory::getElement('max', 'Textbox');
		$elt->setValue('5');

		$num = tao_helpers_form_FormFactory::getValidator('Integer', array('integer2_ref' => $elt, 'comparator' => '>'));
		$this->exec($num,
				array(10, 102),
				array(2, -40),
				'Integer comparator validation'
			);
	}

	public function testLabel(){
		//@todo implement test cases
	}

	public function testLength(){
		$minlenght = new tao_helpers_form_validators_Length(array('min' => 3));
		$this->exec($minlenght,
				array('abc', '1234', '___', '   '),
				array('!', "qc", "  ", ""),
				'Length with min 3'
		);

		$maxlenght = new tao_helpers_form_validators_Length(array('max' => 3));
		$this->exec($maxlenght,
				array('abc', '12', '_', '   ', '','!'),
				array("qcde",'    '),
				'Length with max 3'
		);

		$minmaxlenght = new tao_helpers_form_validators_Length(array('min' => 2, 'max' => 4));
		$this->exec($minmaxlenght,
				array('ab', '123', '____', '   '),
				array('!', "q", "qq  q", ""),
				'Length with min 2 max 4'
		);

		$utf8 = 'ää';
		$umls = iconv("UTF-8", mb_internal_encoding(), $utf8);
		$this->assertFalse($minlenght->evaluate($umls), 'Error during length validation of special characters \''.$utf8.'\' using encoding '.mb_internal_encoding());
	}

    /**
     * @dataProvider notEmptyProvider
     */
	public function testNotEmpty($value, $expected){

        $validator = tao_helpers_form_FormFactory::getValidator('NotEmpty');
        $this->assertEquals( $expected, $validator->evaluate($value) );
	}

    public function notEmptyProvider(){
        $validObject   = new \stdClass();
        $validObject->data = 'valid_object';

        return array(
            'valid_string'   => array('valid_string', true),
            'valid_array'    => array(array('valid_array'),  true),
            'valid_int'      => array(1, true),
            'valid_int_zero' => array(1, true),
            'valid_float'      => array(1.5, true),
            'valid_float_zero' => array(0.0, true),
            'valid_object'     => array($validObject, true),
            'valid_bool_t'     => array(true, true),
            'valid_bool_f'     => array(false, true),

            'invalid_string'   => array('', false),
            'invalid_array'    => array(array(),  false),
            'invalid_null'     => array(null, false),
        );
    }

	public function testEquals() {
		$formelement = new tao_helpers_form_elements_xhtml_Textbox('testelement');
		$formelement->setValue('123');

		$equals = tao_helpers_form_FormFactory::getValidator('Equals', array(
			'reference' => $formelement
		));
		$this->assertIsA($equals, 'tao_helpers_form_validators_Equals');

		$this->assertFalse($equals->evaluate('1234'));
		$this->assertTrue($equals->evaluate('123'));

		$equals = tao_helpers_form_FormFactory::getValidator('Equals', array(
			'reference' => $formelement,
			'invert'	=> true
		));
		$this->assertIsA($equals, 'tao_helpers_form_validators_Equals');

		$this->assertFalse($equals->evaluate('123'));
		$this->assertTrue($equals->evaluate('1234'));

		$this->setExpectedException('common_Exception');
		$equals = tao_helpers_form_FormFactory::getValidator('Equals');
		//@todo implement test cases for multivalues
	}

    /**
     * Positive test for tao_helpers_form_validators_Password
     */
	public function testPasswordPositive(){
        $validPassword = 'valid_top_secret_test_password';
        $invalidPassword = 'invalid_top_secret_test_password';

        $formElement = new tao_helpers_form_elements_xhtml_Password('testelement');
        $formElement->setValue($validPassword);

        $validator = tao_helpers_form_FormFactory::getValidator('Password', array(
            'password2_ref' => $formElement
        ));

        $this->assertInstanceOf('tao_helpers_form_Validator', $validator);
        $this->assertTrue( $validator->evaluate($validPassword) );
        $this->assertFalse( $validator->evaluate($invalidPassword) );
	}

    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Please set the reference of the second password element
     */
    public function testPasswordNegativeNoReference()
    {
        $validator = tao_helpers_form_FormFactory::getValidator('Password');
        $validator->evaluate('something');
    }

    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Please set the reference of the second password element
     */
    public function testPasswordNegativeInvalidReference()
    {
        $validator = tao_helpers_form_FormFactory::getValidator('Password', array('password2_ref' => 'invalid_form_element'));
        $validator->evaluate('something');
    }

    /**
     * @param string $value
     * @param string $format
     * @param bool $expected
     *
     * @dataProvider regexProvider
     */
	public function testRegex($value, $format, $expected)
    {
        $validator = tao_helpers_form_FormFactory::getValidator('Regex', array(
            'format' => $format
        ));

        $this->assertInstanceOf('tao_helpers_form_Validator', $validator);
        $this->assertEquals($expected, $validator->evaluate($value) );
	}

    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Please set the format options (define your regular expression)!
     */
    public function testRegexMisconfig()
    {
        tao_helpers_form_FormFactory::getValidator('Regex', array());
    }

    public function regexProvider()
    {
        return array(
            'valid'   => array('aaaaababaaab','/[ab]+/',true),
            'invalid' => array('cdcdcdcdcddd','/[ab]+/',false),
        );
    }

    /**
     * @param $value
     * @param $allowParams
     * @param $expected
     *
     * @dataProvider urlProvider
     */
	public function testUrl($value, $allowParams, $expected)
    {
        $options = array();

        if( $allowParams === true ){
            $options['allow_parameters'] = true;
        }

        $validator = tao_helpers_form_FormFactory::getValidator('Url', $options);
        $this->assertInstanceOf('tao_helpers_form_Validator', $validator);
        $this->assertEquals( $expected, $validator->evaluate($value) );
	}

    public function urlProvider(){
        return array(
            'valid_short'       => array('http://example.com', false, true),
            'valid_port'        => array('http://example.com:1234', false, true),
            'valid_ip'          => array('http://127.0.0.1', false, true),
            'valid_ip_port'     => array('http://127.0.0.1:1234', false, true),
            'valid_proto'       => array('ftp://example.com', false, true),
            'valid_subdomain'   => array('http://subdomain.example.com', false, true),
            'valid_full'        => array('http://example.com/foo/bar.html', false, true),
            'valid_short_path'  => array('http://example.com/foo/bar', false, true),
            'valid_full_params' => array('http://example.com/foo/bar.html?param=1', true, true),
            //--------
            'invalid_not_url'   => array('not_an_url', false, false),
            'invalid_url_1'     => array('http//example.com', false, false),
            'invalid_url_2'     => array('http:/example.com', false, false),
            'invalid_url_3'     => array('http://example,com', false, false),
            'invalid_long_port' => array('http://example,com:234242', false, false),
            'invalid_no_params' => array('http://example.com/?param=restricted', false, false),
        );
    }

    public function testPasswordStrength()
    {
        $validator = tao_helpers_form_FormFactory::getValidator('PasswordStrength');

        $this->assertInstanceOf('tao_helpers_form_Validator', $validator);
        $this->assertTrue( $validator->evaluate( $this->buildValidPassword() ) );
        $this->assertFalse( $validator->evaluate( $this->buildInvalidPassword() ) );
    }

	//Helpers

    protected function buildValidPassword()
    {
        $validPassword = '';

        $constaintsService = PasswordConstraintsService::singleton();

        $config = $this->invokeProtectedMethod($constaintsService, 'getConfig');

        if( $config['upper'] === true ){
            $validPassword .= 'A';
        }

        if( $config['lower'] === true ){
            $validPassword .= 'b';
        }

        if( $config['number'] === true ){
            $validPassword .= '3';
        }

        if( $config['spec'] === true ){
            $validPassword .= '@';
        }

        $validPassword = str_pad($validPassword, $config['length'], 'c');

        return $validPassword;
    }

    protected function buildInvalidPassword()
    {
        $constaintsService = PasswordConstraintsService::singleton();
        $config = $this->invokeProtectedMethod($constaintsService, 'getConfig');

        $invalidPassword = $this->buildValidPassword();

        if( $config['upper'] === true ){
            $invalidPassword = str_replace('A', '', $invalidPassword);
        }

        if( $config['lower'] === true ){
            $invalidPassword = str_replace('b', '', $invalidPassword);
        }

        if( $config['number'] === true ){
            $invalidPassword = str_replace('3', '', $invalidPassword);
        }

        if( $config['spec'] === true ){
            $invalidPassword = str_replace('@', '', $invalidPassword);
        }

        $invalidPassword = substr($invalidPassword, 0, $config['length']-1 );

        return $invalidPassword;
    }

    public function testUnique()
    {
        $resourceMock = $this->getMockBuilder('core_kernel_classes_Class')->disableOriginalConstructor()->getMock();
        $resourceMock->method('getParentClasses')->willReturn(array(new kernel_class_Stub()));

        $validator = tao_helpers_form_FormFactory::getValidator('Unique');
        $this->assertInstanceOf('tao_helpers_form_Validator', $validator);

        $options = array(
            'property'      => new kernel_property_Stub(kernel_class_Stub::TEST_PROPERTY_NAME),
        );
        $validator->setOptions($options);

        $this->assertTrue($validator->evaluate(kernel_class_Stub::PROPERTY_NOT_EXISTS));
        $this->assertFalse($validator->evaluate(kernel_class_Stub::PROPERTY_EXISTS_FIRST_LEVEL));
        $this->assertFalse($validator->evaluate(kernel_class_Stub::PROPERTY_EXISTS_RECURSIVE));
    }

    /**
     * @expectedException common_exception_Error
     * @expectedExceptionMessage Property not set
     */
    public function testUniqueNegativePropertyNotSet(){
        $resourceMock = $this->getMockBuilder('core_kernel_classes_Class')->disableOriginalConstructor()->getMock();
        $resourceMock->method('getParentClasses')->willReturn(array(new kernel_class_Stub()));

        $options = array(
            'resourceClass' => $resourceMock,
        );

        $validator = tao_helpers_form_FormFactory::getValidator('Unique', $options);
        $validator->evaluate('some value');
    }

	public function instanceMirror($value) {
		return $value;
	}
}

class kernel_class_Stub {

    const TEST_PROPERTY_NAME          = 'testProperty';
    const PROPERTY_EXISTS_FIRST_LEVEL = 'exists_first_level';
    const PROPERTY_EXISTS_RECURSIVE   = 'exists_recursive';
    const PROPERTY_NOT_EXISTS         = 'not_exists';

    public function searchInstances($propertyFilters = array(), $options = array())
    {
        $recursive = isset( $options['recursive'] ) ? $options['recursive'] : false;

        $returnValue = array();

        switch( $propertyFilters[self::TEST_PROPERTY_NAME] ){
            case self::PROPERTY_EXISTS_FIRST_LEVEL: {
                $returnValue[] = 'some_found_value';
                break;
            }
            case self::PROPERTY_EXISTS_RECURSIVE: {
                if( $recursive === true ){
                    $returnValue[] = 'some_found_value';
                }
                break;
            }
        }

        return $returnValue;
    }
}

class kernel_property_Stub extends core_kernel_classes_Property {
	public function getDomain()
	{
		return array( new kernel_class_Stub() );
	}

}

//Global function
function ValidatorTestCaseGlobalInstanceOf($values) {
	$return = true;
	foreach ($values as $class => $object)
		if (!$object instanceof $class)
			$return = false;
	return $return;
};
function ValidatorTestCaseGlobalMirror($values) {
	return $values;
};

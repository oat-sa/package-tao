<?php
namespace oat\tao\test;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

use \common_ext_ExtensionsManager;
use \common_persistence_Manager;


abstract class RestTestCase extends TaoPhpUnitTestRunner
{

    protected $host = ROOT_URL;

    protected $userUri = "";

    protected $login = "";

    protected $password = "";

    
    public abstract function serviceProvider();
    
    

    
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->disableCache();
        
        // creates a user using remote script from joel
        
        $testUserData = array(
            PROPERTY_USER_LOGIN => 'tjdoe',
            PROPERTY_USER_PASSWORD => 'test123',
            PROPERTY_USER_LASTNAME => 'Doe',
            PROPERTY_USER_FIRSTNAME => 'John',
            PROPERTY_USER_MAIL => 'jdoe@tao.lu',
            PROPERTY_USER_DEFLG => \tao_models_classes_LanguageService::singleton()->getLanguageByCode(DEFAULT_LANG)->getUri(),
            PROPERTY_USER_UILG => \tao_models_classes_LanguageService::singleton()->getLanguageByCode(DEFAULT_LANG)->getUri(),
            PROPERTY_USER_ROLES => array(
                INSTANCE_ROLE_GLOBALMANAGER
            )
        );
        
        $testUserData[PROPERTY_USER_PASSWORD] = 'test' . rand();
        
        $data = $testUserData;
        $data[PROPERTY_USER_PASSWORD] = \core_kernel_users_Service::getPasswordHash()->encrypt($data[PROPERTY_USER_PASSWORD]);
        $tmclass = new \core_kernel_classes_Class(CLASS_TAO_USER);
        $user = $tmclass->createInstanceWithProperties($data);
        \common_Logger::i('Created user ' . $user->getUri());
        
        // prepare a lookup table of languages and values
        $usage = new \core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_GUI);
        $propValue = new \core_kernel_classes_Property(RDF_VALUE);
        $langService = \tao_models_classes_LanguageService::singleton();
        
        $lookup = array();
        foreach ($langService->getAvailableLanguagesByUsage($usage) as $lang) {
            $lookup[$lang->getUri()] = (string) $lang->getUniquePropertyValue($propValue);
        }
        
        $data = array(
            'rootUrl' => ROOT_URL,
            'userUri' => $user->getUri(),
            'userData' => $testUserData,
            'lang' => $lookup
        );
        
        $this->login = $data['userData'][PROPERTY_USER_LOGIN];
        $this->password = $data['userData'][PROPERTY_USER_PASSWORD];
        $this->userUri = $data['userUri'];
    }

    public function tearDown()
    {
        // removes the created user
        $user = new \core_kernel_classes_Resource($this->userUri);
        $success = $user->delete();
        $this->restoreCache();
    }

    /**
     * shall be used beyond high level http connections unit tests (default parameters)
     *
     * @param
     *            returnType CURLINFO_HTTP_CODE, etc... (default returns rhe http response data
     *
     * @return mixed
     */
    protected function curl($url, $method = CURLOPT_HTTPGET, $returnType = "data", $curlopt_httpheaders = array())
    {
        $process = curl_init($url);
        if ($method != "DELETE") {
            curl_setopt($process, $method, 1);
        } else {
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        
        curl_setopt($process, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        
        $headers = array_merge(array(
            "Accept: application/json"
        ), $curlopt_httpheaders);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        if ($method == CURLOPT_POST) {
            curl_setopt($process, CURLOPT_POSTFIELDS, "");
        }
        // curl_setopt($process,CURLOPT_HTTPHEADER,$curlopt_httpheaders);
        $data = curl_exec($process);
        if ($returnType != "data") {
            $data = curl_getinfo($process, $returnType);
        }
        curl_close($process);
        return $data;
    }

    /**
     * @dataProvider serviceProvider
     */
    public function testHttp($service)
    {
        $url = $this->host . $service;
        // HTTP Basic
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"
        ));
        
        // should return a 401
        curl_setopt($process, CURLOPT_USERPWD, "dummy:dummy");
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        
        $data = curl_exec($process);
        $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
        
        $this->assertEquals("401", $http_status, 'bad response on url ' . $url . ' return ' . $http_status);
        curl_close($process);
        
        // should return a 401
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"
        ));
        curl_setopt($process, CURLOPT_USERPWD, $this->login . ":dummy");
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($process);
        $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
        $this->assertEquals($http_status, "401");
        curl_close($process);
        
        // should return a 406
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            "Accept: dummy/dummy"
        ));
        curl_setopt($process, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($process);
        $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
        $this->assertEquals($http_status, "406");
        curl_close($process);
        
        // should return a 200
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml"
        ));
        curl_setopt($process, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($process);
        $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
        $this->assertEquals($http_status, "200");
        
        // should return a 200, should return content encoding application/xml
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
        ));
        curl_setopt($process, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($process);
        $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
        $this->assertEquals($http_status, "200");
        $contentType = curl_getinfo($process, CURLINFO_CONTENT_TYPE);
        $this->assertEquals($contentType, "application/xml");
        curl_close($process);
        
        // should return a 200
        $http_status = $this->curl($url, CURLOPT_HTTPGET, CURLINFO_HTTP_CODE);
                        $this->assertEquals($http_status, "200");
    
    }
    
    /**
     * @dataProvider serviceProvider
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetAll($service, $topclass = null){
        if($topclass == null){
            $this->markTestSkipped('This test do not apply to topclass' , $topclass);
        }
        $url = $this->host.$service;
        $returnedData = $this->curl($url);
        $data = json_decode($returnedData, true);
        $this->assertArrayHasKey('success', $data);
        $this->assertTrue( $data["success"]);
    
        $ItemClass = new \core_kernel_classes_Class($topclass);
        $instances = $ItemClass->getInstances(true);
        foreach ($data['data'] as $results){
            $this->assertInternalType('array', $results);
            $this->assertArrayHasKey('uri', $results);
            $this->assertArrayHasKey('properties', $results);
            $this->assertInternalType('array', $instances);
    
            $this->assertArrayHasKey($results['uri'], $instances);
            $resource = $instances[$results['uri']];
    
            foreach ($results['properties'] as $propArray){
                $this->assertInternalType('array', $propArray);
    
                $this->assertArrayHasKey('predicateUri',$propArray);
                $prop = new \core_kernel_classes_Property($propArray['predicateUri']);
                $values = $resource->getPropertyValues($prop);
                $this->assertArrayHasKey('values',$propArray);
                $current = current($propArray['values']);
                $this->assertInternalType('array',$current);
    
                $this->assertArrayHasKey('valueType',$current);
                if (\common_Utils::isUri(current($values))){
                    $this->assertEquals('resource', $current['valueType']);
    
                } else {
                    $this->assertEquals('literal', $current['valueType']);
                }
                $this->assertArrayHasKey('value',$current);
                $this->assertEquals(current($values), $current['value']);
    
            }
             
        }
         
    }

}

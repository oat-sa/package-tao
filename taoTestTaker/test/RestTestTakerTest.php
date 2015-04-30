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
 */
namespace oat\taoTestTaker\test;

use oat\tao\test\RestTestCase;
use \core_kernel_users_Service;

/**
 * connects as a client agent on the rest controller
 *
 * @author patrick
 * @package taoTestTaker
 *
 */
class RestTestTakerTest extends RestTestCase
{

    private $genLogin;

    private function checkPropertyValues($propertyValues, $property, $valueType = "literal", $value)
    {
        if (is_array($propertyValues)) {
            foreach ($propertyValues as $propertyValue) {
                if ($propertyValue["predicateUri"] == $property) {

                    $this->assertEquals($propertyValue["values"][0]["valueType"], $valueType);
                    $this->assertEquals($propertyValue["values"][0]["value"], $value);
                }
            }
        } else {
            $this->fail('$propertyValues should be an array');
        }
    }

    public function serviceProvider()
    {
        return array(
            array(
                'taoTestTaker/Api',
                TAO_SUBJECT_CLASS
            )
        );
    }

    private function getUrl() {
        return $this->host.'taoTestTaker/Api';
    }

    /**
     *
     */
    public function testCreateTestTaker()
    {
        // create a new test taker without aprameters, should return a 400
        $http_status = $this->curl($this->getUrl(), CURLOPT_POST, CURLINFO_HTTP_CODE);
        $this->assertEquals($http_status, "400");

        // login but no password, should return a 400
        $http_status = $this->curl($this->getUrl(), CURLOPT_POST, CURLINFO_HTTP_CODE, array(
            'login: dummy'
        ));
        $this->assertEquals($http_status, "400");

        // should be 200
        $genLogin = 'dummy_login';
        $returnedData = $this->curl($this->getUrl(), CURLOPT_POST, "data", array(
            'login: dummy_login',
            'password: dummy'
        ));
        $data = json_decode($returnedData, true);
        $this->assertNotEquals(false, $returnedData);
        $this->assertEquals(true, $data["success"]);
        $this->assertArrayHasKey("data", $data);
        $this->assertArrayHasKey("uriResource", $data["data"]);

        return $data["data"]["uriResource"];

    }

    /**
     * @depends testCreateTestTaker
     */
    public function testReadTestTaker($uriSubject)
    {
        // get this test taker
        $returnedData = $this->curl($this->getUrl(), CURLOPT_HTTPGET, "data", array(
            'uri: ' . $uriSubject
        ));
        $data = json_decode($returnedData, true);
        $this->assertEquals($data["success"], true);
        $this->assertEquals($data["data"]["uri"], $uriSubject);

        $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_LOGIN, "literal", 'dummy_login');

        foreach ($data["data"]["properties"] as $propertyValue) {
            if ($propertyValue["predicateUri"] == PROPERTY_USER_PASSWORD) {
                $this->assertTrue(\core_kernel_users_Service::getPasswordHash()->verify('dummy', $propertyValue["values"][0]["value"]));
            }
        }
    }

    /**
     * @depends testCreateTestTaker
     */
    public function testUpdateTestTaker($uriSubject)
    {
        // modifying the login of a subject is not allowed : 412
        $returnedData = $this->curl($this->getUrl(), CURLOPT_PUT, CURLINFO_HTTP_CODE, array(
            'uri: ' . $uriSubject,
            'login: blabla'
        ));
        $this->assertEquals($returnedData, 412);

        // modifying the password of a subject is allowed : 200
        $returnedData = $this->curl($this->getUrl(), CURLOPT_PUT, CURLINFO_HTTP_CODE, array(
            'uri: ' . $uriSubject,
            'password: blabla'
        ));
        $this->assertEquals($returnedData, 200);

        // edit this test taker
        $returnedData = $this->curl($this->getUrl(), CURLOPT_PUT, "data", array(
            'uri: ' . $uriSubject,
            'firstName: patrick',
            'password: blabla'
        ));
        $returnedData = $this->curl($this->getUrl(), CURLOPT_HTTPGET, "data", array(
            'uri: ' . $uriSubject
        ));
        $data = json_decode($returnedData, true);

        $this->assertEquals($data["success"], true);
        $this->assertEquals($data["data"]["uri"], $uriSubject);
        $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_LOGIN, "literal", 'dummy_login');

        foreach ($data["data"]["properties"] as $propertyValue) {
            if ($propertyValue["predicateUri"] == PROPERTY_USER_PASSWORD) {
                $this->assertTrue(\core_kernel_users_Service::getPasswordHash()->verify('blabla', $propertyValue["values"][0]["value"]));
            }
        }

        $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_LASTNAME, "literal", 'patrick');
	}

	public function testCreateTestTaker2()
	{
	    $returnedData = $this->curl($this->getUrl(), CURLOPT_POST, "data", array(
	        'login: 2_dummy_login',
	        'password: dummy'
	    ));
	    $data = json_decode($returnedData, true);
	    $this->assertEquals($data["success"], true);
	    return $data["data"]["uriResource"];
	}

	/**
	 * remove all test takers
	 *
	 * @depends testCreateTestTaker
	 * @depends testCreateTestTaker2
	 */
	public function testDeleteTestTakers($uriSubject, $uri2Subject)
	{
	    // get all test takers

	    $returnedData = $this->curl($this->getUrl());
	    $data = json_decode($returnedData, true);
	    $this->assertEquals($data["success"], true);
	    $this->assertGreaterThanOrEqual(2, count($data["data"]));
	    $beforeDelete = count($data["data"]);


	    $returnedData = $this->curl($this->getUrl(), "DELETE", CURLINFO_HTTP_CODE, array(
	        'uri: ' . $uriSubject
	    ));
	    $this->assertEquals($returnedData, 200);

	    $returnedData = $this->curl($this->getUrl(), "DELETE", "data", array(
	        'uri: ' . $uri2Subject
	    ));
	    $data = json_decode($returnedData, true);
	    $this->assertEquals($data["success"], true);


	     //check the removal
	    $returnedData = $this->curl($this->getUrl());
	    $data = json_decode($returnedData, true);
	    $this->assertEquals($data["success"], true);
	    $this->assertCount($beforeDelete - 2 , $data["data"]);

	}

}

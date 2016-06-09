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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoQtiItem\model;

use oat\oatbox\service\ConfigurableService;
use oat\taoClientDiagnostic\exception\InvalidCallException;

class ValidationService extends ConfigurableService
{
    const SERVICE_ID = 'taoQtiItem/validation';

    private $contentValidation = array(
        'http://www.imsglobal.org/xsd/imsqti_v2p0' => array(
            '/qti/data/qtiv2p0/imsqti_v2p0.xsd'
        ),
        'http://www.imsglobal.org/xsd/apip/apipv1p0/qtiitem/imsqti_v2p1' => array(
                '/qti/data/apipv1p0/Core_Level/Package/apipv1p0_qtiitemv2p1_v1p0.xsd'
        ),
        'default' => array(
                '/qti/data/qtiv2p1/imsqti_v2p1.xsd'
        )
    );

    private $manifestValidation = array(
        'default' => array(
                '/qti/data/imscp_v1p1.xsd',
                '/qti/data/apipv1p0/Core_Level/Package/apipv1p0_imscpv1p2_v1p0.xsd'
        )
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
        foreach($this->contentValidation as $key => &$array){
            foreach($array as &$value){
                $value = __DIR__.$value;
            }
        }
        foreach($this->manifestValidation as $key => &$array){
            foreach($array as &$value){
                $value = __DIR__.$value;
            }
        }
    }

    /**
     * @param string $key the namespace of content to validate
     * @return array of schema for content validation
     */
    public function getContentValidationSchema($key){
        $validationArray = $this->getContentValidation();

        return $this->getSchemas($validationArray, $key);
    }

    /**
     * @param string $key the namespace of manifest to validate
     * @return array of schema for manifest validation
     */
    public function getManifestValidationSchema($key){
        $validationArray = $this->getManifestValidation();

        return $this->getSchemas($validationArray, $key);
    }

    /**
     * @param array $validationArray list of xsds for namespaces
     * @param string $key the namespace
     * @return array schemas for validation
     */
    protected function getSchemas($validationArray, $key){
        if(isset($validationArray[$key])){
            return $validationArray[$key];
        }

        return $validationArray['default'];
    }

    /**
     * @return array
     */
    protected function getContentValidation(){
        return $this->contentValidation;
    }

    /**
     * @return array
     */
    protected function getManifestValidation(){
        return $this->manifestValidation;
    }
}
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */
namespace oat\generis\model\user;

use common_ext_ExtensionException;
use common_ext_ExtensionsManager;

/**
 * Class PasswordConstraintsService used to verify password strength
 * @package generis
 */
class PasswordConstraintsService extends \tao_models_classes_Service
{
    /**
     * @var array
     */
    protected $validators = array();

    protected function __construct()
    {
        parent::__construct();
        $config = $this->getConfig();
        $this->register( $config );
    }


    /**
     * Test if password pass all constraints rules
     *
     * @param $password
     *
     * @return bool
     */
    public function validate( $password )
    {
        $result = true;
        /** @var \tao_helpers_form_Validator $validator */
        foreach ($this->validators as $validator) {
            $result &= $validator->evaluate( $password );
        }

        return (boolean) $result;
    }

    /**
     * Set up all validator according configuration file
     *
     * @param $config
     */
    protected function register($config)
    {
        $this->validators = array();

        if (array_key_exists( 'length', $config ) && (int) $config['length']) {
            $this->validators[] = new \tao_helpers_form_validators_Length( array( 'min' => (int) $config['length'] ) );
        }

        if (( array_key_exists( 'upper', $config ) && $config['upper'] )
            || ( array_key_exists( 'lower', $config ) && $config['lower'] )
        ) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include at least one letter' ),
                    'format'  => '/\pL/'
                ), 'letters'
            );
        }

        if (( array_key_exists( 'upper', $config ) && $config['upper'] )) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include upper case letters' ),
                    'format'  => '/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/'
                ), 'caseUpper'
            );
        }

        if (( array_key_exists( 'lower', $config ) && $config['lower'] )) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include lower case letters' ),
                    'format'  => '/(\p{Ll}+)/'
                ), 'caseLower'
            );
        }

        if (array_key_exists( 'number', $config ) && $config['number']) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include at least one number' ),
                    'format'  => '/\pN/'
                ), 'number'
            );
        }

        if (array_key_exists( 'spec', $config ) && $config['spec']) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include at least one special letter' ),
                    'format'  => '/[^p{Ll}\p{Lu}\pL\pN]/'
                ), 'spec'
            );
        }

    }

    /**
     * Any errors that was found during validation process
     * @return array
     */
    public function getErrors()
    {
        $errors = array();
        /** @var \tao_helpers_form_Validator $validator */
        foreach ($this->validators as $validator) {
            $errors[] = $validator->getMessage();
        }

        return $errors;
    }

    /**
     * List of active validators
     * @return array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Retrieve at least default config ( if extension is not yet installed )
     * @return array
     */
    protected function getConfig()
    {

        try {
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById( 'generis' );

            $config = $ext->getConfig( 'passwords' );

        } catch ( common_ext_ExtensionException $e ) {
            $config = require_once( __DIR__ . '/../../config/default/passwords.conf.php' );

        }

        return (array) $config['constrains'];

    }

}
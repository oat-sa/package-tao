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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\taoTestTaker\models;

/**
 * A custom subject CSV importer
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoSubjects

 */
class CsvImporter extends \tao_models_classes_import_CsvImporter
{
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getExludedProperties()
     */
    protected function getExludedProperties()
    {
       return array_merge(parent::getExludedProperties(), array(
           PROPERTY_USER_DEFLG,
           PROPERTY_USER_ROLES,
           PROPERTY_USER_LASTEXTENSION,
           PROPERTY_USER_FIRSTTIME,
           PROPERTY_USER_TIMEZONE
       ));
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getStaticData()
     */
    protected function getStaticData()
    {
        $lang = \tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG)->getUri();

        return array(
            PROPERTY_USER_DEFLG => $lang,
            PROPERTY_USER_TIMEZONE => TIME_ZONE,
            PROPERTY_USER_ROLES => INSTANCE_ROLE_DELIVERY
        );
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getAdditionAdapterOptions()
     */
    protected function getAdditionAdapterOptions()
    {
        $returnValue = array(
            'callbacks' => array(
                '*' => array('trim'),
                PROPERTY_USER_PASSWORD => array('oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode')
            )
        );

        return $returnValue;
    }

    /**
     * Wrapper for password hash
     *
     * @param  string $value
     * @return string
     */
    public static function taoSubjectsPasswordEncode($value)
    {
        return \core_kernel_users_Service::getPasswordHash()->encrypt($value);
    }

}

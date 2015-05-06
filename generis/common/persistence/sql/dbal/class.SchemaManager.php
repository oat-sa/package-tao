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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 *
 */
 class common_persistence_sql_dbal_SchemaManager extends common_persistence_sql_SchemaManager{
    
    private $dbalSchemaManager;
    
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param d $dbalSchemaManager
     */
    public function __construct($dbalSchemaManager){
        $this->dbalSchemaManager = $dbalSchemaManager;
    }
    
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    protected function getSchemaManager() {
        return $this->dbalSchemaManager;
    }
    
    
    /**
     * Short description of method getIndexAlreadyExistsErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getIndexAlreadyExistsErrorCode()
    {
        return '42P07';
    }
    
    /**
     * Short description of method getIndexAlreadyExistsErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getColumnNotFoundErrorCode()
    {
        return '42703';
    }
       



    



    

    
    
}
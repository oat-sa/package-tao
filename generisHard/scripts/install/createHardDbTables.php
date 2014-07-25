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
 *
 */


$dbWrapper = \core_kernel_classes_DbWrapper::singleton();

$schema = $dbWrapper->getSchemaManager()->createSchema();
$origSchema = clone $schema;

// create class_to_table
$class_to_table = $schema->createTable("class_to_table");
$class_to_table->addColumn("id", "integer",array("notnull" => true,"autoincrement" => true));
$class_to_table->addColumn("uri", "string",array("notnull" => null));
$class_to_table->addColumn("table", "string",array("notnull" => null,"length" => 64));
$class_to_table->addColumn("topclass", "string",array("notnull" => null));
$class_to_table->addIndex(array("uri"),"idx_class_to_table_uri");
$class_to_table->addIndex(array("id"),"id");
$class_to_table->setPrimaryKey(array("id"));
$class_to_table->addOption('engine' , 'MyISAM');


// create class_additional_properties
$class_additional_properties = $schema->createTable("class_additional_properties");
$class_additional_properties->addColumn("class_id", "integer",array("notnull" => true));
$class_additional_properties->addColumn("property_uri", "string",array("length" => 255,"notnull" => null));
$class_additional_properties->setPrimaryKey(array("class_id","property_uri"));
$class_additional_properties->addOption('engine' , 'MyISAM');



$resource_to_table = $schema->createTable("resource_to_table");
$resource_to_table->addColumn("id", "integer",array("notnull" => true,"autoincrement" => true));
$resource_to_table->addColumn("uri", "string",array("notnull" => null));
$resource_to_table->addColumn("table", "string",array("notnull" => null,"length" => 64));
$resource_to_table->addIndex(array("uri"),"idx_resource_to_table_uri");
$resource_to_table->addIndex(array("id"),"id");
$resource_to_table->setPrimaryKey(array("id"));
$resource_to_table->addOption('engine' , 'MyISAM');


$resource_has_class = $schema->createTable("resource_has_class");
$resource_has_class->addColumn("resource_id", "integer",array("notnull" => true));
$resource_has_class->addColumn("class_id", "integer",array("notnull" => true));
$resource_has_class->setPrimaryKey(array("resource_id","class_id"));
$resource_has_class->addOption('engine' , 'MyISAM');

$sql = $dbWrapper->getPlatForm()->getMigrateSchemaSql($origSchema,$schema);

try{
    foreach ($sql as $query){
        $dbWrapper->exec($query);
    }      
}
catch (\PDOException $e){
    throw new \Exception("An error occured during table creation " . $e->getMessage());
}



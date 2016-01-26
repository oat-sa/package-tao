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

namespace oat\taoOutcomeRds\scripts\update;

use oat\taoOutcomeRds\model\RdsResultStorage;

class Updater extends \common_ext_ExtensionUpdater 
{

	/**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {
        
        $currentVersion = $initialVersion;
		if ($currentVersion == '1.0' || $currentVersion == '1.0.1' || $currentVersion == '1.0.2' ) {
			$currentVersion = '1.0.3';
		}

		if ($currentVersion == '1.0.3') {

			//get variables
			$persistence = \common_persistence_Manager::getPersistence('default');
			$sql = 'SELECT * FROM ' . RdsResultStorage::VARIABLES_TABLENAME . ' WHERE '. RdsResultStorage::VARIABLE_VALUE .' IS NULL';
			$countSql = 'SELECT count(*) FROM ' . RdsResultStorage::VARIABLES_TABLENAME . ' WHERE '. RdsResultStorage::VARIABLE_VALUE .' IS NULL';

			//update variable storage table schema
			$schema = $persistence->getDriver()->getSchemaManager()->createSchema();
			$fromSchema = clone $schema;

			$tableVariables = $schema->getTable(RdsResultStorage::VARIABLES_TABLENAME);
			if(!$tableVariables->hasColumn(RdsResultStorage::VARIABLE_VALUE)){
				$tableVariables->addColumn(RdsResultStorage::VARIABLE_VALUE, "text", array("notnull" => false));
				$queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);

				foreach ($queries as $query) {
					$persistence->exec($query);
				}

				$sql = 'SELECT * FROM ' . RdsResultStorage::VARIABLES_TABLENAME;
				$countSql = 'SELECT count(*) FROM ' . RdsResultStorage::VARIABLES_TABLENAME;
			}

			$params = array();
			$entries = $persistence->query($countSql, $params)->fetchColumn();

			$limit = 1000;
			for($i = 0; $i <= $entries; $i+=$limit){
				$newSql = $sql . ' ORDER BY ' . RdsResultStorage::VARIABLES_TABLE_ID;
				$query = $persistence->getPlatform()->limitStatement($newSql, $limit,$i);
				$variables = $persistence->query($query);

				//store information the new way
				foreach($variables as $variable){
					//get Variable informations
					$variableSql = 'SELECT * FROM ' . RdsResultStorage::RESULT_KEY_VALUE_TABLE_NAME . '
				WHERE ' . RdsResultStorage::RESULTSKV_FK_COLUMN .' = ?';
					$params = array($variable[RdsResultStorage::VARIABLES_TABLE_ID]);
					$variableValues = $persistence->query($variableSql, $params);

					if (class_exists($variable[RdsResultStorage::VARIABLE_CLASS])) {
						$resultVariable = new $variable[RdsResultStorage::VARIABLE_CLASS]();
					} else {
						$resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
					}

					foreach($variableValues as $variableValue){
						$setter = 'set' . ucfirst($variableValue[RdsResultStorage::KEY_COLUMN]);
						$value = $variableValue[RdsResultStorage::VALUE_COLUMN];
						if (method_exists($resultVariable, $setter) && !is_null($value)) {
							if ($variableValue[RdsResultStorage::KEY_COLUMN] == 'value' || $variableValue[RdsResultStorage::KEY_COLUMN] == 'candidateResponse') {
								$value = base64_decode($value);
							}

							$resultVariable->$setter($value);
						}
					}

					$sqlUpdate = 'UPDATE ' . RdsResultStorage::VARIABLES_TABLENAME . ' SET ' . RdsResultStorage::VARIABLE_VALUE . ' = ? WHERE ' . RdsResultStorage::VARIABLES_TABLE_ID . ' = ?';
					$paramsUpdate = array(serialize($resultVariable), $variable[RdsResultStorage::VARIABLES_TABLE_ID]);
					$persistence->exec($sqlUpdate, $paramsUpdate);

				}
			}

			//remove kv table
			$schema = $persistence->getDriver()->getSchemaManager()->createSchema();
			$fromSchema = clone $schema;

			$tableVariables = $schema->getTable(RdsResultStorage::VARIABLES_TABLENAME);
			$resultKv = $schema->dropTable(RdsResultStorage::RESULT_KEY_VALUE_TABLE_NAME);
			$tableVariables->dropColumn(RdsResultStorage::VARIABLE_CLASS);
			$queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
			foreach ($queries as $query) {
				$persistence->exec($query);
			}

			$currentVersion = '1.1.0';
		}

		return $currentVersion;
	}
}
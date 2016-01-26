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
use oat\generis\model\data\ModelManager;
use oat\generis\model\kernel\persistence\file\FileIterator;
use oat\tao\model\extension\ExtensionModel;
use oat\tao\scripts\update\ModelFixer;

require_once dirname(__FILE__) . '/../includes/raw_start.php';

$persistence = common_persistence_SqlPersistence::getPersistence('default');

$smoothIterator = new core_kernel_persistence_smoothsql_SmoothIterator($persistence, array(1));
$count = 0;
foreach ($smoothIterator as $triple) {
    $count++;
}
echo PHP_EOL.$count.' user triples in ontology'.PHP_EOL;

$modelIds = array_diff(
    core_kernel_persistence_smoothsql_SmoothModel::getReadableModelIds(),
    core_kernel_persistence_smoothsql_SmoothModel::getUpdatableModelIds()
);
$smoothIterator = new core_kernel_persistence_smoothsql_SmoothIterator($persistence, $modelIds);
$count = 0;
foreach ($smoothIterator as $triple) {
    $count++;
}
echo PHP_EOL.$count.' system triples in ontology'.PHP_EOL;

$files = array();
$rdfIterator = new AppendIterator();
foreach (common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
    $model = new ExtensionModel($ext);
    $rdfIterator->append($model);
}

$count = 0;
foreach ($rdfIterator as $triple) {
    $count++;
}
echo PHP_EOL.$count.' triples in rdfs'.PHP_EOL;

$diff = helpers_RdfDiff::create($smoothIterator, $rdfIterator);
echo PHP_EOL.'Diff: '.PHP_EOL.$diff->dump();

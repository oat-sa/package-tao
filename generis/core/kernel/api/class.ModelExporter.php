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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */               

class core_kernel_api_ModelExporter {
    
    /**
     * Export the entire ontology
     * 
     * @return string
     */
    public static function exportAll() {
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $result = $dbWrapper->query('SELECT DISTINCT "subject", "predicate", "object", "l_language" FROM "statements"');
        return self::statement2rdf($result);
    }
    
    /**
     * Export models by id
     * 
     * @param array $modelIds
     * @return string
     */
    public static function exportModels($modelIds) {
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $result = $dbWrapper->query('SELECT DISTINCT "subject", "predicate", "object", "l_language" FROM "statements" 
            WHERE "modelid" IN (\''.implode('\',\'', $modelIds).'\')');
        	
        common_Logger::i('Found '.$result->rowCount().' entries for models '.implode(',', $modelIds));
        return self::statement2rdf($result);
    }
    
    /**
     * Export a model by URI
     * 
     * @param array $modelUri
     * @return string
     */
    public static function exportModelByUri($modelUri) {
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $result = $dbWrapper->query('SELECT modelid FROM "models"  WHERE "modeluri" = ?', array($modelUri))->fetch(PDO::FETCH_ASSOC);
        self::exportModels($result['modelid']);
    }
    
    /**
     * @ignore
     */
    private static function statement2rdf(PDOStatement $statement) {
        $graph = new EasyRdf_Graph();
        while($r = $statement->fetch()){
            if (isset($r['l_language']) && !empty($r['l_language'])) {
                $graph->addLiteral($r['subject'], $r['predicate'], $r['object'], $r['l_language']);
            } elseif (common_Utils::isUri($r['object'])) {
                $graph->add($r['subject'], $r['predicate'], $r['object']);
            } else {
                $graph->addLiteral($r['subject'], $r['predicate'], $r['object']);
            }
        }
        
        $format = EasyRdf_Format::getFormat('rdfxml');
        return $graph->serialise($format);
    }
    
}
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * Utility class for package core\kernel\persistence\smoothsql.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @author Cédric Alfonsi <cerdic.alfonsi@tudor.lu>
 */
class core_kernel_persistence_smoothsql_Utils
{

    /**
     * Sort a given $dataset by language.
     *
     * @param mixed dataset A PDO dataset.
     * @param string langColname The name of the column corresponding to the language of results.
     * @return array An array representing the sorted $dataset.
     */
    static public function sortByLanguage($persistence, $dataset, $langColname)
    {
        $returnValue = array();
        
        $selectedLanguage = \common_session_SessionManager::getSession()->getDataLanguage();
        $defaultLanguage = DEFAULT_LANG;
        $fallbackLanguage = '';
        				  
        $sortedResults = array(
            $selectedLanguage => array(),
            $defaultLanguage => array(),
            $fallbackLanguage => array()
        );

        foreach ($dataset as $row) {
        	$sortedResults[$row[$langColname]][] = array(
        	    'value' => $persistence->getPlatForm()->getPhpTextValue($row['object']), 
        	    'language' => $row[$langColname]
            );
        }
        
        $returnValue = array_merge(
            $sortedResults[$selectedLanguage], 
            (count($sortedResults) > 2) ? $sortedResults[$defaultLanguage] : array(),
            $sortedResults[$fallbackLanguage]
        );
        
        return $returnValue;
    }

    /**
     * Get the first language encountered in the $values associative array.
     *
     * @param  array values
     * @return array
     */
    static public function getFirstLanguage($values)
    {
        $returnValue = array();

        if (count($values) > 0) {
            $previousLanguage = $values[0]['language'];
        
            foreach ($values as $value) {
                if ($value['language'] == $previousLanguage) {
                    $returnValue[] = $value['value'];
                } else {
                    break;
                }
            }
        }

        return (array) $returnValue;
    }

    /**
     * Filter a $dataset by language.
     *
     * @param mixed dataset
     * @param string langColname
     * @return array
     */
    static public function filterByLanguage(common_persistence_SqlPersistence $persistence, $dataset, $langColname)
    {
        $returnValue = array();
        
        $result = self::sortByLanguage($persistence, $dataset, $langColname);
        $returnValue = self::getFirstLanguage($result);
        
        return $returnValue;
    }

    /**
     * Short description of method identifyFirstLanguage
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array values
     * @return string
     */
    static public function identifyFirstLanguage($values)
    {
        $returnValue = '';

        if (count($values) > 0) {
            $previousLanguage = $values[0]['language'];
            $returnValue = $previousLanguage;
            
            foreach ($values as $value) {
                if ($value['language'] == $previousLanguage) {
                    continue;
                } else {
                    $returnValue = $previousLanguage;
                    break;
                }
            }
        }

        return $returnValue;
    }

    /**
     * Build a SQL search pattern on basis of a pattern and a comparison mode.
     *
     * @param  tring pattern A value to compare.
     * @param  boolean like The manner to compare values. If set to true, the LIKE SQL operator will be used. If set to false, the = (equal) SQL operator will be used.
     * @return string
     */
    static public function buildSearchPattern(common_persistence_SqlPersistence $persistence, $pattern, $like = true)
    {
        $returnValue = '';
        
        // Take care of RDFS Literals!
        if ($pattern instanceof core_kernel_classes_Literal) {
            $pattern = $pattern->__toString();
        }
        
        switch (gettype($pattern)) {
            case 'object' :
                if ($pattern instanceof core_kernel_classes_Resource) {
                    $returnValue = '= ' . $persistence->quote($pattern->getUri());
                } else {
                    common_Logger::w('non ressource as search parameter: '. get_class($pattern), 'GENERIS');
                }
                break;
            
            default:
                $patternToken = $pattern;
                $wildcard = mb_strpos($patternToken, '*', 0, 'UTF-8') !== false;
                $object = trim(str_replace('*', '%', $patternToken));
                
                if ($like) {
                    if (!$wildcard && !preg_match("/^%/", $object)) {
                        $object = "%" . $object;
                    }
                    if (!$wildcard && !preg_match("/%$/", $object)) {
                        $object = $object . "%";
                    }
                    if (!$wildcard && $object === '%') {
                        $object = '%%';
                    }
                    $returnValue .= 'LIKE '. $persistence->quote($object);
                } else {
                    $returnValue .= '= '. $persistence->quote($patternToken);
                }
                break;
        }
        
        return $returnValue;
    }
    
    static public function buildPropertyQuery(core_kernel_persistence_smoothsql_SmoothModel $model, $propertyUri, $values, $like, $lang = '')
    {
        $persistence = $model->getPersistence();
        
        // Deal with predicate...
        $predicate = $persistence->quote($propertyUri);
        
        // Deal with values...
        if (is_array($values) === false) {
            $values = array($values);
        }
        
        $valuePatterns = array();
        foreach ($values as $val) {
            $valuePatterns[] = 'object ' . self::buildSearchPattern($persistence, $val, $like);
        }
        
        $sqlValues = implode(' OR ', $valuePatterns);
        
        // Deal with language...
        $sqlLang = '';
        if (empty($lang) === false) {
            $sqlLang = ' AND (' . self::buildLanguagePattern($persistence, $lang) . ')';
        }
        
        $query = "SELECT DISTINCT subject FROM statements WHERE (predicate = ${predicate}) AND (${sqlValues}${sqlLang})"
            .' AND modelid IN ('.implode(',', $model->getReadableModels()).')';
        
        return $query;
    }
    
    static public function buildLanguagePattern(common_persistence_SqlPersistence $persistence, $lang = '')
    {
        $languagePattern = '';
        
        if (empty($lang) === false) {
            $sqlEmpty = $persistence->quote('');
            $sqlLang = $persistence->quote($lang);
            $languagePattern = "l_language = ${sqlEmpty} OR l_language = ${sqlLang}";
        }
        
        return $languagePattern;
    }
    
    static public function buildUnionQuery($propertyQueries) {
        
        if (count($propertyQueries) === 0) {
            return false;
        } else if (count($propertyQueries) === 1) {
            return $propertyQueries[0];
        } else {
            // Add parenthesis.
            $finalPropertyQueries = array();
            foreach ($propertyQueries as $query) {
                $finalPropertyQueries[] = "(${query})";
            }
            
            return implode(' UNION ALL ', $finalPropertyQueries);
        }
    }
    
    static public function buildFilterQuery(core_kernel_persistence_smoothsql_SmoothModel $model, $classUri, array $propertyFilters, $and = true, $like = true, $lang = '', $offset = 0, $limit = 0, $order = '', $orderDir = 'ASC')
    {
        $persistence = $model->getPersistence();
        
        // Deal with target classes.
        if (is_array($classUri) === false) {
            $classUri = array($classUri);
        }
        
        $propertyQueries = array(self::buildPropertyQuery($model, RDF_TYPE, $classUri, false));
        foreach ($propertyFilters as $propertyUri => $filterValues) {
            $propertyQueries[] = self::buildPropertyQuery($model, $propertyUri, $filterValues, $like, $lang);
        }
        
        $unionQuery = self::buildUnionQuery($propertyQueries);
        
        if (($propCount = count($propertyFilters)) === 0) {
            $query = self::buildPropertyQuery($model, RDF_TYPE, $classUri, false, $lang);
        } else {
            $unionCount = ($and === true) ? ($propCount + 1) : 2;
            $query = "SELECT subject FROM (${unionQuery}) AS unionq GROUP BY subject HAVING count(*) >= ${unionCount}";
        }

        // Order...
        if (empty($order) === false) {
            $orderPredicate = $persistence->quote($order);
            
            $sqlLang = '';
            if (empty($lang) === false) {
                $sqlEmptyLang = $persistence->quote('');
                $sqlRequestedLang = $persistence->quote($lang);
                $sqlLang = " AND (l_language = ${sqlEmptyLang} OR l_language = ${sqlRequestedLang})";
            }
            
            $sqlOrderFilter = "mainq.subject = orderq.subject AND predicate = ${orderPredicate}${sqlLang}";
            
            $query = "SELECT mainq.subject, orderq.object FROM (${query}) AS mainq JOIN ";
            $query .= "statements AS orderq ON (${sqlOrderFilter}) ORDER BY orderq.object ${orderDir}";
        }
        
        // Limit...
        if ($limit > 0) {
            $query = $persistence->getPlatForm()->limitStatement($query, $limit, $offset);
        }
        
        // Suffix order...
        if (empty($order) === false) {
            $query = "SELECT subject FROM (${query}) as rootq";
        }
        
        return $query;
    }
}
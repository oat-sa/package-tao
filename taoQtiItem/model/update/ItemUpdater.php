<?php
/*
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 *
 */

namespace oat\taoQtiItem\model\update;

use oat\taoQtiItem\model\qti\ParserFactory;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

abstract class ItemUpdater
{
    protected $itemPath     = '';
    protected $checkedFiles = array();

    /**
     * Init the item updater with the item directory path
     * 
     * @param string $itemRootPath
     * @throws \common_Exception
     */
    public function __construct($itemRootPath)
    {
        if (file_exists($itemRootPath)) {
            $this->itemPath = $itemRootPath;
        } else {
            throw new \common_Exception('the given item root path does not exist');
        }
    }

    /**
     * Update all the item files found within the $itemRootPath
     * @param boolean $changeItemContent - tells if the item files will be written with the updated content or not
     * @return array of modified item instances
     */
    public function update($changeItemContent = false)
    {
        $returnValue = array();
        $objects     = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->itemPath), RecursiveIteratorIterator::SELF_FIRST);
        $i = 0;
        $fixed = 0;
        
        foreach ($objects as $itemFile => $cursor) {

            if (is_file($itemFile)) {
                
                $this->checkedFiles[$itemFile] = false;

                if (basename($itemFile) === 'qti.xml') {

                    $i++;
                    $xml = new \DOMDocument();
                    $xml->load($itemFile);

                    $parser = new ParserFactory($xml);
                    $item   = $parser->load();
                    \common_Logger::i('checking item #'.$i.' id:'.$item->attr('identifier').' file:'.$itemFile);

                    if ($this->updateItem($item, $itemFile)) {
                        $this->checkedFiles[$itemFile] = true;
                        $returnValue[$itemFile]        = $item;
                        \common_Logger::i('fixed required for #'.$i.' id:'.$item->attr('identifier').' file:'.$itemFile);
                        if ($changeItemContent) {
                            $fixed++;
                            \common_Logger::i('item fixed #'.$i.' id:'.$item->attr('identifier').' file:'.$itemFile);
                            file_put_contents($itemFile, $item->toXML());
                        }
                    }
                }
            }
        }

        \common_Logger::i('total item fixed : '.$fixed);
        return $returnValue;
    }

    /**
     * Get the list of checked files 
     * @return array
     */
    public function getCheckedFiles()
    {
        return $this->checkedFiles;
    }

    /**
     * Try updating an single item instance,
     * Returns true if the content has been changed, false otherwise
     *
     * @param oat\taoQtiItem\modal\Item $item
     * @param string $itemFile
     * @return boolean
     */
    abstract protected function updateItem(\oat\taoQtiItem\model\qti\Item $item, $itemFile);
}
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
namespace oat\tao\model\import;

use oat\oatbox\action\Action;
use oat\generis\model\kernel\persistence\file\FileIterator;
use oat\generis\model\data\ModelManager;
/**
 * System import of RDF files, will not transform URIs
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class ImportRdf implements Action
{

    public function __invoke($params)
    {
        if (count($params) < 1) {
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Usage: ImportRdf RDF_FILE [MODEL_ID]'));
        }

        $filename = array_shift($params);
        if (!file_exists($filename) || !is_readable($filename)) {
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Unable to open file %s', $filename));
        }
        
        if (empty($params)) {
            $iterator = new FileIterator($filename);
        } else {
            $modelId = array_shift($params);
            $iterator = new FileIterator($filename, $modelId);
        }
        
        $rdf = ModelManager::getModel()->getRdfInterface();
        $triples = 0;
        foreach ($iterator as $triple) {
            $triples++;
            $rdf->add($triple);
        }
        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('Successfully imported %s tripples', $triples));

    }
}

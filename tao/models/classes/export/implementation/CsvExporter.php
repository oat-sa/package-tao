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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\tao\model\export\implementation;

use SPLTempFileObject;

/**
 * Class CsvExporter
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package oat\tao\model\export
 */
class CsvExporter extends AbstractFileExporter
{
    /**
     * @var string value of `Content-Type` header
     */
    protected $contentType = 'text/csv; charset=UTF-8';

    /**
     * @param boolean $columnNames array keys will be used in the first line of CSV data as column names.
     * @param boolean $download
     * @param string $delimiter sets the field delimiter (one character only).
     * @param string $enclosure sets the field enclosure (one character only).
     * @return string
     */
    public function export($columnNames = false, $download = false, $delimiter = ",", $enclosure = '"')
    {
        $data = $this->data;

        if ($columnNames) {
            array_unshift($data, array_keys($data[0]));
        }
        $file = new SPLTempFileObject();
        foreach ($data as $row) {
            $file->fputcsv($row, $delimiter, $enclosure);
        }

        $file->rewind();
        $exportData = '';
        while (!$file->eof()) {
            $exportData .= $file->fgets();
        }
        $exportData = trim($exportData);

        if ($download) {
            $this->download($exportData, 'export.csv');
        } else {
            return $exportData;
        }
    }
}
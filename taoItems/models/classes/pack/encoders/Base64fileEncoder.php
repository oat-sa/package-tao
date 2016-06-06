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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */
namespace oat\taoItems\model\pack\encoders;

use oat\taoItems\model\pack\ExceptionMissingAsset;
use tao_helpers_File;

/**
 * Class Base64fileEncoder
 * Helper, encode file by uri for embedding  using base64 algorithm
 * @package oat\taoItems\model\pack\encoders
 */
class Base64fileEncoder implements Encoding
{
    /**
     * @var string
     */
    private $path;

    /**
     * Applied data-uri format placeholder
     */
    const DATA_PREFIX = 'data:%s;base64,%s';

    /**
     * Base64fileEncoder constructor.
     *
     * @param string $path base path to resource
     */
    public function __construct( $path = '' )
    {
        $this->path = $path;
    }


    /**
     * @param string $data path to file
     *
     * @return string
     * @throws ExceptionMissingAsset
     */
    public function encode( $data )
    {
        //skip  if external resource
        if (filter_var( $data, FILTER_VALIDATE_URL )) {
            return $data;
        }

        $fullPath = $this->path . DIRECTORY_SEPARATOR . $data;
        if (file_exists( $fullPath )) {
            return sprintf(self::DATA_PREFIX, tao_helpers_File::getMimeType($fullPath), base64_encode( file_get_contents( $fullPath ) ));
        }

        throw new ExceptionMissingAsset( 'Assets not found ' . $this->path . '/' . $data );
    }
}

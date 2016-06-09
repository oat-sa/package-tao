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

namespace oat\taoItems\model\pack;

use oat\taoItems\model\pack\encoders\Encoding;
use tao_models_classes_Service;

/**
 * Class EncoderService
 * Factory retrieve encoder by his name
 * @package oat\taoItems\model\pack
 */
class EncoderService extends tao_models_classes_Service
{
    /**
     * @param $type
     *
     * @return Encoding
     * @throws ExceptionMissingEncoder
     */
    public function get( $type )
    {
        $class = __NAMESPACE__ . '\\encoders\\' . ucfirst( $type ) . 'Encoder';
        if (class_exists( $class ) && in_array(
                'oat\taoItems\model\pack\encoders\Encoding',
                class_implements( $class )
            )
        ) {
            $result = new $class;
            if (method_exists( $result, '__construct' )) {
                call_user_func_array(
                    array( $result, '__construct' ),
                    array_slice( (array) func_get_args(), 1, func_num_args() )
                );
            }
            return $result;

        }
        throw new ExceptionMissingEncoder( 'Encoder missing : ' .  $class );
    }
}

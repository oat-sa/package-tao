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
 * Copyright (c) 2015
 *
 */

use oat\generis\model\user\PasswordConstraintsService;

class tao_install_checks_PasswordConformity extends common_configuration_Component
{

    /**
     * @return common_configuration_Report
     */
    public function check()
    {
        $content = json_decode( file_get_contents( 'php://input' ), true );

        if (PasswordConstraintsService::singleton()->validate( $content['value']['password'] )) {
            $report = new common_configuration_Report(
                common_configuration_Report::VALID,
                'Password is strong enough',
                $this
            );
        } else {
            $report = new common_configuration_Report(
                common_configuration_Report::INVALID,
                implode( "</br>", PasswordConstraintsService::singleton()->getErrors() ),
                $this
            );
        }

        return $report;
    }
}

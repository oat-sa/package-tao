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
class tao_install_checks_Opcache extends common_configuration_Component
{

    /**
     * @return common_configuration_Report
     */
    public function check()
    {
        $error = null;

        if ( ! function_exists('opcache_get_configuration')) {
            $error = 'You can install OPcache extension to improve performance';
        } else {

            $configuration = opcache_get_configuration();

            if ( ! $configuration['directives']['opcache.enable']) {
                $error = 'You can enable OPcache extension to improve performance';
            }
        }

        $report = new common_configuration_Report(
            null !== $error ? common_configuration_Report::INVALID : common_configuration_Report::VALID,
            null !== $error ? $error : 'OPcache is installed',
            $this
        );

        return $report;
    }
}

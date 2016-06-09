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
 */

/**
 * This helper aims at providing utility methods to render
 * reports into TXT.
 * 
 *
 */
class helpers_Report {
   
    const AUTOSENSE = 'autosense';
    
    /**
     * Contains the logic to render a report and its children to the command line
     *
     * @param common_report_Report $report A report to be rendered.
     * @param boolean $useColor
     * @param integer $intend the intend of the message.
     * @return string The shell output of $report.
     */
    public static function renderToCommandLine(common_report_Report $report, $useColor = self::AUTOSENSE, $intend = 0) {
        switch ($report->getType()) {
            case common_report_Report::TYPE_SUCCESS:
                $color = '0;32'; // green
                break;
            
            case common_report_Report::TYPE_WARNING:
                $color = '1;33'; // yellow
                break;
            
            case common_report_Report::TYPE_ERROR:
                $color = '1;31'; // red
                break;
            
            default:
                $color = '0;37'; // light grey
        }
        if ($useColor == self::AUTOSENSE) {
            $useColor = !helpers_PlatformInstance::isWindows();
        }
            
        $output =  ($useColor ? "\033[".$color.'m' : '')
            .($intend > 0 ? str_repeat(' ', $intend) : '')
            .$report->getMessage()
            .($useColor ? "\033[0m" : '').PHP_EOL;
        foreach ($report as $child) {
            $output .= self::renderToCommandline($child, $useColor, $intend + 2);
        }
        return $output;
    }
    
}
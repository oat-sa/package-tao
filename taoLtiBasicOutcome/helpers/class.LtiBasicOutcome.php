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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 
 * 
 */

class taoLtiBasicOutcome_helpers_LtiBasicOutcome
{

    public static function buildXMLMessage($sourcedId, $grade, $operation){
            $language = 'en-us';
            $operation = 'replaceResultRequest';
           $body = '<?xml version = "1.0" encoding = "UTF-8"?>
                <imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/lis/oms1p0/pox">
                    <imsx_POXHeader>
                        <imsx_POXRequestHeaderInfo>
                            <imsx_version>V1.0</imsx_version>
                            <imsx_messageIdentifier>'.uniqid().'</imsx_messageIdentifier>
                        </imsx_POXRequestHeaderInfo>
                    </imsx_POXHeader>
                    <imsx_POXBody>
                        <'.$operation.'>
                            <resultRecord>
                                <sourcedGUID>
                                    <sourcedId>'.$sourcedId.'</sourcedId>
                                </sourcedGUID>
                                <result>
                                    <resultScore>
                                        <language>'.$language.'</language>
                                        <textString>'.$grade.'</textString>
                                    </resultScore>
                                </result>
                            </resultRecord>
                        </'.$operation.'>
                    </imsx_POXBody>
                </imsx_POXEnvelopeRequest>';

                return $body;
    }


   
}

?>
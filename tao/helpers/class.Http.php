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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

use oat\tao\helpers\FileUploadException;

/**
 * Description of class
 *
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 */
class tao_helpers_Http
{

    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return boolean|Ambigous <unknown, string>
     */
    public static function getDigest()
    {
        // seems apache-php is absorbing the header
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $digest = $_SERVER['PHP_AUTH_DIGEST'];
            // most other servers
        } elseif (isset($_SERVER['HTTP_AUTHENTICATION'])) {
            if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'digest') === 0) {
                $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            }
        } else {
            return false;
        }

        return $digest;
    }

    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param string $digest
     * @return Ambigous <boolean, multitype:unknown >
     */
    public static function parseDigest($digest)
    {
        // protect against missing data
        $needed_parts = array(
            'nonce' => 1,
            'nc' => 1,
            'cnonce' => 1,
            'qop' => 1,
            'username' => 1,
            'uri' => 1,
            'response' => 1
        );
        $data = array();
        $keys = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $digest, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }
        return $needed_parts ? false : $data;
    }

    public static function getHeaders()
    {
        return apache_request_headers();
    }

    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return string
     */
    public static function getFiles()
    {
        return $_FILES;
    }

    /**
     * Get the files data from an HTTP file upload (ie. from the $_FILES)
     * @author "Bertrand Chevrier <bertrand@taotesting.com>
     * @param string the file field name 
     * @return array the file data
     * @throws common_exception_Error in case of wrong upload
     */
    public static function getUploadedFile($name)
    {

        $files = self::getFiles();
        $fileData = $files[$name];
        if (isset($files[$name])) {

            //check for upload errors
            if (isset($fileData['error']) && $fileData['error'] != UPLOAD_ERR_OK) {
                switch ($fileData['error']) {
                    case UPLOAD_ERR_NO_FILE:
                        throw new FileUploadException('No file sent.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new FileUploadException('Exceeded filesize limit of ' . tao_helpers_Environment::getFileUploadLimit());
                    default:
                        throw new common_exception_Error('Upload fails, check errors');
                }
            }
        }
        if (!is_uploaded_file($fileData['tmp_name'])) {
            throw new common_exception_Error('Non uploaded file in filedata, potential attack');
        }
        return $fileData;
    }

    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param string $supportedMimeTypes
     * @param string $requestedMimeTypes
     * @throws common_exception_NotAcceptable
     * @return string|NULL
     */
    public static function acceptHeader($supportedMimeTypes = null, $requestedMimeTypes = null)
    {
        $acceptTypes = Array();
        $accept = strtolower($requestedMimeTypes);
        $accept = explode(',', $accept);
        foreach ($accept as $a) {
            // the default quality is 1.
            $q = 1;
            // check if there is a different quality
            if (strpos($a, ';q=')) {
                // divide "mime/type;q=X" into two parts: "mime/type" i "X"
                list ($a, $q) = explode(';q=', $a);
            }
            // mime-type $a is accepted with the quality $q
            // WARNING: $q == 0 means, that mime-type isn’t supported!
            $acceptTypes[$a] = $q;
        }
        arsort($acceptTypes);
        if (!$supportedMimeTypes) {
            return $acceptTypes;
        }
        $supportedMimeTypes = array_map('strtolower', (array) $supportedMimeTypes);
        // let’s check our supported types:
        foreach ($acceptTypes as $mime => $q) {
            if ($q && in_array(trim($mime), $supportedMimeTypes)) {
                return trim($mime);
            }
        }
        throw new common_exception_NotAcceptable();
        return null;
    }

    /**
     * Sends file content to the client(browser or video/audio player in the browser), it serves images, video/audio files and any other type of file.<br />
     * If the client asks for partial contents, then partial contents are served, if not, the whole file is send.<br />
     * Works well with big files, without eating up memory.
     * @author "Martin for OAT <code@taotesting.com>"
     * @param string the file name
     * @return mixed the file data
     */
    public static function returnFile($filename)
    {
        if (tao_helpers_File::securityCheck($filename, true)) {
            if (file_exists($filename)) {
                $mimeType = tao_helpers_File::getMimeType($filename, true);
                header('Content-Type: ' . $mimeType);
                $fp = fopen($filename, 'rb');
                if ($fp === false) {
                    header("HTTP/1.0 404 Not Found");
                } else {

                    // session must be closed because, for example, video files might take a while to be sent to the client
                    //  and we need the client to be able to make other calls to the server during that time
                    session_write_close();
                    
                    $http416RequestRangeNotSatisfiable = 'HTTP/1.1 416 Requested Range Not Satisfiable';
                    $http206PartialContent = 'HTTP/1.1 206 Partial Content';
                    $http200OK = 'HTTP/1.1 200 OK';
                    $filesize = filesize($filename);
                    $offset = 0;
                    $length = $filesize;
                    $useFpassthru = false;
                    $partialContent = false;
                    header('Accept-Ranges: bytes');
                    
                    if (isset($_SERVER['HTTP_RANGE'])) {
                        $partialContent = true;
                        preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
                        $offset = intval($matches[1]);
                        if (!isset($matches[2])) {
                            // no end position is given, so we serve the file from the start position to the end
                            $useFpassthru = true;
                        } else {
                            $length = intval($matches[2]) - $offset;
                        }
                    }
                    
                    fseek($fp, $offset);
                    
                    if ($partialContent) {
                        if (($offset < 0) || ($offset > $filesize)) {
                            header($http416RequestRangeNotSatisfiable);
                        } else {
                            if ($useFpassthru) {
                                // only a starting position is given
                                header($http206PartialContent);
                                header("Content-Length: " . ($filesize - $offset));
                                header('Content-Range: bytes ' . $offset . '-' . ($filesize - 1) . '/' . $filesize);
                                if (ob_get_level() > 0) ob_end_flush();
                                fpassthru($fp);
                            } else {
                                // we are given a starting position and how many bytes the client asks for
                                $endPosition = $offset + $length;
                                if ($endPosition > $filesize) {
                                    header($http416RequestRangeNotSatisfiable);
                                } else {
                                    header($http206PartialContent);
                                    header("Content-Length: " . ($length));
                                    header('Content-Range: bytes ' . $offset . '-' . ($offset + $length - 1) . '/' . $filesize);
                                    // send 500KB per cycle
                                    $bytesPerCycle = (1024 * 1024) * 0.5;
                                    $currentPosition = $offset;
                                    if (ob_get_level() > 0) ob_end_flush();
                                    // because the client might ask for the whole file, we split the serving into little pieces
                                    // this is also good in case someone with bad intentions tries to get the whole file many times
                                    //  and eat up the server memory, we are not loading the whole file into the memory.
                                    while (!feof($fp)) {
                                        if (($currentPosition + $bytesPerCycle) <= $endPosition) {
                                            $data = fread($fp, $bytesPerCycle);
                                            $currentPosition = $currentPosition + $bytesPerCycle;
                                            echo $data;
                                        } else {
                                            $data = fread($fp, ($endPosition - $currentPosition));
                                            echo $data;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        // client does not want partial contents so we just serve the whole file
                        header($http200OK);
                        header("Content-Length: " . $filesize);
                        if (ob_get_level() > 0) ob_end_flush();
                        fpassthru($fp);
                    }
                    fclose($fp);
                }
            } else {
                common_Logger::w('File '.$filename.' not found');
                header("HTTP/1.0 404 Not Found");
            }
        } else {
            throw new common_exception_Error('Security exception for path ' . $filename);
        }
    }

}

?>

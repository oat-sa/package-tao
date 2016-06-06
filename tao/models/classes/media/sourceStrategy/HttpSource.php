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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\tao\model\media\sourceStrategy;

use common_Logger;
use GuzzleHttp\Client;
use helpers_TimeOutHelper;
use oat\tao\model\media\MediaBrowser;

/**
 * This media source gives access to files not part of the Tao platform
 *
 * It is not intended to be used to browse for
 */
class HttpSource implements MediaBrowser
{
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getFileInfo()
     */
    public function getFileInfo($link)
    {
        throw new \common_Exception(__FUNCTION__ . ' not implemented');
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::download()
     */
    public function download($link)
    {
        $url = str_replace('\/', '/', $link);
        $fileName = \tao_helpers_File::createTempDir() . basename($link);

        common_Logger::d('Downloading ' . $url);
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::NO_TIMEOUT);
        $fp = fopen($fileName, 'w+');
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_FILE, $fp);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, 50);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);

        //if there is an http auth on the local domain, it's mandatory to auth with curl
        if (USE_HTTP_AUTH) {

            $addAuth = false;
            $domains = array('localhost', '127.0.0.1', ROOT_URL);

            foreach ($domains as $domain) {
                if (preg_match("/" . preg_quote($domain, '/') . "/", $url)) {
                    $addAuth = true;
                }
            }

            if ($addAuth) {
                curl_setopt($curlHandler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curlHandler, CURLOPT_USERPWD, USE_HTTP_USER . ":" . USE_HTTP_PASS);
            }
        }

        curl_exec($curlHandler);
        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $success = $httpCode == 200;
        curl_close($curlHandler);
        fclose($fp);
        helpers_TimeOutHelper::reset();

        return $fileName;
    }

    public function getBaseName($link)
    {
        $url = str_replace('\/', '/', $link);

        // by URL Basename
        $path = parse_url($url, PHP_URL_PATH);
        $realfilename = basename($path);

        $content = @get_headers($url, 1);

        if($content === false){
            throw new \tao_models_classes_FileNotFoundException($url);
        }

        $content = array_change_key_case($content, CASE_LOWER);
        // by header
        if (isset($content['content-disposition'])) {
            $tmp_name = explode('=', $content['content-disposition']);
            if ($tmp_name[1]) {
                $realfilename = trim($tmp_name[1], '";\'');
            }
        }

        return $realfilename;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getDirectory()
     */
    public function getDirectory($parentLink = '/', $acceptableMime = array(), $depth = 1)
    {
        throw new \common_Exception('Unable to browse the internet');
    }


    public function getFileStream($link)
    {
        $url = str_replace('\/', '/', $link);
        common_Logger::d('Getting Stream ' . $url);

        $response = $this->getRequest($url);
        $stream = $response->getBody();
        return $stream;
    }

    /**
     * @param $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function getRequest($url)
    {
        $client = new Client();
        return $client->get($url);
    }
}

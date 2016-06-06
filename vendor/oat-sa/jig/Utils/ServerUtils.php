<?php

/**
 * This file is part of the Jig package.
 *
 * Copyright (c) 04-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Utils;

use Exception;

/**
 * ServerUtils
 */
class ServerUtils
{

    /**
     * Determine whether a request has been made with AJAX
     *
     * @return bool
     */
    public static function requestIsXhr()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(
            $_SERVER['HTTP_X_REQUESTED_WITH']
        ) === 'xmlhttprequest';
    }

    /**
     * Make an educated guess on the URI that called the current script
     *
     * @param bool $qualified
     * @return string
     */
    public static function getScriptUri($qualified = false)
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return $qualified ? self::getBaseUri() . '/' . ltrim(
                    $_SERVER['REQUEST_URI'],
                    '/'
                ) : $_SERVER['REQUEST_URI'];
        }
        if (!empty($_SERVER['REDIRECT_URL'])) {
            return $qualified ? self::getBaseUri() . '/' . ltrim(
                    $_SERVER['REDIRECT_URL'],
                    '/'
                ) : $_SERVER['REDIRECT_URL'];
        }
        $queryString = '';
        if (!empty($_SERVER['QUERY_STRING'])) {
            $queryString = $_SERVER['QUERY_STRING'];
        } else if (!empty($_SERVER['REDIRECT_QUERY_STRING'])) {
            $queryString = $_SERVER['REDIRECT_QUERY_STRING'];
        }
        $queryString = '?' . htmlspecialchars($queryString);
        $phpSelf     = '';
        if (!empty($_SERVER['PHP_SELF'])) {
            $phpSelf = $_SERVER['PHP_SELF'];
        } else if (!empty($_SERVER['SCRIPT_NAME'])) {
            $phpSelf = $_SERVER['SCRIPT_NAME'];
        }
        if ($phpSelf) {
            return $qualified ? self::getBaseUri() . '/' . ltrim(
                    $phpSelf . $queryString,
                    '/'
                ) : $phpSelf . $queryString;
        }
        return '';
    }

    /**
     * Get base uri (protocol + domain)
     *
     * @return string
     */
    public static function getBaseUri()
    {
        $server = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        return rtrim(self::getProtocol() . '://' . $server, '/');
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public static function getProtocol()
    {
        return 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "");
    }

    /**
     * Redirect to a different resource
     *
     * @param string $path to the resource without protocol and without host
     * @return bool|void
     */
    public static function redirect($path = '')
    {

        $redirection = self::getProtocol() . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
        if (self::getScriptUri(true) === $redirection) {
            return false;
        }

        if ($redirection !== self::getScriptUri(true)) {
            header('Location: ' . $redirection, true, 301);
            exit(-1);
        }
        return false;
    }

    /**
     * Force a file to download
     *
     * @param $path
     * @param null $dwlFilename
     * @throws Exception
     */
    public static function forceDownload($path, $dwlFilename = null)
    {
        if (!is_file($path)) {
            throw new Exception(__METHOD__ . '() File ' . $path . ' doesn\'t exist!');
        }

        if (!$dwlFilename) {
            $dwlFilename = basename($path);
        }

        $content = file_get_contents($path);

        // initialize download
        header('Expires: Fri, 16 Sep 1983 00:00:01 GMT');
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Cache-control: private');
        header('Content-type: application/octet-stream');
        header('Content-Length: ' . strlen($content));
        header('Content-Disposition: ' . $dwlFilename);
        print($content);
        exit(-1);
    }

    public static function clientIpIs($ip)
    {
        return !empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === $ip;
    }

    public static function protocolizeUri($uri)
    {
        $uri = ltrim($uri, '/');
        if (strpos($uri, '://') === false) {
            $uri = self::getServerProtocol() . '://' . $uri;
        }
        return $uri;
    }

}

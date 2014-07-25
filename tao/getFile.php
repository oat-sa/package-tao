<?php
/**
 * 
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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
$url = $_SERVER['REQUEST_URI'];
$configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'configGetFile.php';
$ttl = 240;

$rel = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '/getFile.php/') + strlen('/getFile.php/'));
$parts = explode('/', $rel, 3);
if (count($parts) < 3 || ! file_exists($configPath)) {
    header('HTTP/1.0 403 Forbidden');
    die();
}
$config = include $configPath;
$compiledPath = $config['folder'];
$secretPassphrase = $config['secret'];

list ($timestamp, $token, $subPath) = $parts;
$parts = explode('*/', $subPath, 2);
// TODO add security check on url
if (count($parts) < 2) {
    header('HTTP/1.0 403 Forbidden');
    die();
}
list ($subPath, $file) = $parts;
$correctToken = md5($timestamp . $subPath . $secretPassphrase);

if (time() - $timestamp > $ttl || $token != $correctToken) {
    echo 'x';
    header('HTTP/1.0 403 Forbidden');
    die();
}

$file = str_replace('/', DIRECTORY_SEPARATOR, $file);
$filename = $compiledPath . $subPath . $file;
if (strpos($filename, '?')) {
    // A query string is provided with the file to be retrieved - clean up!
    $parts = explode('?', $filename);
    $filename = $parts[0];
}
require_once '../generis/helpers/class.File.php';
require_once '../tao/helpers/class.File.php';
$mimeType = tao_helpers_File::getMimeType($filename, true);
if (tao_helpers_File::securityCheck($filename, true)) {
    header('Content-Type: ' . $mimeType);
    $fp = fopen($filename, 'rb');
    fpassthru($fp);
}
exit();
    
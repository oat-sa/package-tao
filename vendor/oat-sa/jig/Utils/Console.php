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

class Console
{

    private static $instance;
    /**
     * Log into a file, the HTML source, as a block in the HTML, into the browser console or as plain text
     *
     * @var array
     */
    private $modes = array('file', 'src', 'html', 'console', 'plain');
    private $mode = 'html';
    private $logfile = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $class          = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    /**
     * Set the mode to any of the $modes, note that 'file' needs the file path as argument
     *
     * @param $mode
     * @param string $logfile
     * @return mixed
     * @throws Exception
     */
    public static function setMode($mode, $logfile = '')
    {
        $selfObj          = self::getInstance();
        $mode             = in_array($mode, $selfObj->modes) ? $mode : 'log';
        $selfObj->logfile = $logfile;
        if ($mode === 'file' && !$selfObj->logfile) {
            throw new Exception(__METHOD__ . '(): If you set the mode to \'file\' you need to specify the log file as second argument');
        }
        $selfObj->mode = $mode;
        return $selfObj;
    }


    /**
     * Dump message
     */
    public static function log()
    {
        $selfObj = self::getInstance();
        if (!in_array($selfObj->mode, $selfObj->modes)) {
            $selfObj->mode = 'html';
        }
        switch ($selfObj->mode) {
            case 'file':
                $log_dir = dirname($selfObj->logfile);
                if (!is_dir($log_dir) && !($result = @mkdir($log_dir, 0777, true))) {
                    print __METHOD__ . '(): I tried really hard to create ' . $log_dir . ' but I failed. My sincere apologies.';
                }
                $old = is_file($selfObj->logfile) ? file_get_contents($selfObj->logfile) : '';
                file_put_contents($selfObj->logfile, self::getMessage() . "\n" . $old);
                break;
            case 'console':
                print '<script>console.log(' . json_encode(self::getMessage()) . ')</script>';
                break;
            default:
                print self::getMessage();
                break;
        }
    }


    /**
     * Clear log file
     *
     * @return mixed
     */
    public static function clearLog()
    {
        $selfObj = self::getInstance();
        if (is_file($selfObj->logfile)) {
            unlink($selfObj->logfile);
        }
        return $selfObj;
    }

    /**
     * Process and format the message
     *
     * @return string
     */
    protected static function getMessage()
    {
        $selfObj = self::getInstance();
        $format  = 'html';
        $format_arr = array();

        if (!isset($_SERVER['HTTP_HOST']) || $selfObj->mode !== 'html') {
            $format = $selfObj->mode;
        } else {
            $response_arr = @headers_list();
            if ($response_arr) {
                foreach ($response_arr as $header) {
                    if (strpos($header, 'text/plain') !== false) {
                        $format = 'plain';
                    }
                }
            }
        }

        $backtrace = debug_backtrace();
        $msg_head  = $format === 'file' ? date('Y-m-d H:i:s') . ', ' : '';
        $msg_head .= 'File ' . str_replace(DIRECTORY_SEPARATOR, '/', $backtrace[1]['file']) . ', '
            . 'Line ' . $backtrace[1]['line'];


        switch ($format) {
            case 'console':
                return $backtrace[1]['args'];
                break;
            case 'src':
            case 'plain':
            case 'file':
                $format_arr = array(
                    'open'      => ($format === 'src' ? "<!--\n" : ''),
                    'close'     => ($format === 'src' ? "\n-->" : ''),
                    'break'     => "\n",
                    'sep'       => str_repeat('-', strlen($msg_head)),
                    'highlight' => false
                );
                break;

            case 'html':
                $msg_head   = 'File <span style="color:#669">' . str_replace(
                        DIRECTORY_SEPARATOR,
                        '/',
                        $backtrace[1]['file']
                    ) . '</span>, '
                    . 'Line <span style="color:#966">' . $backtrace[1]['line'] . '</span>';
                $format_arr = array(
                    'open'      => '<pre style="white-space: pre-wrap; font: 16px \'courier new\'; border:10px solid #ccc;background:#eee;padding: 15px;position:relative"><button type=button onclick="this.parentNode.style.display=\'none\'" style="position:absolute; right:5px; top:5px; cursor:pointer" title="Close console">x</button>',
                    'close'     => '</pre>',
                    'break'     => '',
                    'sep'       => '<hr style=\'border: 0;color: #ccc;background-color: #99c;height: 1px;width: 100%;\' />',
                    'highlight' => true
                );
                break;

        }


        $msg = $format_arr['break']
            . $format_arr['open']
            . $msg_head
            . $format_arr['break'];

        foreach ($backtrace[1]['args'] as $arg) {
            $msg .= $format_arr['sep']
                . $format_arr['break']
                . self::dumpAsString($arg, 15, $format_arr['highlight'])
                . $format_arr['break'];
        }

        $msg .= $format_arr['close'];
        return $msg;
    }


    /**
     * Bring all slashes in the same form. I'm wondering sometimes if this was such a great idea after all.
     *
     * @param $path
     * @param string $slash
     * @return mixed
     */
    protected static function normalizeSlashes($path, $slash = '/')
    {
        return str_replace(array('\\\\', '\\', '\\/', '/'), $slash, $path);
    }



// taken from the yii framework
    /**
     * CVarDumper class file.
     *
     * @author Qiang Xue <qiang.xue@gmail.com>
     * @link http://www.yiiframework.com/
     * @copyright Copyright &copy; 2008-2011 Yii Software LLC
     * @license http://www.yiiframework.com/license/
     */

    /**
     * CVarDumper is intended to replace the buggy PHP function var_dump and print_r.
     * It can correctly identify the recursively referenced objects in a complex
     * object structure. It also has a recursive depth control to avoid indefinite
     * recursive display of some peculiar variables.
     *
     * CVarDumper can be used as follows,
     * <pre>
     * CVarDumper::dump($var);
     * </pre>
     *
     * @author Qiang Xue <qiang.xue@gmail.com>
     * @version $Id: CVarDumper.php 2799 2011-01-01 19:31:13Z qiang.xue $
     * @package system.utils
     * @since 1.0
     */
    private static $_objects;
    private static $_output;
    private static $_depth;

    /**
     * Displays a variable.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as Yii controllers.
     *
     * @param mixed $var variable to be dumped
     * @param integer $depth maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param boolean $highlight whether the result should be syntax-highlighted
     */
    public static function dump($var, $depth = 10, $highlight = false)
    {
        echo self::dumpAsString($var, $depth, $highlight);
    }

    /**
     * Dumps a variable in terms of a string.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as Yii controllers.
     *
     * @param mixed $var variable to be dumped
     * @param integer $depth maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param boolean $highlight whether the result should be syntax-highlighted
     * @return string the string representation of the variable
     */
    public static function dumpAsString($var, $depth = 10, $highlight = false)
    {
        self::$_output  = '';
        self::$_objects = array();
        self::$_depth   = $depth;
        self::dumpInternal($var, 0);
        if ($highlight) {
            $result        = highlight_string(
                "<?php\n" . stripslashes(trim(self::normalizeSlashes(self::$_output), "' \n")),
                true
            );
            self::$_output = preg_replace('/&lt;\\?php<br \\/>/', '', $result, 1);
        }
        return stripslashes(trim(str_replace(array('(,'), array('('), self::normalizeSlashes(self::$_output)), "'"));
    }

    /*
     * @param mixed $var variable to be dumped
     * @param integer $level depth level
     */

    private static function dumpInternal($var, $level)
    {
        switch (gettype($var)) {
            case 'boolean':
                self::$_output .= $var ? 'true' : 'false';
                break;
            case 'integer':
                self::$_output .= "$var";
                break;
            case 'double':
                self::$_output .= "$var";
                break;
            case 'string':
                self::$_output .= "'" . $var . "'";
                break;
            case 'resource':
                self::$_output .= '{resource}';
                break;
            case 'NULL':
                self::$_output .= "null";
                break;
            case 'unknown type':
                self::$_output .= '{unknown}';
                break;
            case 'array':
                if (self::$_depth <= $level) {
                    self::$_output .= 'array(...)';
                } else if (empty($var)) {
                    self::$_output .= 'array()';
                } else {
                    $keys   = array_keys($var);
                    $spaces = str_repeat(' ', $level * 4);
                    self::$_output .= "array\n" . $spaces . '(';
                    foreach ($keys as $key) {
                        $key2 = str_replace("'", "\\'", $key);
                        self::$_output .= ",\n" . $spaces . "    '$key2' => ";
                        self::$_output .= self::dumpInternal($var[$key], $level + 1);
                    }
                    self::$_output .= ",\n" . $spaces . ')';
                }
                break;
            case 'object':
                if (($id = array_search($var, self::$_objects, true)) !== false) {
                    self::$_output .= get_class($var) . '#' . ($id + 1) . '(...)';
                } else if (self::$_depth <= $level) {
                    self::$_output .= get_class($var) . '(...)';
                } else {
                    $id        = array_push(self::$_objects, $var);
                    $className = get_class($var);
                    $members   = (array)$var;
                    $spaces    = str_repeat(' ', $level * 4);
                    self::$_output .= "$className#$id\n" . $spaces . '(';
                    foreach ($members as $key => $value) {
                        $keyDisplay = strtr(trim($key), array("\0" => ':'));
                        self::$_output .= "\n" . $spaces . "    [$keyDisplay] => ";
                        self::$_output .= self::dumpInternal($value, $level + 1);
                    }
                    self::$_output .= ",\n" . $spaces . ')';
                }
                break;
        }
    }

}

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

/**
 * Class Timer helps to figure out how much time a certain task within a script is taking up
 *
 * @package Jig\Utils
 */
class Timer
{

    protected static $instance = null;
    protected static $timerArr = array();
    protected static $timerStart = null;
    protected static $memUsage = 0;
    protected static $counter = 0;

    /**
     * creates and returns the global instance of the settings class
     *
     * @return object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $class            = __CLASS__;
            self::$instance   = new $class;
            self::$timerStart = microtime(1);
            self::$timerArr   = array();
            self::$memUsage   = memory_get_usage(true);
            register_shutdown_function(array($class, 'write'));
        }
        return self::$instance;
    }

    /**
     * Add a new point to the timer
     *
     * @param string $message
     */
    public static function add($message = '')
    {
        $instance = self::getInstance();
        $instance::$counter++;
        $info                  = debug_backtrace();
        $projectDir            = str_replace(DIRECTORY_SEPARATOR, '/', dirname(dirname(dirname(__DIR__))));
        $file                  = str_replace(DIRECTORY_SEPARATOR, '/', $info[0]['file']);
        $instance::$timerArr[] = array(
            'file'    => str_replace($projectDir, '', $file),
            'counter' => $instance::$counter,
            'line'    => $info[0]['line'],
            'time'    => microtime(1),
            'memory'  => memory_get_usage(true),
            'message' => $message
        );
    }


    public static function get($message = '')
    {
        $instance = self::getInstance();
        $instance::add($message);
        $id = uniqid();

        $table = '<div style="position:relative" id="' . $id . '-div">' . "\n"
            . '<button type=button onclick="this.parentNode.style.display=\'none\'" style="position:absolute; right:15px; top:15px; cursor:pointer;color:black" title="Close timer">x</button>' . "\n"
            . '    <table id="' . $id . '-tbl" style="border-collapse:collapse;border:10px solid #ccc;color:#333;background:#eee;margin:0;font:16px monospace !important">' . "\n"
            . '  <tr>' . "\n"
            . '    <th style="border:1px solid #ccc;background:#eee;padding:10px;font-weight:normal;color:#336">#</th>' . "\n"
            . '    <th style="border:1px solid #ccc;background:#eee;padding:10px;font-weight:normal;color:#336">File</th>' . "\n"
            . '    <th style="border:1px solid #ccc;background:#eee;padding:10px;font-weight:normal;color:#336">Line</th>' . "\n"
            . '    <th style="border:1px solid #ccc;background:#eee;padding:10px;font-weight:normal;color:#336">Seconds</th>' . "\n"
            . '    <th style="border:1px solid #ccc;background:#eee;padding:10px;font-weight:normal;color:#336">Sec. total</th>' . "\n"
            . '    <th style="border:1px solid #ccc;background:#eee;padding:10px;font-weight:normal;color:#336">Memory</th>' . "\n"
            . '    <th style="border:1px solid #ccc;background:#eee;padding:10px;font-weight:normal;color:#336">Message</th>' . "\n"
            . '  </tr>' . "\n";
        foreach ($instance::$timerArr as $cnt => $timer_data) {
            $time = $cnt > 0
                ? number_format($timer_data['time'] - $instance::$timerArr[$cnt - 1]['time'], 5)
                : number_format($timer_data['time'] - $instance::$timerStart, 5);

            $table .= '  <tr style="text-align:right">' . "\n"
                . '    <td style="white-space:nowrap;border:1px solid #ccc;background:#eee;padding:10px;color:#669">' . $timer_data['counter'] . '</td>' . "\n"
                . '    <td style="white-space:nowrap;border:1px solid #ccc;background:#eee;padding:10px;color:#669">' . $timer_data['file'] . '</td>' . "\n"
                . '    <td style="white-space:nowrap;border:1px solid #ccc;background:#eee;padding:10px;color:#966">' . $timer_data['line'] . '</td>' . "\n"
                . '    <td style="white-space:nowrap;border:1px solid #ccc;background:#eee;padding:10px;">' . self::formatTime(
                    $time
                ) . '</td>' . "\n"
                . '    <td style="white-space:nowrap;border:1px solid #ccc;background:#eee;padding:10px;">' . number_format(
                    $timer_data['time'] - $instance::$timerStart,
                    5
                ) . '</td>'
                . '    <td style="white-space:nowrap;border:1px solid #ccc;background:#eee;padding:10px;">' . self::formatMemoryUsage(
                    $timer_data['memory'] - $instance::$memUsage
                ) . '</td>'
                . '    <td style="white-space:nowrap;border:1px solid #ccc;background:#eee;padding:10px;">' . $timer_data['message'] . '</td>'
                . '  </tr>' . "\n";
        }
        $table .= '</table><script>document.getElementById("' . $id . '-div").style.width = document.defaultView.getComputedStyle(document.getElementById("' . $id . '-tbl"),"").getPropertyValue("width")</script></div>' . "\n";
        $instance::$timerArr = array();
        return $table;
    }


    /**
     * Time format for print
     *
     * @param $time
     * @return string
     */
    private static function formatTime($time)
    {
        return $time < 0.01
            ? $time
            : '<span style="color:#DD0000">' . $time . '</span>';
    }

    /**
     * Memory usage in pretty print
     *
     * @param $size
     * @return string
     */
    private static function formatMemoryUsage($size)
    {
        $units = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        $i     = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $i++;
        }
        return number_format($size, 2, '.', ',') . '&nbsp;' . $units[$i];
    }

    /**
     * Dump the timer result
     *
     * @param string $message
     */
    public static function write($message = '')
    {
        $instance = self::getInstance();
        print $instance::get($message);
    }
}
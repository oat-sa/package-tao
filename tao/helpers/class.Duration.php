<?php

/**
 * Helps you to manipulate durations.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 
 */
class tao_helpers_Duration {

    /**
     * Converts a time string to an ISO8601 duration
     * @param string $time as hh:mm:ss 
     * @return string the ISO duration
     */
    public static function timetoDuration($time){
        $duration = 'PT';
        if(preg_match ("/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/", $time)){
            $timeTokens = explode(':', $time);
            $duration .= intval($timeTokens[0]).'H'
                        .intval($timeTokens[1]).'M'
                        .intval($timeTokens[2]).'S';
        } else {
            $duration .= '0S';
        }
        return $duration;
    }

    /**
     * Converts  an interval to a time 
     * @param DateInterval $interval 
     * @return string time hh:mm:ss 
     */
    public static function intervalToTime(DateInterval $interval){
        $time = null;
        if(!is_null($interval)){
            $time = $interval->format('%H:%I:%S');
        } 
        return $time;
    }
    
    /**
     * Converts  a duration to a time 
     * @param string $duration the ISO duration
     * @return string time hh:mm:ss 
     */
    public static function durationToTime($duration){
        $time = null;
        try{
            $interval = new DateInterval($duration); 
            $time = self::intervalToTime($interval);
        } catch(Exception $e){
            common_Logger::e($e->getMessage());
        }
        return $time;
    }
}

?>

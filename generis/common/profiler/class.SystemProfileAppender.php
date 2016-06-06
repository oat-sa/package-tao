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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * 
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package generis
 
 */
class common_profiler_SystemProfileAppender extends common_profiler_Appender
{

    protected $comment = '';
    protected $data = array();
    protected $archivers = array();

    /**
     * Short description of method init
     *
     * @access public
     * @author Sam
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration){

        parent::init($configuration);
        $this->data = array();
        $this->archivers = array();

        if(isset($configuration['local_server_comment'])){
            $this->comment = strval($configuration['local_server_comment']);
        }

        foreach($configuration['archivers'] as $archiverConfig){
            if(isset($archiverConfig['class'])){
                $classname = $archiverConfig['class'];
                if(!class_exists($classname)){
                    $classname = 'common_profiler_archiver_'.$classname;
                }
                if(class_exists($classname)){
                    $archiver = new $classname();
                    try{
                        if($archiver instanceof common_profiler_archiver_Archiver && !is_null($archiver) && $archiver->init($archiverConfig)){
                            $this->archivers[] = $archiver;
                        }
                    }catch(InvalidArgumentException $e){
                        common_Logger::w('archiver configuration issue: '.$e->getMessage());
                    }
                }
            }
        }

        return true;
    }

    public function logContext(common_profiler_Context $context){
        $this->data['context'] = $context->toArray();
        if(!empty($this->comment)){
            $this->data['context']['comment'] = $this->comment;
        }
    }

    public function logTimer($flag, $duration, $total){
        if(!isset($this->data['timer'])){
            $this->data['timer'] = array();
        }
        if(!isset($this->data['timer'][$flag])){
            $this->data['timer'][$flag] = array();
        }
        if(!isset($this->data['timer']['TOTAL'])){
            $this->data['timer']['TOTAL'] = $total;
        }
        $this->data['timer'][$flag][] = $duration;
    }

    public function logMemoryPeak($memPeak, $memMax){
        if(!isset($this->data['mem'])){
            $this->data['mem'] = array();
        }
        $this->data['mem']['peak'] = $memPeak;
        $this->data['mem']['max'] = $memMax;
    }

    public function logQueriesCount($count){
        if(!isset($this->data['query'])){
            $this->data['query'] = array();
        }
        $this->data['query']['count'] = $count;
    }

    public function logQueriesSlow($slowQueries){
        if(!isset($this->data['query'])){
            $this->data['query'] = array();
        }

        $logs = array();
        foreach($slowQueries as $statementKey => $queries){
            $count = count($queries);
            $logs[$statementKey] = array();
            for($i = 0; $i < $count; $i++){
                $logs[$statementKey] = $queries[$i]->toArray();
            }
        }

        $this->data['query']['slow'] = $logs;
    }

    public function logQueriesSlowest($slowestQueries){
        if(!isset($this->data['query'])){
            $this->data['query'] = array();
        }
        $this->data['query']['slowest'] = array();
        foreach($slowestQueries as $query){
            $this->data['query']['slowest'][] = $query->toArray();
        }
    }

    public function logQueriesStat($queries){
        $count = $this->getConfigOption('queries', 'count');
        if(!is_null($count)){
            $this->data['query']['stat'] = $queries;
        }
    }

    public function flush(){
        foreach($this->archivers as $archiver){
            $archiver->store($this->data);
        }
    }

}

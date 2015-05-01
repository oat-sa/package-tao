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
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package 

 *
 */
class common_persistence_PhpFileDriver implements common_persistence_KvDriver, common_persistence_Purgable
{
    /**
     * List of characters permited in filename
     * @var array
     */
    private static $ALLOWED_CHARACTERS = array('A' => '','B' => '','C' => '','D' => '','E' => '','F' => '','G' => '','H' => '','I' => '','J' => '','K' => '','L' => '','M' => '','N' => '','O' => '','P' => '','Q' => '','R' => '','S' => '','T' => '','U' => '','V' => '','W' => '','X' => '','Y' => '','Z' => '','a' => '','b' => '','c' => '','d' => '','e' => '','f' => '','g' => '','h' => '','i' => '','j' => '','k' => '','l' => '','m' => '','n' => '','o' => '','p' => '','q' => '','r' => '','s' => '','t' => '','u' => '','v' => '','w' => '','x' => '','y' => '','z' => '',0 => '',1 => '',2 => '',3 => '',4 => '',5 => '',6 => '',7 => '',8 => '',9 => '','_' => '','-' => '');

    /**
     * absolute path of the directory to use
     * ending on a directory seperator
     * 
     * @var string
     */
    private $directory;
    
    /**
     * Nr of subfolder levels in order to prevent filesystem bottlenecks
     * Only used in non human readable mode
     * 
     * @var int
     */
    private $levels;
    
    /**
     * Whenever or not the filenames should be human readable
     * FALSE by default for performance issues with many keys
     * 
     * @var boolean
     */
    private $humanReadable;
    
    /**
     * Workaround to prevent opcaches from providing
     * deprecated values
     * 
     * @var array
     */
    private $cache = array();
    
    /**
     * Using 3 default levels, so the files get split up into
     * 16^3 = 4096 induvidual directories 
     * 
     * @var int
     */
    const DEFAULT_LEVELS = 3;
    
    const DEFAULT_MASK = 0700;

    /**
     * (non-PHPdoc)
     * @see common_persistence_Driver::connect()
     */
    function connect($id, array $params)
    {
        $this->directory = isset($params['dir']) 
            ? $params['dir'].($params['dir'][strlen($params['dir'])-1] == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR)
            : FILES_PATH.'generis'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;
        $this->levels = isset($params['levels']) ? $params['levels'] : self::DEFAULT_LEVELS;
        $this->humanReadable = isset($params['humanReadable']) ? $params['humanReadable'] : false;
        return new common_persistence_KeyValuePersistence($params, $this);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::set()
     */
    public function set($id, $value, $ttl = null)
    {
        if (!is_null($ttl)) {
            throw new common_exception_NotImplemented('TTL not implemented in '.__CLASS__);
        } else {
            $filePath = $this->getPath($id);
            $dirname = dirname($filePath);
            if (!file_exists($dirname)) {
                mkdir($dirname, self::DEFAULT_MASK, true);
            }
            
            $string = $this->getContent($id, $value);
            
            // we first open with 'c' in case the flock fails
            // 'w' would empty the file that someone else might be working on
            if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
            
                // We first need to truncate.
                ftruncate($fp, 0);
            
                $success = fwrite($fp, $string);
                @flock($fp, LOCK_UN);
                @fclose($fp);
                if ($success) {
                    // OPcache workaround
                    $this->cache[$id] = $value;
                    if (function_exists('opcache_invalidate')) {
                        opcache_invalidate($filePath, true);
                    }
                } else {
                    common_Logger::w('Could not write '.$filePath);
                }
                return $success !== false;
            } else {
                common_Logger::w('Could not obtain lock on '.$filePath);
                return false;
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::get()
     */
    public function get($id) {
        if (isset($this->cache[$id])) {
            // OPcache workaround
            return $this->cache[$id];
        }
        $value = @include $this->getPath($id);
        return $value;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::exists()
     */
    public function exists($id) {
        return file_exists($this->getPath($id));
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::del()
     */
    public function del($id) {
        if (isset($this->cache[$id])) {
            // OPcache workaround
            unset($this->cache[$id]);
        }
        $filePath = $this->getPath($id);
        
        // invalidate opcache first, fails on already deleted file
        if  (function_exists('opcache_invalidate')) {
            opcache_invalidate($filePath, true);
        }
        $success = @unlink($filePath);
        
        return $success;
    }

    /**
     * purge the persistence directory
     * 
     * @return boolean
     */
    public function purge() {
        // @todo opcache invalidation
        return file_exists($this->directory)
            ? helpers_File::emptyDirectory($this->directory)
            : false;
    }

    /**
     * Map the provided key to a relativ path
     * 
     * @param string $key
     * @return string
     */
    protected function getPath($key) {
        if ($this->humanReadable) {
            $path = '';
            foreach (str_split($key) as $char) {
                $path .= isset(self::$ALLOWED_CHARACTERS[$char]) ? $char : base64_encode($char);
            }
        } else {
            $encoded = md5($key);
            $path = implode(DIRECTORY_SEPARATOR,str_split(substr($encoded, 0, $this->levels))).DIRECTORY_SEPARATOR.substr($encoded, $this->levels);
        }
        return  $this->directory.$path.'.php';
    }
    
    /**
     * Generate the php code that returns the provided value
     * 
     * @param string $key
     * @param mixed $value
     * @return string
     */
    protected function getContent($key, $value) {
        return $this->humanReadable
            ? "<?php return ".common_Utils::toHumanReadablePhpString($value).";".PHP_EOL
            : "<?php return ".common_Utils::toPHPVariableString($value).";";
    }

}

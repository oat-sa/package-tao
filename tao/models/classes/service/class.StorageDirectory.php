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
 * 
 */

use oat\oatbox\filesystem\FileSystemService;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamInterface;

/**
 * Represents  direxctory for file storage 
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class tao_models_classes_service_StorageDirectory implements ServiceLocatorAwareInterface, IteratorAggregate
{
    use ServiceLocatorAwareTrait;
    
    private $id;
    
    /**
     * 
     * @var core_kernel_fileSystem_FileSystem
     */
    private $fs;
    private $relPath;
    private $accessProvider;
    
    public function __construct($id, $fs, $path, $provider) {
        $this->id = $id;
        $this->fs = $fs;
        $this->relPath = $path;
        $this->accessProvider = $provider;
    }
    
    /**
     * Returned the absolute path to this directory
     * Please use read and write to access files
     * 
     * @return string
     * @deprecated
     */
    public function getPath() {
        return $this->fs->getPath().$this->relPath;
    }

    /**
     * Returns the relative path of this directory
     * @return string
     */
    public function getRelativePath() {
        return $this->relPath;
    }
    
    /**
     * Returns the identifier of this directory
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Returns whenever or not this directory is public
     * @return boolean
     */
    public function isPublic() {
        return !is_null($this->accessProvider);
    }
    
    /**
     * Returns a URL that allows you to access the files in a directory
     * preserving the relative paths
     * 
     * @return string
     * @throws common_Exception
     */
    public function getPublicAccessUrl() {
        if (is_null($this->accessProvider)) {
            common_Logger::e('accessss');
            throw new common_Exception('Tried obtaining access to private directory with ID '.$this->id);
        }
        return $this->accessProvider->getAccessUrl($this->relPath);
    }
    
    /**
     * Return content of file located at $path. Output as string
     *
     * @param string $path
     * @return StreamInterface
     */
    public function read($path)
    {
        return  $this->getFileSystem()->readStream($this->getRelativePath().$path);
    }

    /**
     * Return content of file located at $path. Output as stream
     * @param $path
     * @return \Slim\Http\Stream
     */
    public function readStream($path)
    {
        $resource =  $this->read($path);
        return new \GuzzleHttp\Psr7\Stream($resource);
    }

    /**
     * Store a file in the directory from resource
     *
     * @param string $path
     * @param mixed $resource
     * @return boolean
     */
    public function write($path, $resource)
    {
        common_Logger::d('Writting in ' . $this->getRelativePath().$path);
        return $this->getFileSystem()->writeStream($this->getRelativePath().$path, $resource);
    }

    /**
     * Store a file in the directory from stream
     *
     * @param $path
     * @param StreamInterface $stream
     * @return bool
     * @throws common_Exception
     */
    public function writeStream($path, StreamInterface $stream)
    {
        if (!$stream->isReadable()) {
            throw new common_Exception('Stream is not readable. Write to filesystem aborted.');
        }
        if (!$stream->isSeekable()) {
            throw new common_Exception('Stream is not seekable. Write to filesystem aborted.');
        }
        $stream->rewind();

        $resource = GuzzleHttp\Psr7\StreamWrapper::getResource($stream);
        if (!is_resource($resource)) {
            throw new common_Exception('Unable to create resource from the given stream. Write to filesystem aborted.');
        }

        return $this->write($path, $resource);
    }

    /**
     * Check if file exists
     *
     * @param $path
     * @return bool
     */
    public function has($path)
    {
        return $this->getFileSystem()->has($this->getRelativePath().$path);
    }

    /**
     * Delete file
     *
     * @param $path
     * @return bool
     * @throws FileNotFoundException
     */
    public function delete($path)
    {
        try {
            return $this->getFileSystem()->delete($this->getRelativePath() . $path);
        } catch (\League\Flysystem\FileNotFoundException $e) {
            common_Logger::e($e->getMessage());
            throw new tao_models_classes_FileNotFoundException($path);
        }
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        $files = array();
        $content = $this->getFileSystem()->listContents($this->getRelativePath(), true);
        foreach($content as $file){
            if($file['type'] === 'file'){
                $files[] = str_replace($this->getRelativePath(), '', $file['path']);
            }
        }
        return new ArrayIterator($files);
    }

    /**
     * @return Filesystem
     */
    protected function getFileSystem() {
        return $this->getServiceLocator()->get(FileSystemService::SERVICE_ID)->getFileSystem($this->fs->getUri());
    }
}
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
 *
 */

namespace oat\taoQtiItem\model\flyExporter\simpleExporter;

use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ConfigurableService;

/**
 *
 * Class ItemExporter
 * @package oat\taoQtiItem\model\flyExporter\simpleExporter
 */
class ItemExporter extends ConfigurableService implements SimpleExporter
{
    /**
     * File system
     */
    const EXPORT_FILESYSTEM = 'taoQtiItem';

    /**
     * Default csv delimiter
     */
    const CSV_DELIMITER = ',';

    /**
     * Default property delimiter
     */
    const DEFAULT_PROPERTY_DELIMITER = '|';

    /**
     * Header of flyfile
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Columns requested by export
     * @var array
     */
    protected $columns = [];

    /**
     * Available extractors
     * @var array
     */
    protected $extractors = [];

    /**
     * Flysytem to manage file storage
     * @var
     */
    protected $filesystem;

    /**
     * file location inside filesystem
     * @var
     */
    protected $filelocation;


    /**
     * @inheritdoc
     *
     * @param null $uri
     * @return mixed
     * @throws ExtractorException
     */
    public function export($uri=null)
    {
        $this->loadConfig();
        $items = $this->getItems($uri);
        $data  = $this->extractDataFromItems($items);
        $this->save($data);

        return $this->filelocation;
    }

    /**
     * Load config & check if mandatory settings exist
     *
     * @return $this
     * @throws ExtractorException
     * @throws \common_Exception
     */
    protected function loadConfig()
    {
        $this->filelocation = $this->getOption('fileLocation');
        if (!$this->filelocation) {
            throw new ExtractorException('File location config is not correctly set.');
        }

        $serviceManager = $this->getServiceManager();
        $fsService = $serviceManager->get(FileSystemService::SERVICE_ID);
        $this->filesystem = $fsService->getFileSystem(self::EXPORT_FILESYSTEM);

        $this->extractors = $this->getOption('extractors');
        $this->columns = $this->getOption('columns');
        if (!$this->extractors || !$this->columns) {
            throw new ExtractorException('Data config is not correctly set.');
        }

        return $this;
    }

    /**
     * Get all items of given uri otherwise get default class
     *
     * @param $uri
     * @return array
     */
    protected function getItems($uri)
    {
        if (!empty($uri)) {
            $classUri = $uri;
        } else {
            $classUri = $this->getDefaultUriClass();
        }

        $class = new \core_kernel_classes_Class($classUri);
        return $class->getInstances(true);
    }

    /**
     * Get default class e.q. root class
     *
     * @return mixed
     */
    protected function getDefaultUriClass()
    {
        return TAO_ITEM_CLASS;
    }

    /**
     * Loop all items and call extract function
     *
     * @param array $items
     * @return array
     * @throws ExtractorException
     */
    protected function extractDataFromItems(array $items)
    {
        $output = [];
        foreach ($items as $item) {
            $output[] = $this->extractDataFromItem($item);
        }

        if (empty($output)) {
            throw new ExtractorException('No data to export.');
        }

        return $output;
    }

    /**
     * Loop foreach columns and extract data thought extractors
     *
     * @param \core_kernel_classes_Resource $item
     * @return array
     */
    protected function extractDataFromItem(\core_kernel_classes_Resource $item)
    {
        try {
            foreach ($this->columns as $column => $config) {
                $extractor = $this->extractors[$config['extractor']];
                if (isset($config['parameters'])) {
                    $parameters = $config['parameters'];
                } else {
                    $parameters = [];
                }
                $extractor->addColumn($column, $parameters);
            }

            $data = ['0' => []];
            foreach ($this->extractors as $extractor) {

                $extractor->setItem($item);
                $extractor->run();
                $values = $extractor->getData();

                foreach($values as $key => $value) {

                    if (count($value) > 1) {
                        $interactionData = $value;
                    } else {
                        $interactionData = $values;
                    }

                    if (array_values(array_intersect(array_keys($data[0]), array_keys($interactionData))) == array_keys($interactionData)) {
                        $line = array_intersect_key($data[0], array_flip($this->headers));
                        $data[] = array_merge($line, $interactionData);
                    } else {
                        $data[0] = array_merge($data[0], $interactionData);
                    }

                    $this->headers = array_unique(array_merge($this->headers, array_keys($interactionData)));
                }
            }

            return $data;

        } catch (ExtractorException $e) {
            \common_Logger::e('ERROR on item ' . $item->getUri() . ' : ' . $e->getMessage());
        }
    }

    /**
     * Save data to file
     *
     * @param array $data
     * @throws ExtractorException
     * @throws \Exception
     */
    protected function save(array $data)
    {
        $this->handleFile($this->filelocation);

        $output = $contents = [];

        $contents[] = implode(self::CSV_DELIMITER, $this->headers);

        foreach ($data as $item) {
            foreach ($item as $line) {
                foreach ($this->headers as $index => $value) {
                    if (isset($line[$value]) && $line[$value]!=='') {
                        $output[$value] = '"' . $line[$value] . '"';
                        unset($line[$value]);
                    } else {
                        $output[$value] = '';
                    }
                }
                $contents[] = implode(self::CSV_DELIMITER,  array_merge($output, $line));
            }
        }
        $this->filesystem->update($this->filelocation, implode("\n", $contents));
    }

    /**
     * Handle file, delete if already exist
     *
     * @param $filename
     * @throws ExtractorException
     * @throws \Exception
     */
    protected function handleFile($filename)
    {
        if (empty($filename)) {
            throw new ExtractorException('Filename is empty!');
        }

        if ($this->filesystem->has($filename)) {
            $this->filesystem->delete($filename);
        }

        if ($resource = fopen('temp', 'w')===false) {
            throw new \Exception('Unable to create csv file.');
        }

        $this->filesystem->write($filename, $resource);
        if (is_resource($resource)) {
            fclose($resource);
        }
    }
}
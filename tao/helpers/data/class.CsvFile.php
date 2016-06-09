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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_helpers_data_CsvFile
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_data_CsvFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    const FIELD_DELIMITER = 'field_delimiter';
    const FIELD_ENCLOSER = 'field_encloser';
    const MULTI_VALUES_DELIMITER = 'multi_values_delimiter';
    const FIRST_ROW_COLUMN_NAMES = 'first_row_column_names';

    /**
     * Contains the CSV data as a simple 2-dimensional array. Keys are integer
     * the mapping done separatyely if column names are provided.
     *
     * @access private
     * @var array
     */
    private $data = array();

    /**
     * Contains the mapping for column names if the CSV file contains a row
     * with column names.
     *
     * [0] ='id'
     * [1] = 'label'
     * ...
     *
     * If it has no name, empty string for this index.
     *
     * @access private
     * @var array
     */
    private $columnMapping = array();

    /**
     * Options such as string delimiter, new line escaping sequence, ...
     *
     * @access private
     * @var array
     */
    private $options = array();

    /**
     * The count of columns in the CsvFile. Will be updated at each row
     * The largest count will be taken into account.
     *
     * @access private
     * @var Integer
     */
    private $columnCount = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        
        $defaults = array('field_delimiter' => ';',
        				  'field_encloser' => '"',
                            // if empty - don't use multi_values
        				  'multi_values_delimiter' => '',
        				  'first_row_column_names' => true);
        
        $this->setOptions(array_merge($defaults, $options));
        $this->setColumnCount(0);
        
    }

    /**
     * Short description of method setData
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array data
     * @return void
     */
    protected function setData($data)
    {
        
        $this->data = $data;
        
    }

    /**
     * Short description of method getData
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getData()
    {
        $returnValue = array();

        
        $returnValue = $this->data;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method setColumnMapping
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array columnMapping
     * @return void
     */
    protected function setColumnMapping($columnMapping)
    {
        
        $this->columnMapping = $columnMapping;
        
    }

    /**
     * Short description of method getColumnMapping
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getColumnMapping()
    {
        $returnValue = array();

        
        $returnValue = $this->columnMapping;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method load
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string path
     * @return void
     */
    public function load($path)
    {
        
        if (!is_file($path)){
        	throw new InvalidArgumentException("Expected CSV file '${path}' could not be open.");
        }
        else if (!is_readable($path)){
        	throw new InvalidArgumentException("CSV file '${path}' is not readable.");	
        }
        else{
        	// Let's try to read this !
	        $fields = array();
	        $data = array();
	        
	        // More readable variables
	    	$WRAP  = preg_quote($this->options['field_encloser'], '/');
			$DELIM = $this->options['field_delimiter'];
			$MULTI = $this->options['multi_values_delimiter'];
			
			
			$adle = ini_get('auto_detect_line_endings');
			ini_set('auto_detect_line_endings',TRUE);
			$rows = file($path, FILE_IGNORE_NEW_LINES);
			ini_set('auto_detect_line_endings',$adle);
			
			if ($this->options['first_row_column_names']){
				
				$fields = array_map('rtrim', explode($DELIM, $rows[0]));
				foreach($fields as $i => $field){
					$fieldData = preg_replace("/^$WRAP/", '', $field);
					$fieldData = preg_replace("/$WRAP$/", '', $fieldData);
					$fields[$i] = $fieldData;
				}
				
				// We got the column mapping.
				$this->setColumnMapping($fields);
				unset($rows[0]); // Unset to avoid processing below.
			}
			
			$lineNumber = 0;
			foreach ($rows as  $row){
				if (trim($row) != ''){
					$data[$lineNumber] = array();
					
					$rowFields = array_map('rtrim', explode($DELIM, $row));
					for ($i = 0; $i < count($rowFields); $i++){
						$fieldData = preg_replace("/^$WRAP/", '', $rowFields[$i]);
						$fieldData = preg_replace("/$WRAP$/", '', $fieldData);
						// If there is nothing in the cell, replace by null for
						// abstraction consistency.
						if ($fieldData == ''){
							$fieldData = null;	
						} elseif(!empty($MULTI) && mb_strpos($fieldData, $MULTI) !== false) {
                            // try to split by multi_value_delimiter
                            $multiField = [];
                            foreach (explode($MULTI, $fieldData) as $item) {
                                if(!empty($item))
                                    $multiField[] = $item;
                            }
                            $fieldData = $multiField;
                        }
						$data[$lineNumber][$i] = $fieldData;
					}

					// Update the column count.
					$currentRowColumnCount = count($rowFields);
					if ($this->getColumnCount() < $currentRowColumnCount){
						$this->setColumnCount($currentRowColumnCount);
					}
					
					$lineNumber++;
				}
			}
			
			$this->setData($data);
        }
        
    }

    /**
     * Short description of method setOptions
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array array
     * @return void
     */
    public function setOptions($array = array())
    {
        
        $this->options = $array;
        
    }

    /**
     * Short description of method getOptions
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getOptions()
    {
        $returnValue = array();

        
        $returnValue = $this->options;
        

        return (array) $returnValue;
    }

    /**
     * Get a row at a given row $index.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int index The row index. First = 0.
     * @param  boolean associative Says that if the keys of the array must be the column names or not. If $associative is set to true but there are no column names in the CSV file, an IllegalArgumentException is thrown.
     * @return array
     */
    public function getRow($index, $associative = false)
    {
        $returnValue = array();

        
        $data = $this->getData();
        if (isset($data[$index])){
        	if ($associative == false) {
        		$returnValue = $data[$index];	
        	}
        	else{
        		$mapping = $this->getColumnMapping();
        	
        		if (!count($mapping)){
        			// Trying to access by column name but no mapping detected.
        			throw new InvalidArgumentException("Cannot access column mapping for this CSV file.");	
        		}
        		else{
        			$mappedRow = array();
        			for ($i = 0; $i < count($mapping); $i++){
        				$mappedRow[$mapping[$i]] = $data[$index][$i];
        			}
        			
        			$returnValue = $mappedRow;
        		}
        	}
        }
        else{
        	throw new InvalidArgumentException("No row at index ${index}.");	
        }
        

        return (array) $returnValue;
    }

    /**
     * Counts the number of rows in the CSV File.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function count()
    {
        $returnValue = (int) 0;

        
        $returnValue = count($this->getData());
        

        return (int) $returnValue;
    }

    /**
     * Get the value at the specified $row,$col.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int row Row index. If there is now row at $index, an IllegalArgumentException is thrown.
     * @param  int col
     * @return mixed
     */
    public function getValue($row, $col)
    {
        $returnValue = null;

        
        $data = $this->getData();
        if (isset($data[$row][$col])){
        	$returnValue = $data[$row][$col];	
        }
        else if (isset($data[$row]) && is_string($col)){
        	// try to access by col name.
        	$mapping = $this->getColumnMapping();
        	for ($i = 0; $i < count($mapping); $i++){
        		
        		if ($mapping[$i] == $col && isset($data[$row][$col])){
        			// Column with name $col extists.
        			$returnValue = $data[$row][$col];
        		}
        	}
        }
        else {
        	throw new InvalidArgumentException("No value at ${row},${col}.");	
        }
        

        return $returnValue;
    }

    /**
     * Sets a value at the specified $row,$col.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int row Row Index. If there is no such row, an IllegalArgumentException is thrown.
     * @param  int col
     * @param  int value The value to set at $row,$col.
     * @return void
     */
    public function setValue($row, $col, $value)
    {
        
        $data = $this->getData();
        if (isset($data[$row][$col])){
        	$this->data[$row][$col] = $value;	
        } else if (isset($data[$row]) && is_string($col)){
        	// try to access by col name.
        	$mapping = $this->getColumnMapping();
        	for ($i = 0; $i < count($mapping); $i++){
        		
        		if ($mapping[$i] == $col && isset($data[$row][$col])){
        			// Column with name $col extists.
        			$this->data[$row][$col] = $value;
        		}
        	}
        	
        	// Not found.
        	throw new InvalidArgumentException("Unknown column ${col}");
        }
        else{
        	throw new InvalidArgumentException("No value at ${row},${col}.");	
        }
        
    }

    /**
     * Gets the count of columns contained in the CsvFile.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function getColumnCount()
    {
        $returnValue = (int) 0;

        
        $returnValue = $this->columnCount;
        

        return (int) $returnValue;
    }

    /**
     * Sets the column count.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int count The column count.
     * @return void
     */
    protected function setColumnCount($count)
    {
        
        $this->columnCount = $count;
        
    }

}

?>
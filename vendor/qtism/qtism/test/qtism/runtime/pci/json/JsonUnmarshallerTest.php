<?php

use qtism\common\datatypes\files\FileSystemFileManager;
use qtism\common\datatypes\files\FileSystemFile;
use qtism\runtime\common\RecordContainer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\QtiDatatype;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\IntOrIdentifier;
use qtism\common\datatypes\Uri;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Boolean;
use qtism\runtime\pci\json\Unmarshaller;
use qtism\common\datatypes\Scalar;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class JsonUnmarshallerTest extends QtiSmTestCase {
	
    static protected function createUnmarshaller() {
        return new Unmarshaller(new FileSystemFileManager());
    }
    
    /**
     * @dataProvider unmarshallScalarProvider
     * 
     * @param Scalar $expectedScalar
     * @param string $json
     */
    public function testUnmarshallScalar(Scalar $expectedScalar = null, $json) {
        $unmarshaller = self::createUnmarshaller();
        if (is_null($expectedScalar) === false) {
            $this->assertTrue($unmarshaller->unmarshall($json)->equals($expectedScalar));
        }
        else {
            $this->assertSame($expectedScalar, $unmarshaller->unmarshall($json));
        }
    }
    
    /**
     * @dataProvider unmarshallComplexProvider
     * 
     * @param QtiDatatype $expectedComplex
     * @param string $json
     */
    public function testUnmarshallComplex(QtiDatatype $expectedComplex, $json) {
        $unmarshaller = self::createUnmarshaller();
        $value = $unmarshaller->unmarshall($json);
        $this->assertTrue($expectedComplex->equals($value));
    }
    
    /**
     * @dataProvider unmarshallFileProvider
     * 
     * @param File $expectedFile
     * @param string $json
     */
    public function testUnmarshallFile(FileSystemFile $expectedFile, $json) {
        $unmarshaller = self::createUnmarshaller();
        $value = $unmarshaller->unmarshall($json);
        $this->assertTrue($expectedFile->equals($value));
        
        // cleanup.
        $fileManager = new FileSystemFileManager();
        $fileManager->delete($value);
    }
    
    /**
     * @dataProvider unmarshallListProvider
     * 
     * @param MultipleContainer $expectedContainer
     * @param string $json
     */
    public function testUnmarshallList(MultipleContainer $expectedContainer, $json) {
        $unmarshaller = self::createUnmarshaller();
        $this->assertTrue($expectedContainer->equals($unmarshaller->unmarshall($json)));
    }
    
    /**
     * @dataProvider unmarshallRecordProvider
     * 
     * @param RecordContainer $expectedRecord
     * @param string $json
     */
    public function testUnmarshallRecord(RecordContainer $expectedRecord, $json) {
        $unmarshaller = self::createUnmarshaller();
        $this->assertTrue($expectedRecord->equals($unmarshaller->unmarshall($json)));
    }
    
    /**
     * @dataProvider unmarshallInvalidProvider
     * 
     * @param mixed $input
     */
    public function testUnmarshallInvalid($input) {
        $unmarshaller = self::createUnmarshaller();
        $this->setExpectedException('qtism\\runtime\\pci\\json\\UnmarshallingException');
        $unmarshaller->unmarshall($input);
    }
    
    public function testUnmarshallState() {
        $json = '
            {
                "RESPONSE1": { "base" : { "identifier" : "ChoiceA" } },
                "RESPONSE2": { "list" : { "identifier" : ["_id1", "id2", "ID3"] } },
                "RESPONSE3": { "record" : [ { "name" : "rock", "base": { "identifier" : "Paper" } } ] },
                "RESPONSE4": { "base" : null }
            }
        ';
        
        $unmarshaller = self::createUnmarshaller();;
        $state = $unmarshaller->unmarshall($json);
        $this->assertEquals(4, count($state));
        $this->assertEquals(array('RESPONSE1', 'RESPONSE2', 'RESPONSE3', 'RESPONSE4'), array_keys($state));
        
        $response1 = new Identifier('ChoiceA');
        $response2 = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('_id1'), new Identifier('id2'), new Identifier('ID3')));
        $response3 = new RecordContainer(array('rock' => new Identifier('Paper')));
        $response4 = null;

        $this->assertTrue($response1->equals($state['RESPONSE1']));
        $this->assertTrue($response2->equals($state['RESPONSE2']));
        $this->assertTrue($response3->equals($state['RESPONSE3']));
        $this->assertSame($response4, $state['RESPONSE4']);
    }
    
    public function unmarshallScalarProvider() {
        return array(
            array(new Boolean(true), '{ "base" : {"boolean" : true } }'),
            array(new Boolean(false), '{ "base" : {"boolean" : false } }'),
            array(new Integer(123), '{ "base" : {"integer" : 123 } }'),
            array(new Float(23.23), '{ "base" : {"float" : 23.23 } }'),
            array(new Float(6.0), '{ "base" : {"float" : 6 } }'),
            array(new String('string'), '{ "base" : {"string" : "string" } }'),
            array(new Uri('http://www.taotesting.com'), '{ "base" : {"uri" : "http://www.taotesting.com" } }'),
            array(new IntOrIdentifier(10), '{ "base" : {"intOrIdentifier" : 10 } }'),
            array(new IntOrIdentifier('_id1'), '{ "base" : {"identifier" : "_id1" } }'),
            array(new Identifier('_id1'), '{ "base" : {"identifier" : "_id1" } }'),
            array(null, '{ "base": null }')
        );
    }
    
    public function unmarshallComplexProvider() {
        $returnValue = array();
        
        $returnValue[] = array(new Point(10, 20), '{ "base" : { "point" : [10, 20] } }');
        $returnValue[] = array(new Pair('A', 'B'), '{ "base" : { "pair" : ["A", "B"] } }');
        $returnValue[] = array(new DirectedPair('a', 'b'), '{ "base" : { "directedPair" : ["a", "b"] } }');
        $returnValue[] = array(new Duration('PT3S'), '{ "base" : { "duration" : "PT3S" } }');

        return $returnValue;
    }
    
    public function unmarshallFileProvider() {
        $returnValue = array();
        $samples = self::samplesDir();
        $fileManager = new FileSystemFileManager();
        
        $file = $fileManager->retrieve($samples . 'datatypes/file/files_2.txt');
        $returnValue[] = array($file, '{ "base" : { "file" : { "mime" : "text\/html", "data" : ' . json_encode(base64_encode('<img src="/qtism/img.png"/>')) . ' } } }');
        
        $file = $fileManager->retrieve($samples . 'datatypes/file/text-plain_text_data.txt');
        $returnValue[] = array($file, '{ "base" : { "file" : { "mime" : "text\/plain", "data" : ' . json_encode(base64_encode('Some text...')) . ', "name" : "text.txt" } } }');
        
        $originalfile = $samples . 'datatypes/file/raw/image.png';
        $filepath = $samples . 'datatypes/file/image-png_noname_data.png';
        $file = $fileManager->retrieve($filepath);
        $returnValue[] = array($file, '{ "base" : { "file" : { "mime" : "image\/png", "data" : ' . json_encode(base64_encode(file_get_contents($originalfile))) . ' } } }');
        
        return $returnValue;
    }
    
    public function unmarshallListProvider() {
        $returnValue = array();
        
        $container = new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true), new Boolean(true)));
        $json = '{ "list" : { "boolean" : [true, false, true, true] } }';
        $returnValue[] = array($container, $json);
        
        $container = new MultipleContainer(BaseType::INTEGER, array(new Integer(2), new Integer(3), new Integer(5), new Integer(7), new Integer(11), new Integer(13)));
        $json = '{ "list" : { "integer" : [2, 3, 5, 7, 11, 13] } }';
        $returnValue[] = array($container, $json);
        
        $container = new MultipleContainer(BaseType::FLOAT, array(new Float(3.1415926), new Float(12.34), new Float(98.76)));
        $json = '{ "list" : { "float" : [3.1415926, 12.34, 98.76] } }';
        $returnValue[] = array($container, $json);
        
        $container = new MultipleContainer(BaseType::STRING, array(new String('Another'), new String('And Another')));
        $json = '{ "list" : { "string" : ["Another", "And Another"] } }';
        $returnValue[] = array($container, $json);

        $container = new MultipleContainer(BaseType::POINT, array(new Point(123, 456), new Point(640, 480)));
        $json = '{ "list" : { "point" : [[123, 456], [640, 480]] } }';
        $returnValue[] = array($container, $json);
        
        $container = new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('D', 'C')));
        $json = '{ "list" : { "pair" : [["A", "B"], ["D", "C"]] } }';
        $returnValue[] = array($container, $json);
        
        $container = new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('D', 'C')));
        $json = '{ "list" : { "directedPair" : [["A", "B"], ["D", "C"]] } }';
        $returnValue[] = array($container, $json);
        
        $container = new MultipleContainer(BaseType::DURATION, array(new Duration('PT5S'), new Duration('PT10S')));
        $json = '{ "list" : { "duration" : ["PT5S", "PT10S"] } }';
        $returnValue[] = array($container, $json);
        
        $container = new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), null, new Boolean(false)));
        $json = '{ "list" : { "boolean": [true, null, false] } }';
        $returnValue[] = array($container, $json);
        
        return $returnValue;
    }
    
    public function unmarshallRecordProvider() {
        $returnValue = array();
        
        $record = new RecordContainer();
        $json = '{ "record" : [] }';
        $returnValue[] = array($record, $json);
        
        $record = new RecordContainer(array('A' => new String('A')));
        $json = '{ "record" : [ { "name" : "A", "base" : { "string" : "A" } } ] }';
        $returnValue[] = array($record, $json);
        
        $record = new RecordContainer(array('A' => new String('A'), 'B' => null));
        $json = '{ "record" : [ { "name" : "A", "base" : { "string" : "A" } }, { "name" : "B", "base" : null } ] }';
        $returnValue[] = array($record, $json);
        
        $record = new RecordContainer(array('A' => null));
        $json = '{ "record" : [ { "name": "A" } ] }';
        $returnValue[] = array($record, $json);
        
        return $returnValue;
    }
    
    public function unmarshallInvalidProvider() {
        return array(
            array(new \stdClass()),
            array(''),
            array('{ "list": [} }'),
            array('{ "base" : { "booleanooo" : true } }'),
            array('{}'),
            array('{ "base" : { "boolean" : "yop" } }'),
            array('[ "base" : { "boolean" : true} ]'),
            array('{ "list" : { "boolean" : null }'),
            array('{ "list" : { } }'),
            array('{ "liste" : { "boolean" : true } } '),
            array('{ "record" : [ { "namez" } ] '),
        );
    }
}
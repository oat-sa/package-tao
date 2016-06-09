<?php
use qtism\common\datatypes\files\FileSystemFile;
use qtism\runtime\pci\json\MarshallingException;
use qtism\runtime\common\OutcomeVariable;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Uri;
use qtism\common\datatypes\IntOrIdentifier;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\QtiDatatype;
use qtism\runtime\pci\json\Marshaller;
use qtism\common\datatypes\Boolean;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class JsonMarshallerTest extends QtiSmTestCase {
	
    /**
     * @dataProvider marshallScalarProvider
     * 
     * @param QtiDatatype|null $scalar
     * @param string $expectedJson
     */
    public function testMarshallScalar($scalar, $expectedJson) {
        $marshaller = new Marshaller();
        $this->assertEquals($expectedJson, $marshaller->marshall($scalar));
    }
    
    /**
     * @dataProvider marshallComplexProvider
     * 
     * @param QtiDatatype $complex
     * @param string $expectedJson
     */
    public function testMarshallComplex(QtiDatatype $complex, $expectedJson) {
        $marshaller = new Marshaller();
        $this->assertEquals($expectedJson, $marshaller->marshall($complex));
    }
    
    /**
     * @dataProvider marshallMultipleProvider
     * 
     * @param MultipleContainer $multiple
     * @param string $expectedJson
     */
    public function testMarshallMultiple(MultipleContainer $multiple, $expectedJson) {
        $marshaller = new Marshaller();
        $this->assertEquals($expectedJson, $marshaller->marshall($multiple));
    }
    
    /**
     * @dataProvider marshallOrderedProvider
     *
     * @param OrederedContainer $ordered
     * @param string $expectedJson
     */
    public function testMarshallOrdered(OrderedContainer $ordered, $expectedJson) {
        $marshaller = new Marshaller();
        $this->assertEquals($expectedJson, $marshaller->marshall($ordered));
    }
    
    /**
     * @dataProvider marshallRecordProvider
     * 
     * @param RecordContainer $record
     * @param string $expectedJson
     */
    public function testMarshallRecord(RecordContainer $record, $expectedJson) {
        $marshaller = new Marshaller();
        $this->assertEquals($expectedJson, $marshaller->marshall($record));
    }
    
    /**
     * @dataProvider marshallStateProvider
     * 
     * @param State $state
     * @param string $expectedJson
     */
    public function testMarshallState(State $state, $expectedJson) {
        $marshaller = new Marshaller();
        $this->assertEquals($expectedJson, $marshaller->marshall($state));
    }
    
    /**
     * @dataProvider marshallInvalidInputProvider
     * 
     * @param mixed $input
     */
    public function testMarshallInvalidInput($input) {
        $this->setExpectedException('qtism\\runtime\\pci\\json\\MarshallingException', '', MarshallingException::NOT_SUPPORTED);
        $marshaller = new Marshaller();
        $marshaller->marshall($input);
    }
    
    public function testMarshallAsArray() {
        $marshaller = new Marshaller();
        $data = $marshaller->marshall(new Integer(12), Marshaller::MARSHALL_ARRAY);
        $this->assertEquals(12, $data['base']['integer']);
    }
    
    public function marshallScalarProvider() {
        return array(
            array(new Boolean(true), json_encode(array('base' => array('boolean' => true)))),
            array(new Boolean(false), json_encode(array('base' => array('boolean' => false)))),
            array(new Integer(1337), json_encode(array('base' => array('integer' => 1337)))),
            array(new Float(1337.1337), json_encode(array('base' => array('float' => 1337.1337)))),
            array(new String("String!"), json_encode(array('base' => array('string' => "String!")))),
            array(new String(""), json_encode(array('base' => array('string' => "")))),
            array(new Identifier("RESP_X32"), json_encode(array('base' => array('identifier' => "RESP_X32")))),
            array(new IntOrIdentifier("RESP_X33"), json_encode(array('base' => array('intOrIdentifier' => "RESP_X33")))),
            array(new IntOrIdentifier(1337), json_encode(array('base' => array('intOrIdentifier' => 1337)))),
            array(new Uri('http://www.taotesting.com'), json_encode(array('base' => array('uri' => 'http://www.taotesting.com')))),
            array(null, json_encode(array('base' => null)))
        );
    }
    
    public function marshallComplexProvider() {
        $samples = self::samplesDir();
        
        $returnValue = array();
        $returnValue[] = array(new Point(10, 20), json_encode(array('base' => array('point' => array(10, 20)))));
        $returnValue[] = array(new Pair('A', 'B'), json_encode(array('base' => array('pair' => array('A', 'B')))));
        $returnValue[] = array(new DirectedPair('a', 'b'), json_encode(array('base' => array('directedPair' => array('a', 'b')))));
        $returnValue[] = array(new Duration('P3DT4H'), json_encode(array('base' => array('duration' => 'P3DT4H'))));
        
        $file = new FileSystemFile($samples . 'datatypes/file/text-plain_text_data.txt');
        $returnValue[] = array($file, json_encode(array('base' => array('file' => array('mime' => $file->getMimeType(), 'data' => base64_encode($file->getData()), 'name' => 'text.txt')))));
        
        $file = new FileSystemFile($samples . 'datatypes/file/image-png_noname_data.png');
        $returnValue[] = array($file, json_encode(array('base' => array('file' => array('mime' => $file->getMimeType(), 'data' => base64_encode($file->getData()))))));
        
        return $returnValue;
    }
    
    public function marshallMultipleProvider() {
        $returnValue = array();
        
        // bool multiple().
        $container = new MultipleContainer(BaseType::BOOLEAN, array());
        $json = json_encode(array('list' => array('boolean' => array())));
        $returnValue[] = array($container, $json);
        
        // bool multiple(true, false, true).
        $container = new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true)));
        $json = json_encode(array('list' => array('boolean' => array(true, false, true))));
        $returnValue[] = array($container, $json);
        
        // bool multiple(true, null, false).
        $container = new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), null, new Boolean(false)));
        $json = json_encode(array('list' => array('boolean' => array(true, null, false))));
        $returnValue[] = array($container, $json);
        
        // bool multiple (null)
        $container = new MultipleContainer(BaseType::BOOLEAN, array(null));
        $json = json_encode(array('list' => array('boolean' => array(null))));
        $returnValue[] = array($container, $json);
        
        // integer multiple(2, 3, 5, 7, 11, 13).
        $container = new MultipleContainer(BaseType::INTEGER, array(new Integer(2), new Integer(3), new Integer(5), new Integer(7), new Integer(11), new Integer(13)));
        $json = json_encode(array('list' => array('integer' => array(2, 3, 5, 7, 11, 13))));
        $returnValue[] = array($container, $json);
        
        // float multiple(3.1415926, 12.34, 98.76).
        $container = new MultipleContainer(BaseType::FLOAT, array(new Float(3.1415926), new Float(12.34), new Float(98.76)));
        $json = json_encode(array('list' => array('float' => array(3.1415926, 12.34, 98.76))));
        $returnValue[] = array($container, $json);
        
        // string multiple("Another", "And Another").
        $container = new MultipleContainer(BaseType::STRING, array(new String("Another"), new String("And another")));
        $json = json_encode(array('list' => array('string' => array("Another", "And another"))));
        $returnValue[] = array($container, $json);
        
        // point multiple(point(123, 456), point(640, 480)).
        $container = new MultipleContainer(BaseType::POINT, array(new Point(123, 456), new Point(640, 480)));
        $json = json_encode(array('list' => array('point' => array(array(123, 456), array(640, 480)))));
        $returnValue[] = array($container, $json);
        
        // pair multiple(pair(A, B), pair(C, D)).
        $container = new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('D', 'C')));
        $json = json_encode(array('list' => array('pair' => array(array('A', 'B'), array('D', 'C')))));
        $returnValue[] = array($container, $json);
        
        // pair multiple(pair(A, B), pair(C, D)).
        $container = new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('D', 'C')));
        $json = json_encode(array('list' => array('directedPair' => array(array('A', 'B'), array('D', 'C')))));
        $returnValue[] = array($container, $json);
        
        // duration multiple("P3Y6M4DT12H30M5S", "P4Y").
        $container = new MultipleContainer(BaseType::DURATION, array(new Duration('PT4M10S'), new Duration('P4Y')));
        $json = json_encode(array('list' => array('duration' => array('PT4M10S', 'P4Y'))));
        $returnValue[] = array($container, $json);
        
        // uri multiple("file:///aFile.txt", "file:///abc.txt").
        $container = new MultipleContainer(BaseType::URI, array(new Uri('file:///aFile.txt'), new Uri('file:///abc.txt')));
        $json = json_encode(array('list' => array('uri' => array('file:///aFile.txt', 'file:///abc.txt'))));
        $returnValue[] = array($container, $json);
        
        // intOrIdentifier multiple(2, "_id").
        $container = new MultipleContainer(BaseType::INT_OR_IDENTIFIER, array(new IntOrIdentifier(2), new IntOrIdentifier('_id')));
        $json = json_encode(array('list' => array('intOrIdentifier' => array(2, '_id'))));
        $returnValue[] = array($container, $json);
        
        // identifier multiple('_id1', 'id2', 'ID3').
        $container = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('_id1'), new Identifier('id2'), new Identifier('ID3')));
        $json = json_encode(array('list' => array('identifier' => array('_id1', 'id2', 'ID3'))));
        $returnValue[] = array($container, $json);
        
        return $returnValue;
    }
    
    public function marshallOrderedProvider() {
        $returnValue = array();
        
        // bool multiple().
        $container = new OrderedContainer(BaseType::BOOLEAN, array());
        $json = json_encode(array('list' => array('boolean' => array())));
        $returnValue[] = array($container, $json);
        
        // bool multiple(true, false, true).
        $container = new OrderedContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true)));
        $json = json_encode(array('list' => array('boolean' => array(true, false, true))));
        $returnValue[] = array($container, $json);
        
        // bool multiple(true, null, false)
        $container = new OrderedContainer(BaseType::BOOLEAN, array(new Boolean(true), null, new Boolean(false)));
        $json = json_encode(array('list' => array('boolean' => array(true, null, false))));
        $returnValue[] = array($container, $json);
        
        // bool multiple (null)
        $container = new OrderedContainer(BaseType::BOOLEAN, array(null));
        $json = json_encode(array('list' => array('boolean' => array(null))));
        $returnValue[] = array($container, $json);
        
        return $returnValue;
    }
    
    public function marshallRecordProvider() {
        $returnValue = array();
        
        // empty record.
        $record = new RecordContainer();
        $json = json_encode(array('record' => array()));
        $returnValue[] = array($record, $json);
        
        // single boolean value record.
        $record = new RecordContainer(array('rock' => new Boolean(true)));
        $json = json_encode(array('record' => array(array('name' => 'rock', 'base' => array('boolean' => true)))));
        $returnValue[] = array($record, $json);
        
        // single null valued record.
        $record = new RecordContainer(array('rock' => null));
        $json = json_encode(array('record' => array(array('name' => 'rock', 'base' => null))));
        $returnValue[] = array($record, $json);
        
        // miscellaneous record.
        $record = new RecordContainer(array('numeric' => new Float(1337.1337), 'null' => null, 'coordinates' => new Point(10, 20)));
        $json = json_encode(array('record' => array(array('name' => 'numeric', 'base' => array('float' => 1337.1337)), array('name' => 'null', 'base' => null), array('name' => 'coordinates', 'base' => array('point' => array(10, 20))))));
        $returnValue[] = array($record, $json);
        
        return $returnValue;
    }
    
    public function marshallStateProvider() {
        $returnValue = array();
        
        // empty state.
        $state = new State();
        $json = json_encode(array());
        $returnValue[] = array($state, $json);
        
        // simple state.
        $state = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA'))));
        $json = json_encode(array('RESPONSE' => array('base' => array('identifier' => 'ChoiceA'))));
        $returnValue[] = array($state, $json);
        
        // complex state 1.
        $state = new State();
        $state->setVariable(new ResponseVariable('RESPONSE1', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')));
        $state->setVariable(new ResponseVariable('RESPONSE2', Cardinality::SINGLE, BaseType::DURATION));
        $state->setVariable(new ResponseVariable('RESPONSE3', Cardinality::RECORD, -1, new RecordContainer(array('A' => new Identifier('A'), 'B' => new Identifier('B')))));
        $json = json_encode(array('RESPONSE1' => array('base' => array('identifier' => 'ChoiceA')), 'RESPONSE2' => array('base' => null), 'RESPONSE3' => array('record' => array(array('name' => 'A', 'base' => array('identifier' => 'A')), array('name' => 'B', 'base' => array('identifier' => 'B'))))));
        $returnValue[] = array($state, $json);
        
        // complex state 2.
        $state = new State();
        $state->setVariable(new OutcomeVariable('OUTCOME1', Cardinality::MULTIPLE, BaseType::FLOAT, new MultipleContainer(BaseType::FLOAT, array(new Float(0.0), new Float(10.10)))));
        $state->setVariable(new ResponseVariable('RESPONSE1', Cardinality::ORDERED, BaseType::POINT, new OrderedContainer(BaseType::POINT, array(new Point(10, 20)))));
        $json = json_encode(array('OUTCOME1' => array('list' => array('float' => array(0.0, 10.10))), 'RESPONSE1' => array('list' => array('point' => array(array(10, 20))))));
        $returnValue[] = array($state, $json);
        
        return $returnValue;
    }
    
    public function marshallInvalidInputProvider() {
        return array(
            array(10),
            array('string!'),
            array(new \stdClass())                
        );
    }
}

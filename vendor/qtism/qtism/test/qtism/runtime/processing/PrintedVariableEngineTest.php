<?php
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\IntOrIdentifier;
use qtism\common\datatypes\Uri;
use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Duration;
use qtism\runtime\common\TemplateVariable;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\processing\PrintedVariableEngine;
use qtism\data\content\PrintedVariable;
use qtism\runtime\common\State;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class PrintedVariableEngineTest extends QtiSmTestCase {
	
    /**
     * @param mixed $value
     * @param string $expected
     * @param string $format
     * @param boolean $powerForm
     * @param integer|string $base
     * @param $integer|string $index
     * @param string $delimiter
     * @param string $field
     * @param stribng $mappingIndicator
     * @dataProvider printedVariableProvider
     */
    public function testPrintedVariable($expected, $identifier, State $state, $format = '', $powerForm = false, $base = 10, $index = -1, $delimiter = ';', $field = '', $mappingIndicator = '=') {
        
        $printedVariable = new PrintedVariable($identifier);
        $printedVariable->setFormat($format);
        $printedVariable->setPowerForm($powerForm);
        $printedVariable->setBase($base);
        $printedVariable->setIndex($index);
        $printedVariable->setDelimiter($delimiter);
        $printedVariable->setField($field);
        $printedVariable->setMappingIndicator($mappingIndicator);
        
        $engine = new PrintedVariableEngine($printedVariable);
        $engine->setContext($state);
        $this->assertEquals($expected, $engine->process());
    }
    
    public function printedVariableProvider() {
        $state = new State();
        
        $state->setVariable(new OutcomeVariable('nullValue', Cardinality::SINGLE, BaseType::BOOLEAN, null));
        
        // Scalar values.
        $state->setVariable(new OutcomeVariable('nonEmptyString', Cardinality::SINGLE, BaseType::STRING, new String('Non Empty String')));
        $state->setVariable(new OutcomeVariable('emptyString', Cardinality::SINGLE, BaseType::STRING, new String('')));
        $state->setVariable(new TemplateVariable('positiveInteger', Cardinality::SINGLE, BaseType::INTEGER, new Integer(25)));
        $state->setVariable(new TemplateVariable('zeroInteger', Cardinality::SINGLE, BaseType::INTEGER, new Integer(0)));
        $state->setVariable(new TemplateVariable('negativeInteger', Cardinality::SINGLE, BaseType::INTEGER, new Integer(-25)));
        $state->setVariable(new TemplateVariable('positiveFloat', Cardinality::SINGLE, BaseType::FLOAT, new Float(25.3455322345)));
        $state->setVariable(new OutcomeVariable('zeroFloat', Cardinality::SINGLE, BaseType::FLOAT, new Float(0.0)));
        $state->setVariable(new OutcomeVariable('negativeFloat', Cardinality::SINGLE, BaseType::FLOAT, new Float(-53000.0)));
        $state->setVariable(new OutcomeVariable('false', Cardinality::SINGLE, BaseType::BOOLEAN, new Boolean(false)));
        $state->setVariable(new OutcomeVariable('true', Cardinality::SINGLE, BaseType::BOOLEAN, new Boolean(true)));
        $state->setVariable(new OutcomeVariable('URI', Cardinality::SINGLE, BaseType::URI, new Uri('http://qtism.taotesting.com')));
        $state->setVariable(new TemplateVariable('zeroIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, new IntOrIdentifier(0)));
        $state->setVariable(new TemplateVariable('positiveIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, new IntOrIdentifier(25)));
        $state->setVariable(new TemplateVariable('zeroIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, new IntOrIdentifier(0)));
        $state->setVariable(new OutcomeVariable('identifierIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, new IntOrIdentifier('woot')));
        $state->setVariable(new TemplateVariable('negativeIntOrIdentifier', Cardinality::SINGLE, BaseType::INTEGER, new Integer(-25)));
        $state->setVariable(new OutcomeVariable('duration', Cardinality::SINGLE, BaseType::DURATION, new Duration('PT3M26S')));
        $state->setVariable(new OutcomeVariable('pair', Cardinality::SINGLE, BaseType::PAIR, new Pair('A', 'B')));
        $state->setVariable(new OutcomeVariable('directedPair', Cardinality::SINGLE, BaseType::DIRECTED_PAIR, new DirectedPair('B', 'C')));
        $state->setVariable(new OutcomeVariable('identifier', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('woot')));
        
        // -- Multiple containers.
        $state->setVariable(new TemplateVariable('multipleIntegerSingle', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER, array(new Integer(10)))));
        $state->setVariable(new TemplateVariable('multipleInteger', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER, array(new Integer(10), new Integer(20), new Integer(-1)))));
        $state->setVariable(new OutcomeVariable('multipleFloat', Cardinality::MULTIPLE, BaseType::FLOAT, new MultipleContainer(BaseType::FLOAT, array(new Float(10.0), new Float(20.0), new Float(-1.0)))));
        $state->setVariable(new OutcomeVariable('multipleString', Cardinality::MULTIPLE, BaseType::STRING, new MultipleContainer(BaseType::STRING, array(new String('Ta'), new String('Daaa'), new String('h'), new String('')))));
        $state->setVariable(new OutcomeVariable('multipleBoolean', Cardinality::MULTIPLE, BaseType::BOOLEAN, new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true), new Boolean(true)))));
        $state->setVariable(new OutcomeVariable('multipleURI', Cardinality::MULTIPLE, BaseType::URI, new MultipleContainer(BaseType::URI, array(new Uri('http://www.taotesting.com'), new Uri('http://www.rdfabout.com')))));
        $state->setVariable(new OutcomeVariable('multipleIdentifier', Cardinality::MULTIPLE, BaseType::IDENTIFIER, new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('9thing'), new Identifier('woot-woot')))));
        $state->setVariable(new TemplateVariable('multipleDuration', Cardinality::MULTIPLE, BaseType::DURATION, new MultipleContainer(BaseType::DURATION, array(new Duration('PT0S'), new Duration('PT3M')))));
        $state->setVariable(new OutcomeVariable('multiplePair', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('C', 'D'), new Pair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('multipleDirectedPair', Cardinality::MULTIPLE, BaseType::DIRECTED_PAIR, new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('C', 'D'), new DirectedPair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('multipleIntOrIdentifier', Cardinality::MULTIPLE, BaseType::INT_OR_IDENTIFIER, new MultipleContainer(BaseType::INT_OR_IDENTIFIER, array(new IntOrIdentifier('woot'), new IntOrIdentifier(25), new IntOrIdentifier(0), new IntOrIdentifier(-25)))));
        $state->setVariable(new OutcomeVariable('multipleEmpty', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER)));
        $state->setVariable(new TemplateVariable('multipleContainsNull', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER, array(new Integer(-10), null, null))));
        
        // -- Ordered containers, no value for the 'index' attribute.
        $state->setVariable(new TemplateVariable('orderedInteger', Cardinality::ORDERED, BaseType::INTEGER, new OrderedContainer(BaseType::INTEGER, array(new Integer(10), new Integer(20), new Integer(-1)))));
        $state->setVariable(new OutcomeVariable('orderedFloat', Cardinality::ORDERED, BaseType::FLOAT, new OrderedContainer(BaseType::FLOAT, array(new Float(10.0), new Float(20.0), new Float(-1.0)))));
        $state->setVariable(new OutcomeVariable('orderedString', Cardinality::ORDERED, BaseType::STRING, new OrderedContainer(BaseType::STRING, array(new String('Ta'), new String('Daaa'), new String('h'), new String('')))));
        $state->setVariable(new OutcomeVariable('orderedBoolean', Cardinality::ORDERED, BaseType::BOOLEAN, new OrderedContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true), new Boolean(true)))));
        $state->setVariable(new OutcomeVariable('orderedURI', Cardinality::ORDERED, BaseType::URI, new OrderedContainer(BaseType::URI, array(new Uri('http://www.taotesting.com'), new Uri('http://www.rdfabout.com')))));
        $state->setVariable(new OutcomeVariable('orderedIdentifier', Cardinality::ORDERED, BaseType::IDENTIFIER, new OrderedContainer(BaseType::IDENTIFIER, array(new Identifier('9thing'), new Identifier('woot-woot')))));
        $state->setVariable(new TemplateVariable('orderedDuration', Cardinality::ORDERED, BaseType::DURATION, new OrderedContainer(BaseType::DURATION, array(new Duration('PT0S'), new Duration('PT3M')))));
        $state->setVariable(new OutcomeVariable('orderedPair', Cardinality::ORDERED, BaseType::PAIR, new OrderedContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('C', 'D'), new Pair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedDirectedPair', Cardinality::ORDERED, BaseType::DIRECTED_PAIR, new OrderedContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('C', 'D'), new DirectedPair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedIntOrIdentifier', Cardinality::ORDERED, BaseType::INT_OR_IDENTIFIER, new OrderedContainer(BaseType::INT_OR_IDENTIFIER, array(new IntOrIdentifier('woot'), new IntOrIdentifier(25), new IntOrIdentifier(0), new IntOrIdentifier(-25)))));
        $state->setVariable(new TemplateVariable('orderedEmpty', Cardinality::ORDERED, BaseType::INTEGER, new OrderedContainer(BaseType::INTEGER)));
        $state->setVariable(new TemplateVariable('orderedContainsNull', Cardinality::ORDERED, BaseType::INTEGER, new OrderedContainer(BaseType::INTEGER, array(null, null, new Integer(10)))));
        
        // -- Ordered containers, value for the 'index' attribute set.
        $state->setVariable(new TemplateVariable('orderedIndexedInteger', Cardinality::ORDERED, BaseType::INTEGER, new OrderedContainer(BaseType::INTEGER, array(new Integer(10), new Integer(20), new Integer(-1)))));
        // The field is extracted from a variable ref.
        $state->setVariable(new OutcomeVariable('fieldVariableRefInteger', Cardinality::SINGLE, BaseType::INTEGER, new Integer(1)));
        // Set up a wrong variable reference for 'index' value.
        $state->setVariable(new OutcomeVariable('fieldVariableRefString', Cardinality::SINGLE, BaseType::STRING, new String('index')));
        $state->setVariable(new OutcomeVariable('orderedIndexedFloat', Cardinality::ORDERED, BaseType::FLOAT, new OrderedContainer(BaseType::FLOAT, array(new Float(10.0), new Float(20.0), new Float(-1.0)))));
        $state->setVariable(new OutcomeVariable('orderedIndexedString', Cardinality::ORDERED, BaseType::STRING, new OrderedContainer(BaseType::STRING, array(new String('Ta'), new String('Daaa'), new String('h'), new String('')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedBoolean', Cardinality::ORDERED, BaseType::BOOLEAN, new OrderedContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true), new Boolean(true)))));
        $state->setVariable(new OutcomeVariable('orderedIndexedURI', Cardinality::ORDERED, BaseType::URI, new OrderedContainer(BaseType::URI, array(new Uri('http://www.taotesting.com'), new Uri('http://www.rdfabout.com')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedIdentifier', Cardinality::ORDERED, BaseType::IDENTIFIER, new OrderedContainer(BaseType::IDENTIFIER, array(new Identifier('9thing'), new Identifier('woot-woot')))));
        $state->setVariable(new TemplateVariable('orderedIndexedDuration', Cardinality::ORDERED, BaseType::DURATION, new OrderedContainer(BaseType::DURATION, array(new Duration('PT0S'), new Duration('PT3M')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedPair', Cardinality::ORDERED, BaseType::PAIR, new OrderedContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('C', 'D'), new Pair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedDirectedPair', Cardinality::ORDERED, BaseType::DIRECTED_PAIR, new OrderedContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('C', 'D'), new DirectedPair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedIntOrIdentifier', Cardinality::ORDERED, BaseType::INT_OR_IDENTIFIER, new OrderedContainer(BaseType::INT_OR_IDENTIFIER, array(new IntOrIdentifier('woot'), new IntOrIdentifier(25), new IntOrIdentifier(0), new IntOrIdentifier(-25)))));
        
        // -- Record containers.
        $state->setVariable(new OutcomeVariable('recordSingle', Cardinality::RECORD, -1, new RecordContainer(array('a' => new Float(25.3)))));
        $state->setVariable(new OutcomeVariable('recordMultiple', Cardinality::RECORD, -1, new RecordContainer(array('a' => new Integer(-3), 'b' => new Float(3.3), 'c' => new Boolean(true), 'd' => new Boolean(false), 'e' => new String('string'), 'f' => new Uri('http://www.rdfabout.com'), 'g' => new Duration('PT3M'), 'h' => new Pair('A', 'B'), 'i' => new DirectedPair('C', 'D')))));
        $state->setVariable(new TemplateVariable('recordEmpty', Cardinality::RECORD, -1, new RecordContainer()));
        $state->setVariable(new OutcomeVariable('recordContainsNull', Cardinality::RECORD, -1, new RecordContainer(array('a' => new Integer(-3), 'b' => null, 'c' => new Boolean(true)))));
        
        // -- Power form.
        $state->setVariable(new OutcomeVariable('powerFormScalarPositiveInteger', Cardinality::SINGLE, BaseType::INTEGER, new Integer(250)));
        $state->setVariable(new OutcomeVariable('powerFormScalarZeroInteger', Cardinality::SINGLE, BaseType::INTEGER, new Integer(0)));
        $state->setVariable(new OutcomeVariable('powerFormScalarNegativeInteger', Cardinality::SINGLE, BaseType::INTEGER, new Integer(-23)));
        $state->setVariable(new OutcomeVariable('powerFormScalarPositiveFloat', Cardinality::SINGLE, BaseType::FLOAT, new Float(250.35)));
        $state->setVariable(new OutcomeVariable('powerFormScalarZeroFloat', Cardinality::SINGLE, BaseType::FLOAT, new Float(0.0)));
        $state->setVariable(new OutcomeVariable('powerFormScalarNegativeFloat', Cardinality::SINGLE, BaseType::FLOAT, new Float(-23.0)));
        
        // -- IMS NumberFormatting. See http://www.imsglobal.org/question/qtiv2p1/imsqti_implv2p1.html#section10017
        $state->setVariable(new OutcomeVariable('integerMinus987', Cardinality::SINGLE, BaseType::INTEGER, new Integer(-987)));
        
        return array(
            array('', 'nonExistingVariable', $state),
            array('', 'nullValue', $state),            
                        
            array('Non Empty String', 'nonEmptyString', $state),
            array('', 'emptyString', $state),
            array('25', 'positiveInteger', $state),
            array('0', 'zeroInteger', $state),
            array('-25', 'negativeInteger', $state),
            array('2.534553e+1', 'positiveFloat', $state),
            array('0.000000e+0', 'zeroFloat', $state),
            array('-5.300000e+4', 'negativeFloat', $state),
            array('false', 'false', $state),
            array('true', 'true', $state),
            array('http://qtism.taotesting.com', 'URI', $state),
            array('25', 'positiveIntOrIdentifier', $state),
            array('0', 'zeroIntOrIdentifier', $state),
            array('-25', 'negativeIntOrIdentifier', $state),
            array('woot', 'identifierIntOrIdentifier', $state),
            array('206', 'duration', $state),
            array('A B', 'pair', $state),
            array('B C', 'directedPair', $state),
            array('woot', 'identifier', $state),

            array('10', 'multipleIntegerSingle', $state),
            array('10;20;-1', 'multipleInteger', $state),
            array('1.000000e+1;2.000000e+1;-1.000000e+0', 'multipleFloat', $state),
            array('Ta;Daaa;h;', 'multipleString', $state),
            array('true;false;true;true', 'multipleBoolean', $state),
            array('http://www.taotesting.com;http://www.rdfabout.com', 'multipleURI', $state),
            array('9thing;woot-woot', 'multipleIdentifier', $state),
            array('0;180', 'multipleDuration', $state),
            array('A B;C D;E F', 'multiplePair', $state),
            array('woot;25;0;-25', 'multipleIntOrIdentifier', $state),
            array('', 'multipleEmpty', $state),
            array('-10;null;null', 'multipleContainsNull', $state),
                        
            array('10;20;-1', 'orderedInteger', $state),
            array('1.000000e+1;2.000000e+1;-1.000000e+0', 'orderedFloat', $state),
            array('Ta;Daaa;h;', 'orderedString', $state),
            array('true;false;true;true', 'orderedBoolean', $state),
            array('http://www.taotesting.com;http://www.rdfabout.com', 'orderedURI', $state),
            array('9thing;woot-woot', 'orderedIdentifier', $state),
            array('0;180', 'orderedDuration', $state),
            array('A B;C D;E F', 'orderedPair', $state),
            array('woot;25;0;-25', 'orderedIntOrIdentifier', $state),
            array('null;null;10', 'orderedContainsNull', $state),
                        
            array('10', 'orderedIndexedInteger', $state, '', false, 10, 0),
            array('20', 'orderedIndexedInteger', $state, '', false, 10, 1),
            array('-1', 'orderedIndexedInteger', $state, '', false, 10, 2),
                        
            // The index does not exist, the full container content is displayed.
            array('10;20;-1', 'orderedIndexedInteger', $state, '', false, 10, 3),
            // The index is a valid variable reference.
            array('20', 'orderedIndexedInteger', $state, '', false, 10, 'fieldVariableRefInteger'),
            // The index is an unresolvable variable reference, the full container content is displayed.
            array('10;20;-1', 'orderedIndexedInteger', $state, '', false, 10, 'XRef'),
            // The index value is not a string, the full container content is displayed.
            array('10;20;-1', 'orderedIndexedInteger', $state, '', false, 10, 'fieldVariableRefString'),
                 
            array('1.000000e+1', 'orderedIndexedFloat', $state, '', false, 10, 0),
            array('2.000000e+1', 'orderedIndexedFloat', $state, '', false, 10, 1),
            array('-1.000000e+0', 'orderedIndexedFloat', $state, '', false, 10, 2), 
            array('Ta', 'orderedIndexedString', $state, '', false, 10, 0),
            array('Daaa', 'orderedIndexedString', $state, '', false, 10, 1),
            array('h', 'orderedIndexedString', $state, '', false, 10, 2),
            array('', 'orderedIndexedString', $state, '', false, 10, 3),
            array('true', 'orderedIndexedBoolean', $state, '', false, 10, 0),
            array('false', 'orderedIndexedBoolean', $state, '', false, 10, 1),
            array('true', 'orderedIndexedBoolean', $state, '', false, 10, 2),
            array('true', 'orderedIndexedBoolean', $state, '', false, 10, 3),
            array('http://www.taotesting.com', 'orderedIndexedURI', $state, '', false, 10, 0),
            array('http://www.rdfabout.com', 'orderedIndexedURI', $state, '', false, 10, 1),
            array('9thing', 'orderedIndexedIdentifier', $state, '', false, 10, 0),
            array('woot-woot', 'orderedIndexedIdentifier', $state, '', false, 10, 1),
            array('0', 'orderedIndexedDuration', $state, '', false, 10, 0),
            array('180', 'orderedIndexedDuration', $state, '', false, 10, 1),
            array('A B', 'orderedIndexedPair', $state, '', false, 10, 0),
            array('C D', 'orderedIndexedPair', $state, '', false, 10, 1),
            array('E F', 'orderedIndexedPair', $state, '', false, 10, 2),
            array('A B', 'orderedIndexedDirectedPair', $state, '', false, 10, 0),
            array('C D', 'orderedIndexedDirectedPair', $state, '', false, 10, 1),
            array('E F', 'orderedIndexedDirectedPair', $state, '', false, 10, 2),
            array('woot', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 0),
            array('25', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 1),
            array('0', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 2),
            array('-25', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 3),

            // -- Power form (only in force with float values.
            array('250', 'powerFormScalarPositiveInteger', $state, '', true),
            array('0', 'powerFormScalarZeroInteger', $state, '', true),
            array('-23', 'powerFormScalarNegativeInteger', $state, '', true),
            array('2.503500 x 10²', 'powerFormScalarPositiveFloat', $state, '', true),
            array('0.000000 x 10⁰', 'powerFormScalarZeroFloat', $state, '', true),
            array('-2.300000 x 10¹', 'powerFormScalarNegativeFloat', $state, '', true),
                        
            // -- Record containers.
            array('a=2.530000e+1', 'recordSingle', $state),
            array('a=-3;b=3.300000e+0;c=true;d=false;e=string;f=http://www.rdfabout.com;g=180;h=A B;i=C D', 'recordMultiple', $state),
            array('', 'recordEmpty', $state),
            array('a=-3;b=null;c=true', 'recordContainsNull', $state),
                        
            // -- Funny format tests.
            array('bla', 'positiveInteger', $state, 'bla'),
            array(' yeah', 'positiveInteger', $state, '%-P yeah'),
            
            // -- Real tests with format.
            array('25', 'positiveInteger', $state, '%s'),
            array('Integer as string:25', 'positiveInteger', $state, 'Integer as string:%s'),
            array('Preceding with zeros: 0000000025', 'positiveInteger', $state, 'Preceding with zeros: %010d'),
            array('Preceding with zeros (signed): +000000025', 'positiveInteger', $state, 'Preceding with zeros (signed): %+010i'),
            array('Preceding with blanks:         25', 'positiveInteger', $state, 'Preceding with blanks: %10d'),
            array('31', 'positiveInteger', $state, '%#o'),
            array('31', 'positiveFloat', $state, '%+o'),
                        
            array('-987', 'integerMinus987', $state, '%i'),
        );
    }
}
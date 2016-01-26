<?php

use qtism\data\content\xhtml\tables\Table;

use qtism\data\content\xhtml\tables\TbodyCollection;
use qtism\data\content\xhtml\tables\Tbody;
use qtism\common\collections\IdentifierCollection;
use qtism\data\content\xhtml\tables\TableCellCollection;
use qtism\data\content\xhtml\tables\Thead;
use qtism\data\content\xhtml\tables\Col;
use qtism\data\content\xhtml\tables\ColCollection;
use qtism\data\content\xhtml\text\Strong;
use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\tables\Caption;
use qtism\data\content\TextRun;
use qtism\data\content\FlowCollection;
use qtism\data\content\xhtml\tables\Th;
use qtism\data\content\xhtml\tables\Td;
use qtism\data\content\xhtml\tables\Tr;
use qtism\data\content\xhtml\tables\TrCollection;
use qtism\data\content\xhtml\tables\TableCellScope;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class TableMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $th1 = new Th('firstname');
	    $th1->setContent(new FlowCollection(array(new TextRun('First Name'))));
	    $th1->setAxis('identity');
	    $th1->setScope(TableCellScope::COL);
	    $th2 = new Th('lastname');
	    $th2->setContent(new FlowCollection(array(new TextRun('Last Name'))));
	    $th2->setAxis('identity');
	    $th2->setScope(TableCellScope::COL);
	    $tr = new Tr(new TableCellCollection(array($th1, $th2)));
	    $thead = new Thead(new TrCollection(array($tr)));
	    
	    $caption = new Caption();
	    $strong = new Strong();
	    $strong->setContent(new InlineCollection(array(new TextRun('people'))));
	    $caption->setContent(new InlineCollection(array(new TextRun('Some '), $strong, new TextRun(' ...'))));
	    
	    $col1 = new Col();
	    $col1->setSpan(1);
	    $col2 = new Col();
	    $col2->setSpan(1);
	    $cols = new ColCollection(array($col1, $col2));
	    
	    $td1 = new Td();
	    $td1->setContent(new FlowCollection(array(new TextRun('John'))));
	    $td1->setRowspan(1);
	    $td1->setColspan(1);
	    $td1->setHeaders(new IdentifierCollection(array('firstname')));
	    $td2 = new Td();
	    $td2->setContent(new FlowCollection(array(new TextRun('Dunbar Smith Wayson'))));
	    $td2->setHeaders(new IdentifierCollection(array('lastname')));
	    $td2->setAbbr('Dunbar S.W.');
	    $tr1 = new Tr(new TableCellCollection(array($td1, $td2)));
	    
	    $td1 = new Td();
	    $td1->setContent(new FlowCollection(array(new TextRun('Flash'))));
	    $td2 = new Td();
	    $td2->setContent(new FlowCollection(array(new TextRun('Gordon'))));
	    $tr2 = new Tr(new TableCellCollection(array($td1, $td2)));
	    
	    $tbody = new Tbody(new TrCollection(array($tr1, $tr2)));
	    $tbodies = new TbodyCollection(array($tbody));
	    
	    $table = new Table($tbodies, 'my-table', 'qti table');
	    $table->setSummary('Some people...');
	    $table->setThead($thead);
	    $table->setCaption($caption);
	    $table->setCols($cols);
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($table);
	    $element = $marshaller->marshall($table);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    
	    $expected = '<table summary="Some people..." id="my-table" class="qti table">';
	    $expected .= '<caption>Some <strong>people</strong> ...</caption>';
	    $expected .= '<col/>';
	    $expected .= '<col/>';
	    $expected .= '<thead>';
	    $expected .= '<tr>';
	    $expected .= '<th scope="col" axis="identity" id="firstname">First Name</th>';
	    $expected .= '<th scope="col" axis="identity" id="lastname">Last Name</th>';
	    $expected .= '</tr>';
	    $expected .= '</thead>';
	    $expected .= '<tbody>';
	    $expected .= '<tr>';
	    $expected .= '<td headers="firstname" rowspan="1" colspan="1">John</td>';
	    $expected .= '<td headers="lastname" abbr="Dunbar S.W.">Dunbar Smith Wayson</td>';
	    $expected .= '</tr>';
	    $expected .= '<tr>';
	    $expected .= '<td>Flash</td>';
	    $expected .= '<td>Gordon</td>';
	    $expected .= '</tr>';
	    $expected .= '</tbody>';
	    $expected .= '</table>';
	    
	    $this->assertEquals($expected, $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $table = $this->createComponentFromXml('
	        <table id="my-table" class="qti table" summary="Some people...">
                <caption>Some <strong>people</strong> ...</caption>
	            <col span="1"/>
	            <col span="1"/>
	            <thead>
	                <tr>
                        <th axis="identity" id="firstname" scope="col">First Name</th>
	                    <th axis="identity" id="lastname" scope="col">Last Name</th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td headers="firstname" rowspan="1" colspan="1">John</td>
	                    <td headers="lastname" abbr="Dunbar S.W.">Dunbar Smith Wayson</td>
	                </tr>
	                <tr>
                        <td headers="firstname">Flash</td>
	                    <td headers="lastname">Gordon</td>
	                </tr>
	            </tbody>
	        </table>
	    ');
	    
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\tables\\Table', $table);
	    $this->assertEquals('my-table', $table->getId());
	    $this->assertEquals('qti table', $table->getClass());
	    $this->assertEquals('Some people...', $table->getSummary());
	    
	    $thead = $table->getThead();
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\tables\\Thead', $thead);
	    $trs = $thead->getContent();
	    $this->assertEquals(1, count($trs));
	    $ths = $trs[0]->getContent();
	    $this->assertEquals('firstname', $ths[0]->getId());
	    $this->assertEquals(TableCellScope::COL, $ths[0]->getScope());
	    $this->assertEquals('identity', $ths[0]->getAxis());
	    $thContent = $ths[0]->getContent();
	    $this->assertEquals('First Name', $thContent[0]->getContent());
	    $this->assertEquals('lastname', $ths[1]->getId());
	    $this->assertEquals(TableCellScope::COL, $ths[1]->getScope());
	    $this->assertEquals('identity', $ths[1]->getAxis());
	    $thContent = $ths[1]->getContent();
	    $this->assertEquals('Last Name', $thContent[0]->getContent());
	    
	    $tbodies = $table->getTbodies();
	    $this->assertEquals(1, count($tbodies));
	    
	    $trs = $tbodies[0]->getContent();
	    $this->assertEquals(2, count($trs));
	    
	    $tr1 = $trs[0];
	    $this->assertEquals(2, count($tr1->getContent()));
	    
	    $tds = $tr1->getContent();
	    $tdHeaders = $tds[0]->getHeaders();
	    $this->assertEquals(1, count($tdHeaders));
	    $this->assertEquals('firstname', $tdHeaders[0]);
	    $tdHeaders = $tds[1]->getHeaders();
	    $this->assertEquals(1, count($tdHeaders));
	    $this->assertEquals('lastname', $tdHeaders[0]);
	    $this->assertEquals('Dunbar S.W.', $tds[1]->getAbbr());
	    $tdContent = $tds[0]->getContent();
	    $this->assertEquals('John', $tdContent[0]->getContent());
	    $this->assertEquals(1, $tds[0]->getRowspan());
	    $this->assertEquals(1, $tds[0]->getColspan());
	    
	    $tdContent = $tds[1]->getContent();
	    $this->assertEquals('Dunbar Smith Wayson', $tdContent[0]->getContent());
	    
	    $tr2 = $trs[1];
	    $this->assertEquals(2, count($tr2->getContent()));
	    
	    $caption = $table->getCaption();
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\tables\\Caption', $caption);
	    $captionContent = $caption->getContent();
	    $this->assertEquals($captionContent[0]->getContent(), 'Some ');
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Strong', $captionContent[1]);
	    $strongContent = $captionContent[1]->getContent();
	    $this->assertEquals('people', $strongContent[0]->getContent());
	    $this->assertEquals(' ...', $captionContent[2]->getContent());
	    
	    $cols = $table->getCols();
	    $this->assertEquals(2, count($cols));
	    $this->assertEquals(1, $cols[0]->getSpan());
	    $this->assertEquals(1, $cols[1]->getspan());
	}
}

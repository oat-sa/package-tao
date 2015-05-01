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
 *               2013-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
use oat\tao\test\TaoPhpUnitTestRunner;
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * This test case aims at testing the Translation classes of TAO, the reading and
 * the writing processes, ...
 *
 * IMPORTANT: SAVE THIS FILE AS UTF-8. IT CONTAINS CROSS-CULTURAL CONSTANTS !
 *
 * @author Jerome Bogaerts <jerome.bogaerts@tudor.lu>
 * @package tao

 */
class TranslationTest extends TaoPhpUnitTestRunner {

	const RAW_PO = '/samples/sample_raw.po';
	const RAW_PO_WITH_CONTEXT = '/samples/sample_raw_with_context.po';
	const ESCAPING_PO = '/samples/sample_escaping.po';
	const SORTING_PO = '/samples/sample_sort.po';
	const ANNOTATIONS_PO = '/samples/sample_annotations.po';
	const TEMP_PO = 'tao.test.translation.writing';
	const TEMP_RDF = 'tao.test.translation.writing';
	const TAO_MANIFEST = '/samples/structures/tao';
	const GROUPS_MANIFEST = '/samples/structures/groups';
	const ITEMS_MANIFEST = '/samples/structures/items';
	const FAKE_ACTIONS = '/samples/fakeSourceCode/actions/';
	const FAKE_VIEWS = '/samples/fakeSourceCode/views/';
	const FAKE_RDF_LANG_DESC = '/samples/rdf/tao_messages_DE.rdf';
	const FAKE_RDF_TRANSLATION_MODEL = '/samples/locales/en-YO/tao.rdf';
	const FAKE_RDF_TRANSLATION_MODEL_ANNOTATIONS = '/samples/rdf/translation_model_with_annotations.rdf';

	/**
	 * Test of the different classes composing the Translation Model.
	 */
	public function testTranslationModel() {

		// en-US (American English) to en-YA (Yoda English) translation units.
		$tu1 = new tao_helpers_translation_TranslationUnit();
		$tu1->setSource('May the force be with you.');
		$tu1->setTarget('The force with you may be.');
		$tu2 = new tao_helpers_translation_TranslationUnit();
		$tu2->setSource('The dark side smells hate.');
		$tu2->setTarget('Hate the dark side smells.');
		$tu3 = new tao_helpers_translation_TranslationUnit();
		$tu3->setSource('Leia Organa of Alderaan is beautiful.');
		$tu3->setTarget('Beautiful Leia Organa of Alderaan is.');

		// Default source and target languages of translation units is en-US.
		$this->assertTrue($tu1->getSourceLanguage() == tao_helpers_translation_Utils::getDefaultLanguage());
		$this->assertTrue($tu2->getTargetLanguage() == tao_helpers_translation_Utils::getDefaultLanguage());

		$tu1->setSourceLanguage('en-US');
		$tu1->setTargetLanguage('en-YA');
		$tu2->setSourceLanguage('en-US');
		$tu2->setTargetLanguage('en-YA');
		$tu3->setSourceLanguage('en-US');
		$tu3->setTargetLanguage('en-YA');

		// Test source and target languages assignment at TranslationUnit level.
		$this->assertEquals('en-US', $tu2->getSourceLanguage());
		$this->assertEquals('en-YA', $tu3->getTargetLanguage());

		$tf = new tao_helpers_translation_TranslationFile();
		$tf->setSourceLanguage('en-US');
		$tf->setTargetLanguage('en-YA');
		$this->assertEquals($tf->getSourceLanguage(), 'en-US');
		$this->assertEquals($tf->getTargetLanguage(), 'en-YA');

		$tf->addTranslationUnit($tu1);
		$tf->addTranslationUnit($tu2);
		$tf->addTranslationUnit($tu3);

		$tus = $tf->getTranslationUnits();

		$this->assertTrue($tu1 == $tus[0]);
		$this->assertTrue($tu2 == $tus[1]);
		$this->assertTrue($tu3 == $tus[2]);

		$this->assertEquals('May the force be with you.', $tu1->getSource());
		$this->assertEquals('Hate the dark side smells.', $tu2->getTarget());

		$tu3->setSource('Lando Calrician is a great pilot.');
		$tu3->setTarget('A great pilot Lando Calrician is.');
		$this->assertEquals('Lando Calrician is a great pilot.', $tu3->getSource());
		$this->assertEquals('A great pilot Lando Calrician is.', $tu3->getTarget());
		$tu4 = new tao_helpers_translation_TranslationUnit();
		$tu4->setSource('There is another Skywalker.');
		$tu4->setTarget('Another Skywalker there is.');
		$tf->addTranslationUnit($tu4);
		$tus = $tf->getTranslationUnits();
		$tu4 = $tus[3];
		$this->assertEquals('en-YA', $tu4->getTargetLanguage());
		$newTu = new tao_helpers_translation_TranslationUnit();
		$newTu->setSource('Lando Calrician is a great pilot.');
		$newTu->setTarget('Han Solo is a great pilot.');
		$tf->addTranslationUnit($newTu);
		$tus = $tf->getTranslationUnits();
		$tu3 = $tus[2];
		$this->assertEquals(4, count($tus));
		$this->assertEquals('Lando Calrician is a great pilot.', $tu3->getSource());
		$this->assertEquals('Han Solo is a great pilot.', $tu3->getTarget());
		// Test Annotable implementation for translationUnit & translationFile.
		$tf = new tao_helpers_translation_TranslationFile();
		$this->assertTrue(is_array($tf->getAnnotations()), "Annotations for a newly instantiated translation file should be an array.");
		$this->assertTrue(count($tf->getAnnotations()) == 2);
		$tf->addAnnotation('context', 'Unit Testing');
		$tf->addAnnotation('author', 'Jane Doe');
		$this->assertTrue($tf->getAnnotation('context') == array('name' => 'context', 'value' => 'Unit Testing'));
		$this->assertTrue($tf->getAnnotation('author') == array('name' => 'author', 'value' => 'Jane Doe'));
		$this->assertEquals($tf->getAnnotations(), array('sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'context' => 'Unit Testing',
			'author' => 'Jane Doe'));
		$tf->removeAnnotation('author');
		$this->assertTrue($tf->getAnnotation('author') == null);
		$this->assertEquals($tf->getAnnotations(), array('sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'context' => 'Unit Testing'));

		$tu = new tao_helpers_translation_TranslationUnit('test', 'test');
		$this->assertTrue(is_array($tu->getAnnotations()), "Annotations for a newly instantiated translation unit should be an array.");
		$this->assertTrue(count($tu->getAnnotations()) == 2);
		$tu->addAnnotation('context', 'Unit Testing');
		$tu->addAnnotation('author', 'Jane Doe');
		$this->assertTrue($tu->getAnnotation('context') == array('name' => 'context', 'value' => 'Unit Testing'));
		$this->assertTrue($tu->getAnnotation('author') == array('name' => 'author', 'value' => 'Jane Doe'));
		$this->assertEquals($tu->getAnnotations(), array('sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'context' => 'Unit Testing',
			'author' => 'Jane Doe'));
		$tu->removeAnnotation('author');
		$this->assertTrue($tu->getAnnotation('author') == null);
		$this->assertEquals($tu->getAnnotations(), array('sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'context' => 'Unit Testing'));

		// Test utils.
		$this->assertEquals(tao_helpers_translation_Utils::getDefaultLanguage(), 'en-US');
	}

	public function testPOTranslationReading() {
		$po = new tao_helpers_translation_POFileReader(dirname(__FILE__) . self::RAW_PO);
		$po->read();
		$tf = $po->getTranslationFile();
		$tus = $tf->getTranslationUnits();

		// Test default values of TranslationFile. PO files does not
		// contain language information AFAIK.
		$this->assertTrue($tf->getSourceLanguage() == tao_helpers_translation_Utils::getDefaultLanguage());
		$this->assertTrue($tf->getTargetLanguage() == tao_helpers_translation_Utils::getDefaultLanguage());

		$this->assertEquals(count($tus), 4);
		$this->assertEquals('First Try', $tus[0]->getSource());
		$this->assertEquals('', $tus[0]->getTarget());
		$this->assertEquals('Thïs téxt cöntàin$ wéîRd chárâctêrS beçÁuse öf I18N', $tus[1]->getSource());
		$this->assertEquals('', $tus[1]->getTarget());
		$this->assertEquals('This translation will be a very long text', $tus[2]->getSource());
		$this->assertEquals('', $tus[2]->getTarget());
		$this->assertEquals('And this one will contain escaping characters', $tus[3]->getSource());
		$this->assertEquals('', $tus[3]->getTarget());

		// We can test here the change of file while keeping the same instance
		// of FileReader.
		$po->setFilePath(dirname(__FILE__) . self::ESCAPING_PO);
		$po->read();
		$tf = $po->getTranslationFile();
		$tus = $tf->getTranslationUnits();

		$this->assertEquals(4, count($tus));
		$this->assertEquals('The blackboard of Lena is full of "Shakespeare" quotes.', $tus[0]->getSource());
		$this->assertEquals('L\'ardoise de Léna est pleine de citations de "Shakespeare".', $tus[0]->getTarget());
		$this->assertEquals('Thïs téxt cöntàin$ wéîRd chárâctêrS beçÁuse öf I18N', $tus[1]->getSource());
		$this->assertEquals('Ce téxtê cÖntîEn$ de drÔlés dE çÄrÂctÈres @ cAµ$£ dé l\'I18N', $tus[1]->getTarget());
		$this->assertEquals('This translation will be a very long text', $tus[2]->getSource());
		$this->assertEquals('C\'est en effet un texte très très long car j\'aime parler. Grâce à ce test, je vais pouvoir vérifier si les msgstr multilignes sont correctement interpretés par ', $tus[2]->getTarget());
		$this->assertEquals('And this one will contain escaping characters', $tus[3]->getSource());
		$this->assertEquals("Alors je vais passer une ligne \net aussi faire des tabulations \t car c'est très cool.", $tus[3]->getTarget());


		//test ability to read context of po messages
		$po = new tao_helpers_translation_POFileReader(dirname(__FILE__) . self::RAW_PO_WITH_CONTEXT);
		$po->read();
		$tf = $po->getTranslationFile();
		$tus = $tf->getTranslationUnits();

		// Test default values of TranslationFile. PO files can contain language information (feature used by POEdit, at least )
		$this->assertEquals(tao_helpers_translation_Utils::getDefaultLanguage(), $tf->getSourceLanguage());
		$this->assertEquals('de-DE', $tf->getTargetLanguage());

		$this->assertEquals(count($tus), 4);
		$this->assertEquals('label', $tus[0]->getContext());
		$this->assertEquals('comment', $tus[1]->getContext());
		$this->assertEquals('', $tus[3]->getContext());
	}


	public function testPOTranslationSorting() {
		$pr = new tao_helpers_translation_POFileReader(dirname(__FILE__) . self::SORTING_PO);
		$pr->read();
		$tf = $pr->getTranslationFile();
		$unsortedTus = $tf->getTranslationUnits();

		// Ascending case-insensitive.
		$sortedTus = $tf->sortBySource(tao_helpers_translation_TranslationFile::SORT_ASC);
		$this->assertFalse(empty($sortedTus),'array should not be empty');
		$this->assertEquals(' This begins with a white space', $sortedTus[0]->getSource());
		$this->assertEquals('kings are great', $sortedTus[8]->getSource());
		$this->assertEquals('öhïs téxt cöntàin$ wéîRd chárâctêrS beçÁuse öf I18N', $sortedTus[10]->getSource());

		$sortedTus = $tf->sortBySource(tao_helpers_translation_TranslationFile::SORT_ASC_I);
		$this->assertEquals('12 is also a number', $sortedTus[3]->getSource());
		$this->assertEquals('kings are great', $sortedTus[6]->getSource());
		$this->assertEquals('Zapata', $sortedTus[8]->getSource());

		$sortedTus = $tf->sortBySource(tao_helpers_translation_TranslationFile::SORT_DESC);
		$this->assertEquals('Koalas are great', $sortedTus[4]->getSource());
		$this->assertEquals('12 is also a number', $sortedTus[7]->getSource());
		$this->assertEquals('- List1', $sortedTus[9]->getSource());

		$sortedTus = $tf->sortBySource(tao_helpers_translation_TranslationFile::SORT_DESC_I);
		$this->assertEquals('Zapata', $sortedTus[2]->getSource());
		$this->assertEquals('Ahloa', $sortedTus[5]->getSource());
		$this->assertEquals(' This begins with a white space', $sortedTus[10]->getSource());
	}


	public function testPOTranslationWriting(){
		// -- First test
		$pr = new tao_helpers_translation_POFileReader(dirname(__FILE__) . self::RAW_PO);
		$pr->read();
		$tf1 = $pr->getTranslationFile();

		// We serialize the TranslationFile and read it again to check equivalence.
		$filePath = tempnam('/tmp', self::TEMP_PO); // Will try in the correct folder automatically for Win32 c.f. PHP website.
		$pw = new tao_helpers_translation_POFileWriter($filePath, $tf1);
		$pw->write();

		$pr->setFilePath($filePath);
		$pr->read();
		$tf2 = $pr->getTranslationFile();

		// We can now compare them.
		$this->assertTrue('' . $tf1 == '' . $tf2);
		unlink($filePath);

		// -- Second test
		$pr->setFilePath(dirname(__FILE__) . self::ESCAPING_PO);
		$pr->read();
		$tf1 = $pr->getTranslationFile();

		// Serialize and compare later.
		$filePath = tempnam('/tmp', self::TEMP_PO);
		$pw->setFilePath($filePath);
		$pw->setTranslationFile($tf1);
		$pw->write();

		$pr->setFilePath($filePath);
		$pr->read();
		$tf2 = $pr->getTranslationFile();

		// We compare ...
		$this->assertTrue('' . $tf1 == '' . $tf2);
		unlink($filePath);
	}

	public function testJavaScriptTranslationWriting() {
		$jsFilePath = tempnam('/tmp', self::TEMP_PO);
		$pr = new tao_helpers_translation_POFileReader(dirname(__FILE__) . self::RAW_PO);
		$pr->read();
		$tf = $pr->getTranslationFile();
		$tw = new tao_helpers_translation_JSFileWriter($jsFilePath, $tf);
		$tw->write();
		$this->assertTrue(file_exists($jsFilePath));
		unlink($jsFilePath);

		$jsFilePath = tempnam('/tmp', self::TEMP_PO);
		$pr->setFilePath(dirname(__FILE__) . self::ESCAPING_PO);
		$pr->read();
		$tf = $pr->getTranslationFile();
		$tw->setFilePath($jsFilePath);
		$tw->setTranslationFile($tf);
		$tw->write();
		$this->assertTrue(file_exists($jsFilePath));
		unlink($jsFilePath);
	}

	public function testPHPTranslationWriting(){
		$phpFilePath = tempnam('/tmp', self::TEMP_PO);
		$pr = new tao_helpers_translation_POFileReader(dirname(__FILE__) . self::ESCAPING_PO);
		$pr->read();
		$tf = $pr->getTranslationFile();
		$tw = new tao_helpers_translation_PHPFileWriter($phpFilePath, $tf);
		$tw->write();
		$this->assertTrue(file_exists($phpFilePath));

		unlink($phpFilePath);
	}

	public function testManifestExtraction() {
		$taoStructurePath = dirname(__FILE__) . self::TAO_MANIFEST . '/structures.xml';
		$extractor = new tao_helpers_translation_StructureExtractor($taoStructurePath);
		$extractor->extract();
		$tus = $extractor->getTranslationUnits();
		$this->assertEquals(5, count($tus));
		$this->assertEquals('Users', $tus[0]->getSource());
		$this->assertEquals('Manage users', $tus[1]->getSource());
		$this->assertEquals('Add a user', $tus[2]->getSource());
		$this->assertEquals('Edit a user', $tus[3]->getSource());
	}

	public function testMultipleManfiestExtraction() {
		$taoStructurePaths = array(dirname(__FILE__) . self::GROUPS_MANIFEST. '/structures.xml',
			dirname(__FILE__) . self::ITEMS_MANIFEST. '/structures.xml');

		$extractor = new tao_helpers_translation_StructureExtractor($taoStructurePaths);
		$extractor->extract();
		$tus = $extractor->getTranslationUnits();

		$this->assertEquals(26, count($tus));
		$this->assertEquals('Search', $tus[6]->getSource());
		$this->assertEquals('Delete', $tus[9]->getSource());
		$this->assertEquals('Items', $tus[17]->getSource());
		$this->assertEquals('Preview', $tus[24]->getSource());
	}

	public function testSourceExtraction() {
		// Test with only PHP Actions.
		$sourceCodePaths = array(dirname(__FILE__) . self::FAKE_ACTIONS);
		$extensions = array('php');
		$extractor = new tao_helpers_translation_SourceCodeExtractor($sourceCodePaths, $extensions);
		$extractor->extract();
		$tus = $extractor->getTranslationUnits();
		$this->assertEquals(count($tus), 21);

		// Complete test.
		$extensions = array('php', 'tpl', 'js');
		$sourceCodePaths = array(dirname(__FILE__) . self::FAKE_ACTIONS,
			dirname(__FILE__) . self::FAKE_VIEWS);
		$extractor->setFileTypes($extensions);
		$extractor->setPaths($sourceCodePaths);
		$extractor->extract();
		$tus = $extractor->getTranslationUnits();
		$this->assertEquals(count($tus), 60);
		$this->assertEquals('Import', $tus[1]->getSource());
		$this->assertEquals(' Please select the input data format to import ', $tus[2]->getSource());
		$this->assertEquals('Please upload a CSV file formated as "defined" %min by %max the options above.', $tus[5]->getSource());
		$this->assertEquals("Please upload \t an RDF file.\n\n", $tus[8]->getsource());
	}

	public function testRDFTranslationModel() {

	}

	public function testRDFTranslationExtraction(){
		$paths = array(dirname(__FILE__) . self::FAKE_RDF_LANG_DESC);
		$rdfExtractor = new tao_helpers_translation_RDFExtractor($paths);

		$rdfExtractor->setTranslatableProperties(array('http://www.w3.org/2000/01/rdf-schema#label',
			'http://www.w3.org/2000/01/rdf-schema#comment'));
		$rdfExtractor->extract();
		$tus = $rdfExtractor->getTranslationUnits();
		$this->assertTrue(count($tus) == 6);

		// Test 3 Translation Units at random.
		$this->assertEquals('http://www.tao.lu/Ontologies/TAO.rdf#LangDE', $tus[1]->getSubject());
		$this->assertEquals('http://www.w3.org/2000/01/rdf-schema#label', $tus[1]->getPredicate());
		$this->assertEquals('Allemand', $tus[1]->getSource());
		$this->assertEquals('Allemand', $tus[1]->getTarget());
		$this->assertEquals('en-US', $tus[1]->getSourceLanguage());
		$this->assertEquals('FR', $tus[1]->getTargetLanguage());
		$this->assertEquals('http://www.tao.lu/Ontologies/TAO.rdf#LangDE', $tus[2]->getSubject());
		$this->assertEquals('http://www.w3.org/2000/01/rdf-schema#label', $tus[2]->getPredicate());
		$this->assertEquals('Deutsch', $tus[2]->getSource());
		$this->assertEquals('Deutsch', $tus[2]->getTarget());
		$this->assertEquals('en-US', $tus[2]->getSourceLanguage());
		$this->assertEquals('DE', $tus[2]->getTargetLanguage());
		$this->assertEquals('http://www.tao.lu/Ontologies/TAO.rdf#LangDE', $tus[5]->getSubject());
		$this->assertEquals('http://www.w3.org/2000/01/rdf-schema#comment', $tus[5]->getPredicate());
		$this->assertEquals('The German language.', $tus[5]->getSource());
		$this->assertEquals('The German language.', $tus[5]->getTarget());
		$this->assertEquals('en-US', $tus[5]->getSourceLanguage());
		$this->assertEquals('EN', $tus[5]->getTargetLanguage());
	}

	public function testRDFTranslationReading(){
		// We test a file that is in Yoda English (en-YO) and that is a translation
		// of the TAO 2.2 ontology.
		$rdfFilePath = dirname(__FILE__) . self::FAKE_RDF_TRANSLATION_MODEL;
		$reader = new tao_helpers_translation_RDFFileReader($rdfFilePath);
		$reader->read();
		$tus = $reader->getTranslationFile()->getTranslationUnits();

		$this->assertTrue(is_array($tus), "Translation units provided by the RDFFileReader must be as an array.");

		$this->assertEquals($tus[0]->getTarget(), "Object TAO", "Unexpected target for RDFTranslationUnit.");
		$this->assertEquals($tus[0]->getSubject(), "http://www.tao.lu/Ontologies/TAO.rdf#TAOObject", "Unexpected subject for RDFTranslationUnit.");
		$this->assertEquals($tus[0]->getPredicate(), "http://www.w3.org/2000/01/rdf-schema#label", "Unexpected predicate for RDFTranslationUnit.");

		$this->assertEquals($tus[1]->getTarget(), "Related to e-testing any resource", "Unexpected target for RDFTranslationUnit.");
		$this->assertEquals($tus[1]->getSubject(), "http://www.tao.lu/Ontologies/TAO.rdf#TAOObject", "Unexpected subject for RDFTranslationUnit.");
		$this->assertEquals($tus[1]->getPredicate(), "http://www.w3.org/2000/01/rdf-schema#comment", "Unexpected predicate for RDFTranslationUnit.");
	}

	public function testRDFTranslationWriting(){
		$rdfFilePath = tempnam('/tmp', self::TEMP_RDF);
		$rdfFile = new tao_helpers_translation_RDFTranslationFile('en-US', 'multiple');
		$rdfFile->setBase('http://www.tao.lu/Ontologies/TAO.rdf#');

		$writer = new tao_helpers_translation_RDFFileWriter($rdfFilePath, $rdfFile);

		$tu1 = new tao_helpers_translation_RDFTranslationUnit("This is a test");
		$tu1->setSourceLanguage('en-US');
		$tu1->setTargetLanguage('fr-FR');
		$tu1->setSubject('http://www.tao.lu#target1');
		$tu1->setPredicate('http://www.w3.org/2000/01/rdf-schema#label');
		$rdfFile->addTranslationUnit($tu1);

		$writer->write();

		$this->assertTrue(file_exists($rdfFilePath));
		unlink($rdfFilePath);
	}

	public function testRDFUtils(){
		// Instantiate a new RDF language description.
		// Yoda English is a reversed form of english, spoken a long time ago, in a far away galaxy.
		$languageDescription = tao_helpers_translation_RDFUtils::createLanguageDescription('en-YO', 'Yoda English');
		$this->assertTrue(get_class($languageDescription) == 'DOMDocument');

		// Test with XPath if the resulting DOM tree is fine.
		$xPath = new DOMXPath($languageDescription);
		$xPath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
		$xPath->registerNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');

		// Check for the Language en-YO resource.
		$result = $xPath->query("//rdf:Description[@rdf:about='http://www.tao.lu/Ontologies/TAO.rdf#Langen-YO']");
		$this->assertTrue($result->length == 1);

		// Check for the Language rdf:type.
		$result = $xPath->query("//rdf:Description/rdf:type[@rdf:resource='" . CLASS_LANGUAGES . "']");
		$this->assertTrue($result->length == 1);

		// Check for the Language rdfs:label.
		$result = $xPath->query("//rdf:Description/rdfs:label/text()");
		$this->assertTrue($result->length == 1);
		$this->assertTrue($result->item(0)->nodeValue == 'Yoda English');

		// Check for the Language rdf:value.
		$result = $xPath->query("//rdf:Description/rdf:value/text()");
		$this->assertTrue($result->length == 1);
		$this->assertTrue($result->item(0)->nodeValue == 'en-YO');

		$savePath = tempnam('/tmp', self::TEMP_RDF);
		$languageDescription->save($savePath);
		$this->assertTrue(file_exists($savePath));

		unlink($savePath);
	}

	/*
     * This test aims at testing RDF Translation Model Annotations.
     */
	public function testRDFAnnotations(){
		try{
			// - Test parsing from source code.
			// 1. Defensive tests.
			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("");
			$this->assertEquals($annotations, array());

			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("sd@eipredicate%\nblu");
			$this->assertEquals($annotations, array());

			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@fake FUBAR");
			$this->assertEquals($annotations, array());

			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@predicate ");
			$this->assertEquals($annotations, array());

			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@predicate\n@subject");
			$this->assertEquals($annotations, array());

			// 2. Other tests.
			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@predicate http://www.tao.lu/Ontologies/tao.rdf#aFragment\n@fake FUBAR");
			$this->assertEquals($annotations, array("predicate" => "http://www.tao.lu/Ontologies/tao.rdf#aFragment"));

			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@source This is a source test.");
			$this->assertEquals($annotations, array("source" => "This is a source test."));

			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@source This is a source test.\n@sourceLanguage en-US");
			$this->assertEquals($annotations, array("source" => "This is a source test.",
				"sourceLanguage" => "en-US"));

			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@source بعض النصوص في اللغة العربية");
			$this->assertEquals($annotations, array("source" => "بعض النصوص في اللغة العربية"));

			// 3. Test escaping.
			$annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations("@source lorem \\-\\- ipsum \\\\ dolomet.\n@sourceLanguage fr-CA");
			$this->assertEquals($annotations, array("source" => "lorem -- ipsum \\ dolomet.",
				"sourceLanguage" => "fr-CA"));

			$annotations = tao_helpers_translation_RDFUtils::serializeAnnotations(array("source" => "lorem -- ipsum \\ \n dolomet.",
				"sourceLanguage" => "fr-CA"));
			$this->assertEquals($annotations, "@source lorem \\-\\- ipsum \\\\ \n dolomet.\n    @sourceLanguage fr-CA");

			// - Test serialization from array.
			$annotations = tao_helpers_translation_RDFUtils::serializeAnnotations(array("source" => "This is a source test.",
				"sourceLanguage" => "en-US",
				"targetLanguage" => "fr-CA",
				"predicate" => "http://www.tao.lu/Ontologies/tao.rdf#aFragment"));
			$this->assertEquals($annotations, "@source This is a source test.\n    @sourceLanguage en-US\n    @targetLanguage fr-CA\n    @predicate http://www.tao.lu/Ontologies/tao.rdf#aFragment");


			// - Test Annotations parsing while reading with RDFFileWriter.
			$reader = new tao_helpers_translation_RDFFileReader(dirname(__FILE__) . self::FAKE_RDF_TRANSLATION_MODEL_ANNOTATIONS);
			$reader->read();
			$tf = $reader->getTranslationFile();
			$this->assertEquals($tf->getAnnotations(), array('sourceLanguage' => 'EN',
				'targetLanguage' => 'es'));
			$tus = $tf->getTranslationUnits();
			$this->assertEquals($tus[0]->getSourceLanguage(), 'EN');
			$this->assertEquals($tus[0]->getTargetLanguage(), 'es');
			$this->assertEquals($tus[0]->getSource(), 'TAO Object');
			$this->assertEquals($tus[0]->getTarget(), 'TAO objeto');
			$this->assertEquals($tus[0]->getAnnotation('sourceLanguage'), array('name' => 'sourceLanguage', 'value' => 'EN'));
			$this->assertEquals($tus[0]->getAnnotation('targetLanguage'), array('name' => 'targetLanguage', 'value' => 'es'));
			$this->assertEquals($tus[10]->getTarget(), 'Función de usuario de flujo de trabajo: el papel asignado por defecto a todos los usuarios backend, no eliminable');
		}
		catch (tao_helpers_translation_TranslationException $e){
			$this->assertFalse(true, "No TranslationException should be thrown in testRDFAnnotations test.");
		}
	}

	public function testPOAnnotationsReading(){
		$string  = "# This is a comment.\n";
		$string .= "#, flag1 composed-flag flag2";
		$annotations = tao_helpers_translation_POUtils::unserializeAnnotations($string);
		$this->assertEquals($annotations, array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => 'This is a comment.',
			tao_helpers_translation_POTranslationUnit::FLAGS => 'flag1 composed-flag flag2'));

		$string  = "# The first line of my comment continues...\n";
		$string .= "# At the second line.\n";
		$string .= "#. Please do not touch this!\n";
		$string .= "#| msgctxt A previous testing context.\n";
		$string .= "#|  msgid previous-untranslated-string-singular\n";
		$string .= "#| msgid_plural previous-untranslated-string-plural\n";
		$annotations = tao_helpers_translation_POUtils::unserializeAnnotations($string);
		$this->assertEquals($annotations, array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => "The first line of my comment continues...\nAt the second line.",
			tao_helpers_translation_POTranslationUnit::EXTRACTED_COMMENTS => "Please do not touch this!",
			tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGCTXT => "A previous testing context.",
			tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID => "previous-untranslated-string-singular",
			tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID_PLURAL => "previous-untranslated-string-plural"));

		$string  = "# هذا تعليق\n";
		$string .= "# مع خطوط متعددة في الداخل.\n";
		$string .= "#. لا تغير من فضلك!\n";
		$string .= "#| msgctxt السابقة السياق.";
		$annotations = tao_helpers_translation_POUtils::unserializeAnnotations($string);
		$this->assertEquals($annotations, array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => "هذا تعليق\nمع خطوط متعددة في الداخل.",
			tao_helpers_translation_POTranslationUnit::EXTRACTED_COMMENTS => "لا تغير من فضلك!",
			tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGCTXT => "السابقة السياق."));

		$string  = "^ This should not w#ork but the next...\n";
		$string .= "#, flag-read";
		$annotations = tao_helpers_translation_POUtils::unserializeAnnotations($string);
		$this->assertEquals($annotations, array(tao_helpers_translation_POTranslationUnit::FLAGS => 'flag-read'));

		$string = "";
		$annotations = tao_helpers_translation_POUtils::unserializeAnnotations($string);
		$this->assertEquals($annotations, array());

		$reader = new tao_helpers_translation_POFileReader(dirname(__FILE__) . self::ANNOTATIONS_PO);
		$reader->read();
		$tf = $reader->getTranslationFile();
		$tus = $tf->getTranslationUnits();
		$this->assertEquals(count($tus), 6);
		$this->assertEquals($tus[0]->getAnnotations(), array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => 'This is a comment',
			'sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage()));
		$this->assertEquals($tus[1]->getAnnotations(), array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => 'This is another comment',
			tao_helpers_translation_POTranslationUnit::FLAGS => 'flag1 composed-flag flag2 tao-public',
			'sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage()));
		$this->assertEquals($tus[2]->getAnnotations(), array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => "This is a multiline...\ncomment.",
			'sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage()));
		$this->assertEquals($tus[3]->getAnnotations(), array('sourceLanguage' => tao_helpers_translation_Utils::getDefaultLanguage(),
			'targetLanguage' => tao_helpers_translation_Utils::getDefaultLanguage()));

		// Test flag related interface on POTranslationUnit & POTranslationFile.
		$this->assertTrue($tus[5]->hasFlag('flag4'));
		$this->assertEquals($tus[5]->getFlags(), array('flag4'));
		$tus[5]->addFlag('new-flag');
		$this->assertTrue($tus[5]->hasFlag('new-flag'));
		$this->assertEquals($tus[5]->getFlags(), array('flag4', 'new-flag'));
		$tus[5]->addFlag('new-flag');
		$this->assertEquals($tus[5]->getFlags(), array('flag4', 'new-flag'));
		$tus[5]->addFlag('flag5');
		$this->assertEquals($tus[5]->getAnnotation(tao_helpers_translation_POTranslationUnit::FLAGS),
			array('name' => tao_helpers_translation_POTranslationUnit::FLAGS, 'value' => 'flag4 new-flag flag5'));
		$tus[5]->removeFlag('new-flag');
		$this->assertEquals($tus[5]->getFlags(), array('flag4', 'flag5'));

		$flagTus = $tf->getByFlag('composed-flag');
		$this->assertEquals(count($flagTus), 2);
		$this->assertEquals($flagTus[0]->getSource(), "Thïs téxt cöntàin$ wéîRd chárâctêrS beçÁuse öf I18N");
		$this->assertEquals($flagTus[1]->getSource(), "This one contains the same flag as the second one");

		$flagTus = $tf->getByFlags(array('composed-flag', 'flag2'));
		$this->assertEquals(count($flagTus), 2);
		$this->assertEquals($flagTus[0]->getSource(), "Thïs téxt cöntàin$ wéîRd chárâctêrS beçÁuse öf I18N");
		$this->assertEquals($flagTus[1]->getSource(), "This one contains the same flag as the second one");

		// Reload the file.
		// We will check if when the file is written again, we get the same result.
		// In other words, we check idempotency after read/write.
		// We will compare TranslationFiles $tf1 & $tf2.
		$reader->read();
		$tf1 = $reader->getTranslationFile();

		// We write $tf1.
		$path = tempnam('/tmp', self::TEMP_PO);
		$writer = new tao_helpers_translation_POFileWriter($path, $tf1);
		$writer->write();

		// We read $tf2 to be compared with $tf1
		$reader->setFilePath($path);
		$reader->read();
		$tf2 = $reader->getTranslationFile();

		$this->assertEquals($tf1->count(), 6);
		$this->assertEquals($tf2->count(), 6);

		$tus1 = $tf1->getTranslationUnits();
		$tus2 = $tf2->getTranslationUnits();

		$this->assertEquals($tus1[0]->getAnnotations(), $tus2[0]->getAnnotations());
		$this->assertEquals($tus1[1]->getAnnotations(), $tus2[1]->getAnnotations());
		$this->assertEquals($tus1[2]->getAnnotations(), $tus2[2]->getAnnotations());
		$this->assertEquals($tus1[3]->getAnnotations(), $tus2[3]->getAnnotations());
		$this->assertEquals($tus1[4]->getAnnotations(), $tus2[4]->getAnnotations());
		$this->assertEquals($tus1[5]->getAnnotations(), $tus2[5]->getAnnotations());

		unlink($path);
	}

	public function testPOAnnotationsWriting(){
		// Test flag utilities.
		$comment = '';
		$this->assertEquals(tao_helpers_translation_POUtils::addFlag($comment, 'tao-public'), 'tao-public');

		$comment = 'no-error test-flag';
		$this->assertEquals(tao_helpers_translation_POUtils::addFlag($comment, 'tao-public'), 'no-error test-flag tao-public');

		$comment = 'foo bar code';
		$this->assertEquals(tao_helpers_translation_POUtils::addFlag($comment, 'bar '), 'foo bar code');


		// Test PO comments serialization.
		$annotations = array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => 'A single line translator comment.');
		$comment = '# A single line translator comment.';
		$this->assertEquals(tao_helpers_translation_POUtils::serializeAnnotations($annotations), $comment);

		$annotations = array(tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS => "A multi line translator comment...\nWith a second line.",
			tao_helpers_translation_POTranslationUnit::EXTRACTED_COMMENTS => "An extracted comment.",
			tao_helpers_translation_POTranslationUnit::FLAGS => "tao-public foo-bar-code php-format");
		$comment = "# A multi line translator comment...\n# With a second line.\n#. An extracted comment.\n#, tao-public foo-bar-code php-format";
		$this->assertEquals(tao_helpers_translation_POUtils::serializeAnnotations($annotations), $comment);

		$annotations = array(tao_helpers_translation_POTranslationUnit::FLAGS => "tao-public");
		$comment = "#, tao-public";
		$this->assertEquals(tao_helpers_translation_POUtils::serializeAnnotations($annotations), $comment);

	}
}
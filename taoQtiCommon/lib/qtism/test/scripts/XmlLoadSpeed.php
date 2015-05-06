<?php

use qtism\data\storage\xml\XmlDocument;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

function testAssessmentItems(array $files, $validate = false) {
	
	$loaded = 0;
	$totalSpent = 0;
	foreach ($files as $f) {
		$start = microtime();
		
		$itemDoc = new XmlDocument();
		$itemDoc->load($f, $validate);

		$end = microtime();
		$spent = spentTime($start, $end);
		$totalSpent += $spent;
		
		output("Item '" . pathinfo($f, PATHINFO_BASENAME) . "' loaded in " . sprintf("%.8f", $spent) . " seconds.");
		
		$outcomeDeclarationCount = count($itemDoc->getDocumentComponent()->getComponentsByClassName('outcomeDeclaration'));
		$responseDeclarationCount = count($itemDoc->getDocumentComponent()->getComponentsByClassName('responseDeclaration'));
		
		outputDescription("${responseDeclarationCount} resonseDeclaration(s), ${outcomeDeclarationCount} outcomeDeclaration(s)");
		outputDescription("Memory usage is " . (memory_get_usage()  / pow(1024, 2)) . " MB");
		output('');
		
		$loaded++;
	}
	
	outputAverage($totalSpent / $loaded);
}

function testAssessmentTests(array $files, $validate = false) {
	
	$loaded = 0;
	$totalSpent = 0;
	
	foreach ($files as $f) {
		$start = microtime();
		$testDoc = new XmlDocument();
		$testDoc->load($f, $validate);
		
		$end = microtime();
		$spent = spentTime($start, $end);
		$totalSpent += $spent;
		
		output("Test '" . pathinfo($f, PATHINFO_BASENAME) . "' loaded in " . sprintf("%.8f", $spent) . " seconds.");
		
		$partCount = count($testDoc->getDocumentComponent()->getComponentsByClassName('testPart'));
		$sectionCount = count($testDoc->getDocumentComponent()->getComponentsByClassName('assessmentSection'));
		$itemCount = count($testDoc->getDocumentComponent()->getComponentsByClassName('assessmentItemRef'));
		
		outputDescription("${partCount} testPart(s), ${sectionCount} assessmentSection(s), ${itemCount} assessmentItemRef(s)");
		outputDescription("Memory usage is " . (memory_get_usage()  / pow(1024, 2)) . " MB");
		
		output('');
		
		$loaded++;
	}
	
	outputAverage($totalSpent / $loaded);
}

function outputTitle($msg) {
	output('');
	output(str_repeat('+', strlen($msg)));
	output($msg);
	output(str_repeat('+', strlen($msg)));
}

function outputAverage($avg) {
	output(sprintf("--> Average loading time is %.8f seconds.", $avg));
}

function outputDescription($msg) {
	output(" + ${msg}");
}

function output($msg) {
	echo "${msg}\n";
}

function spentTime($start, $end) {
	$startTime = explode(' ', $start);
	$endTime = explode(' ', $end);
	$time = ($endTime[0] + $endTime[1]) - ($startTime[0] + $startTime[1]);
	return $time;
}

define('SAMPLES_DIR', dirname(__FILE__) . '/../samples/');

$items = array(
	SAMPLES_DIR . 'ims/items/2_0/adaptive_template.xml',
	SAMPLES_DIR . 'ims/items/2_0/adaptive.xml',
	SAMPLES_DIR . 'ims/items/2_0/associate.xml',
	SAMPLES_DIR . 'ims/items/2_0/choice_multiple.xml',
	SAMPLES_DIR . 'ims/items/2_0/choice.xml',
	SAMPLES_DIR . 'ims/items/2_0/drawing.xml',
	SAMPLES_DIR . 'ims/items/2_0/extended_text.xml',
	SAMPLES_DIR . 'ims/items/2_0/feedback.xml',
	SAMPLES_DIR . 'ims/items/2_0/gap_match.xml',
	SAMPLES_DIR . 'ims/items/2_0/graphic_associate.xml',
	SAMPLES_DIR . 'ims/items/2_0/graphic_gap_match.xml',
	SAMPLES_DIR . 'ims/items/2_0/graphic_order.xml',
	SAMPLES_DIR . 'ims/items/2_0/hint.xml',
	SAMPLES_DIR . 'ims/items/2_0/hotspot.xml',
	SAMPLES_DIR . 'ims/items/2_0/inline_choice.xml',
	SAMPLES_DIR . 'ims/items/2_0/likert.xml',
	SAMPLES_DIR . 'ims/items/2_0/match.xml',
	SAMPLES_DIR . 'ims/items/2_0/math.xml',
	SAMPLES_DIR . 'ims/items/2_0/nested_object.xml',
	SAMPLES_DIR . 'ims/items/2_0/order_partial_scoring.xml',
	SAMPLES_DIR . 'ims/items/2_0/order.xml',
	SAMPLES_DIR . 'ims/items/2_0/orkney1.xml',
	//SAMPLES_DIR . 'ims/items/2_0/position_object.xml', Note: contains invalid identifiers ;)
	SAMPLES_DIR . 'ims/items/2_0/slider.xml',
	SAMPLES_DIR . 'ims/items/2_0/template_image.xml',
	SAMPLES_DIR . 'ims/items/2_0/template.xml',
	SAMPLES_DIR . 'ims/items/2_0/text_entry.xml',
	SAMPLES_DIR . 'ims/items/2_0/upload_composite.xml',
	SAMPLES_DIR . 'ims/items/2_0/upload.xml'
);

outputTitle("Loading QTI-XML Items 2.0 samples:");
testAssessmentItems($items);

$items = array(
		SAMPLES_DIR . 'ims/items/2_1/associate.xml',
		SAMPLES_DIR . 'ims/items/2_1/choice_multiple.xml',
		SAMPLES_DIR . 'ims/items/2_1/choice.xml',
		SAMPLES_DIR . 'ims/items/2_1/extended_text.xml',
		SAMPLES_DIR . 'ims/items/2_1/gap_match.xml',
		SAMPLES_DIR . 'ims/items/2_1/graphic_associate.xml',
		SAMPLES_DIR . 'ims/items/2_1/graphic_gap_match.xml',
		SAMPLES_DIR . 'ims/items/2_1/hotspot.xml',
		SAMPLES_DIR . 'ims/items/2_1/inline_choice.xml',
		SAMPLES_DIR . 'ims/items/2_1/match.xml',
		SAMPLES_DIR . 'ims/items/2_1/order.xml',
		//SAMPLES_DIR . 'ims/items/2_1/position_object.xml', Note: contains invalid identifiers ;)
		SAMPLES_DIR . 'ims/items/2_1/slider.xml',
		SAMPLES_DIR . 'ims/items/2_1/text_entry.xml'
);

outputTitle("Loading QTI-XML Items 2.1 samples:");
testAssessmentItems($items);

$tests = array(
	SAMPLES_DIR . 'ims/tests/arbitrary_collections_of_item_outcomes/arbitrary_collections_of_item_outcomes.xml',
	SAMPLES_DIR . 'ims/tests/arbitrary_weighting_of_item_outcomes/arbitrary_weighting_of_item_outcomes.xml',
	SAMPLES_DIR . 'ims/tests/basic_statistics_as_outcomes/basic_statistics_as_outcomes.xml',
	SAMPLES_DIR . 'ims/tests/branching_based_on_the_response_to_an_assessmentitem/branching_based_on_the_response_to_an_assessmentitem.xml',
	SAMPLES_DIR . 'ims/tests/categories_of_item/categories_of_item.xml',
	SAMPLES_DIR . 'ims/tests/controlling_item_feedback_in_relation_to_the_test/controlling_item_feedback_in_relation_to_the_test.xml',
	SAMPLES_DIR . 'ims/tests/controlling_the_duration_of_an_item_attempt/controlling_the_duration_of_an_item_attempt.xml',
	SAMPLES_DIR . 'ims/tests/early_termination_of_test_based_on_accumulated_item_outcomes/early_termination_of_test_based_on_accumulated_item_outcomes.xml',
	SAMPLES_DIR . 'ims/tests/feedback_examples_test/feedback_examples_test.xml',
	SAMPLES_DIR . 'ims/tests/golden_required_items_and_sections/golden_required_items_and_sections.xml',
	SAMPLES_DIR . 'ims/tests/interaction_mix_sachsen/interaction_mix_sachsen.xml',
	SAMPLES_DIR . 'ims/tests/items_arranged_into_sections_within_tests/items_arranged_into_sections_within_tests.xml',
	SAMPLES_DIR . 'ims/tests/mapping_item_outcomes_prior_to_aggregation/mapping_item_outcomes_prior_to_aggregation.xml',
	SAMPLES_DIR . 'ims/tests/randomizing_the_order_of_items_and_sections/randomizing_the_order_of_items_and_sections.xml',
	SAMPLES_DIR . 'ims/tests/sets_of_items_with_leading_material/sets_of_items_with_leading_material.xml',
	SAMPLES_DIR . 'ims/tests/simple_feedback_test/simple_feedback_test.xml',
	SAMPLES_DIR . 'ims/tests/specifiying_the_number_of_allowed_attempts/specifiying_the_number_of_allowed_attempts.xml',
	SAMPLES_DIR . 'custom/very_long_assessmenttest.xml'
);

outputTitle("Loading QTI-XML Tests 2.1 samples:");
testAssessmentTests($tests);
<?php
use qtism\runtime\storage\common\AbstractStorage;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\data\storage\xml\XmlCompactDocument;
use qtism\data\storage\xml\XmlDocument;
use qtism\common\datatypes\Identifier;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\data\AssessmentTest;
use qtism\runtime\tests\SessionManager;
use qtism\runtime\storage\binary\BinaryAssessmentTestSeeker;
use qtism\runtime\storage\binary\TemporaryQtiBinaryStorage;
use qtism\data\storage\php\PhpDocument;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

date_default_timezone_set('UTC');

function loadTestDefinition(array &$average = null) {
    $start = microtime();
    
    $phpDoc = new PhpDocument();
    $phpDoc->load(dirname(__FILE__) . '/../../test/samples/custom/php/linear_25_items.php');
    
    if (is_null($average) === false) {
        spentTime($start, microtime(), $average);
    }
    
    return $phpDoc->getDocumentComponent();
}

function createFactory() {
    return new SessionManager();
}

function createStorage(SessionManager $factory, AssessmentTest $test) {
    return new TemporaryQtiBinaryStorage($factory, new BinaryAssessmentTestSeeker($test));
}

function spentTime($start, $end, array &$registration = null) {
    $startTime = explode(' ', $start);
    $endTime = explode(' ', $end);
    $time = ($endTime[0] + $endTime[1]) - ($startTime[0] + $startTime[1]);
    
    if (!is_null($registration)) {
        $registration[] = $time;
    }
    
    return $time;
}

function attempt(AssessmentTestSession $session, $identifier, array &$average = null) {
    $start = microtime();

    $session->beginAttempt();
    $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier($identifier)))));

    if (is_null($average) === false) {
        spentTime($start, microtime(), $average);
    }
}

function retrieve(AbstractStorage $storage, AssessmentTest $test, $sessionId, array &$average = null) {
    $start = microtime();

    $session = $storage->retrieve($test, $sessionId);

    if (is_null($average) === false) {
        spentTime($start, microtime(), $average);
    }

    return $session;
}

function persist(AbstractStorage $storage, AssessmentTestSession $session, &$average = null) {
    $start = microtime();

    $storage->persist($session);

    if (is_null($average) === false) {
        spentTime($start, microtime(), $average);
    }
}

function moveNext(AssessmentTestSession $session, array &$average) {
    $start = microtime();

    $session->moveNext();

    if (is_null($average) === false) {
        spentTime($start, microtime(), $average);
    }
}

function neighbourhood(AssessmentTestSession $session, array &$average = null) {
    $start = microtime();
    $neighbourhood = $session->getPossibleJumps();

    if (is_null($average) === false) {
        spentTime($start, microtime(), $average);
    }
}

$averageAttempt = array();
$effectiveAverageAttempt = array();
$averageRetrieve = array();
$averagePersist = array();
$averageNext = array();
$averageLoad = array();
$averageNeighbourhood = array();

// Beginning of the session + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = $storage->instantiate($test);
$sessionId = $session->getSessionId();
$session->beginTestSession();
$storage->persist($session);
unset($session);
unset($storage);
unset($test);

$end = microtime();
echo "Beginning of the session + persistance (" . spentTime($start, $end) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceA', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 1 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceB', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 2 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceC', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 3 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceD', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 4 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceE', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 5 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceF', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 6 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceG', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 7 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceH', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 8 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceI', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 9 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceJ', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 10 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceK', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 11 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceL', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 12 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceM', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 13 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceN', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 14 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceO', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 15 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceP', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 16 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceQ', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 17 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceR', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 18 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceS', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 19 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceT', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 20 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceU', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 21 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceV', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 22 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceW', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 23 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceX', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 24 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n";

// Retrieving session + make an attempt + persistance.
$start = microtime();

$test = loadTestDefinition($averageLoad);
$storage = createStorage(createFactory(), $test);
$session = retrieve($storage, $test, $sessionId, $averageRetrieve);
neighbourhood($session, $averageNeighbourhood);
attempt($session, 'ChoiceY', $effectiveAverageAttempt);
moveNext($session, $averageNext);
persist($storage, $session, $averagePersist);
$end = microtime();
unset($session);
unset($storage);
unset($test);

echo "Retrieving session + attempt 25 + persistance (" . spentTime($start, $end, $averageAttempt) . ")\n\n";

echo "Average attempt time = " . (array_sum($averageAttempt) / count($averageAttempt)) . "\n";
echo "Effective average attempt time = " . (array_sum($effectiveAverageAttempt) / count($effectiveAverageAttempt)) . "\n";
echo "Retrieve average time = " . (array_sum($averageRetrieve) / count($averageRetrieve)) . "\n";
echo "Persist average time = " . (array_sum($averagePersist) / count($averagePersist)) . "\n";
echo "MoveNext average time = " . (array_sum($averageNext) / count($averageNext)) . "\n";
echo "Load average time = " . (array_sum($averageLoad) / count($averageLoad)) . "\n";
echo "Neighbourhood average time = " . (array_sum($averageNeighbourhood) / count($averageNeighbourhood)) . "\n";
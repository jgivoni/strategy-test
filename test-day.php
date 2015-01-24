<?php

/**
 * This script will simulate different test strategies and evaluate the result
 * The main result to evaluate will be total revenue 
 */
require_once 'pl.php';

require_once 'DayTest.php';
require_once 'DayTestConditions.php';

$conditions = new DayTestConditions();
$conditions->experiences = [
	'A' => new Experience(0.03, 0.01, 1),
	'B' => new Experience(0.031, 0.01, 1.1),
];
$weightingRules = [
    'A' => 1,
    'B' => 1,
];
$test = new DayTest($conditions);
$results = $test->getResults($weightingRules, 1000000);

echo pl('RPC', $results->getRpc(), $conditions->getBaselineRpc());
echo pl('CR', $results->getCr(), $conditions->getBaselineCr());

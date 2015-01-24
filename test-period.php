<?php

/**
 * This script will simulate different test strategies and evaluate the result
 * The main result to evaluate will be total revenue 
 */

require_once 'strategies/AbStrategy.php';

require_once 'PeriodTest.php';
require_once 'PeriodTestConditions.php';

$conditions = new PeriodTestConditions();
$conditions->experiences = [
	'A' => new Experience(0.03, 0.01, 1),
	'B' => new Experience(0.031, 0.01, 1.1),
];
$conditions->strategy = new AbStrategy();
$conditions->visitsPerDay = 100;
$test = new PeriodTest($conditions);
$results = $test->getResults(30);

echo "Period revenue: {$results->revenue}\n";

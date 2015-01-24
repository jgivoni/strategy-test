<?php

/**
 * This script will simulate different test strategies and evaluate the result
 * The main result to evaluate will be total revenue 
 */

require 'strategies/BanditCrStrategy.php';
require 'strategies/AbStrategy.php';

require 'StrategyTest.php';
require 'StrategyTestConditions.php';

$conditions = new StrategyTestConditions();
$conditions->experiences = [
	'A' => new Experience(0.03, 1),
	'B' => new Experience(0.031, 1.1),
];
$conditions->daysPerPeriod = 30;
$conditions->visitsPerDay = 100;
$test = new StrategyTest($conditions);
$iterations = 1;
$results = $test->getResults(new AbStrategy(), $iterations);

echo $results->revenue;

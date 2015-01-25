<?php

/**
 * This script will simulate different test strategies and evaluate the result
 * The main result to evaluate will be total revenue 
 */
require_once 'pl.php';

require_once 'strategies/AbStrategy.php';
require_once 'strategies/MyStrategy.php';
require_once 'strategies/BanditCrStrategy.php';
require_once 'StrategyTest.php';
require_once 'StrategyTestConditions.php';

//$strategy = new AbStrategy();
$strategy = new MyStrategy();
//$strategy = new BanditCrStrategy();
$conditions = new StrategyTestConditions();
$conditions->experiences = [
    'A' => new Experience(0.03, 0.01, 1),
    'B' => new Experience(0.031, 0.02, 1.1),
];
$conditions->daysPerPeriod = 30;
$conditions->visitsPerDay = 1000;
$iterations = 10;
$test = new StrategyTest($conditions);
$results = $test->getResults($strategy, $iterations);

echo "\n";
echo pl('Avg. total revenue', $results->getAvgRevenue(), $conditions->getBaselineRevenue(), $conditions->getBestRevenue());

<?php

/**
 * This script will simulate different test strategies and evaluate the result
 * The main result to evaluate will be total revenue 
 */
require_once 'pl.php';

require_once 'strategies/AbStrategy.php';
require_once 'strategies/MyCrStrategy.php';
require_once 'strategies/BanditCrStrategy.php';
require_once 'StrategyTest.php';
require_once 'StrategyTestConditions.php';

//$strategy = new AbStrategy();
$strategy = new MyCrStrategy();
//$strategy = new BanditCrStrategy();
$conditions = new StrategyTestConditions('test1');
$conditions->experiences = [
    'A' => new Experience(0.030, 0.01, 1),
    'B' => new Experience(0.033, 0.01, 1),
    'C' => new Experience(0.039, 0.01, 1),
    'D' => new Experience(0.042, 0.01, 1),
    'E' => new Experience(0.045, 0.01, 1),
];
$conditions->daysPerPeriod = 20;
$conditions->visitsPerDay = 100;
$iterations = 100;
unlink("/var/log/strategy-test/test1.txt");
var_dump($conditions);
$test = new StrategyTest($conditions);
$results = $test->getResults($strategy, $iterations);

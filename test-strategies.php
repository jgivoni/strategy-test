<?php

/**
 * This script will compare different weighting strategies
 */
require_once 'pl.php';

require_once 'strategies/AbStrategy.php';
require_once 'strategies/MyCrStrategy.php';
require_once 'strategies/BanditCrStrategy.php';
require_once 'strategies/PoissonCrStrategy.php';
require_once 'strategies/BanditRpcStrategy.php';
require_once 'StrategiesTest.php';
require_once 'StrategiesTestConditions.php';
require_once 'Experience2.php';

$conditions = new StrategiesTestConditions('test1');
$conditions->experiences = [
    'A' => new Experience2(0.030, 0.010, 0.25, 0.1),
//    'B' => new Experience2(0.035, 0.010, 0.25, 0.1),
//    'C' => new Experience2(0.035, 0.005, 0.25, 0.1),
//    'D' => new Experience(0.042, 0.01, 1.25),
//    'E' => new Experience(0.045, 0.01, 1.05),
];
$conditions->daysPerPeriod = 1;
$conditions->visitsPerDay = 10000;
$conditions->iterationsPerStrategy = 500;
$strategies = [
//    new MyCrStrategy,
//    new PoissonCrStrategy,
//    new AbStrategy,
//    new BanditCrStrategy,
    new BanditRpcStrategy(33, 0, 4.05, 4.131818182),
//    new BanditRpcStrategy(50, 0, 6.97, 14.14),
//    new BanditRpcStrategy(75, 0, 6.8, 24.10909091),
//    new BanditRpcStrategy(30, 0, 3, 50),
];
if (file_exists("/var/log/strategy-test/test1.txt")) {
    unlink("/var/log/strategy-test/test1.txt");
}
if (file_exists("/var/log/strategy-test/test1.csv")) {
    unlink("/var/log/strategy-test/test1.csv");
}
var_dump($conditions);
$test = new StrategiesTest($conditions);
$results = $test->getResults($strategies);

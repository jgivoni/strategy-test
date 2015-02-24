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

$conditions = new StrategiesTestConditions('US,84006');
$conditions->experiences = [
    'A' => new Experience(0.3241, 0.01, 16.68),
    'B' => new Experience(0.3422, 0.01, 17.62),
    'C' => new Experience(0.2966, 0.01, 17.45),
//    'D' => new Experience(0.042, 0.01, 1.25),
//    'E' => new Experience(0.045, 0.01, 1.05),
];
$conditions->daysPerPeriod = 60;
$conditions->visitsPerDay = 50;
$conditions->iterationsPerStrategy = 500;
$strategies = [
//    new MyCrStrategy,
//    new PoissonCrStrategy,
//    new AbStrategy,
//    new BanditCrStrategy,
    new BanditRpcStrategy(50, 0, 6.97, 14.14),
    new BanditRpcStrategy(100, 0, 8.93, 45.18),
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

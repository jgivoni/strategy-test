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
require_once 'strategies/BanditRpc2Strategy.php';
require_once 'strategies/BanditRpc3Strategy.php';
require_once 'StrategiesTest.php';
require_once 'StrategiesTestConditions.php';
require_once 'Experience2.php';

$conditions = new StrategiesTestConditions('test1');
$conditions->experiences = [
    'A' => new Experience2(0.030, 0.010, 0.25, 0.1),
    'B' => new Experience2(0.035, 0.010, 0.25, 0.1),
    'C' => new Experience2(0.035, 0.005, 0.25, 0.1),
//    'D' => new Experience(0.042, 0.01, 1.25),
//    'E' => new Experience(0.045, 0.01, 1.05),
];
$conditions->daysPerPeriod = 90;
$conditions->visitsPerDay = 100;
$conditions->iterationsPerStrategy = 500;
$strategies = [
//    new MyCrStrategy,
//    new PoissonCrStrategy,
//    new AbStrategy,
//    new BanditCrStrategy,
    new BanditRpcStrategy(33, 0, 1, 1),
    new BanditRpc2Strategy(33, 1, 1),
    new BanditRpc3Strategy(33, 1, 1),
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

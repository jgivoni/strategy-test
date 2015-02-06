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

$conditions = new StrategiesTestConditions('test1');
$conditions->experiences = [
    'A' => new Experience(0.030, 0.01, 1),
    'B' => new Experience(0.033, 0.01, 1.1),
    'C' => new Experience(0.039, 0.01, 1.2),
    'D' => new Experience(0.042, 0.01, 1.25),
    'E' => new Experience(0.045, 0.01, 1.05),
];
$conditions->daysPerPeriod = 60;
$conditions->visitsPerDay = 250;
$conditions->iterationsPerStrategy = 200;
$strategies = [
    new MyCrStrategy,
    new PoissonCrStrategy,
    new BanditCrStrategy,
    new BanditRpcStrategy,
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

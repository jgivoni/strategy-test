<?php

/**
 * This script will compare different weighting strategies
 */
require_once 'pl.php';

require_once 'strategies/AbStrategy.php';
require_once 'strategies/MyCrStrategy.php';
require_once 'strategies/BanditCrStrategy.php';
require_once 'StrategiesTest.php';
require_once 'StrategiesTestConditions.php';

//$strategy = new AbStrategy();
//$strategy = new MyCrStrategy();
$conditions = new StrategiesTestConditions('test1');
$conditions->experiences = [
    'A' => new Experience(0.030, 0.01, 1),
    'B' => new Experience(0.033, 0.01, 1),
    'C' => new Experience(0.039, 0.01, 1),
    'D' => new Experience(0.042, 0.01, 1),
    'E' => new Experience(0.045, 0.01, 1),
];
$conditions->daysPerPeriod = 30;
$conditions->visitsPerDay = 100;
$conditions->iterationsPerStrategy = 100;
$strategies = [
    new AbStrategy,
    new MyCrStrategy,
    new BanditCrStrategy,
];
if (file_exists("/var/log/strategy-test/test1.txt")) {
    unlink("/var/log/strategy-test/test1.txt");
}
var_dump($conditions);
$test = new StrategiesTest($conditions);
$results = $test->getResults($strategies);

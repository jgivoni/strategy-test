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
require_once 'strategies/BanditRpcBulkStrategy.php';
require_once 'strategies/BanditEpcBulkStrategy.php';
require_once 'strategies/BanditEpcVictorBulkStrategy.php';
require_once 'StrategiesTest.php';
require_once 'StrategiesTestConditions.php';
require_once 'Experience2.php';

$conditions = new StrategiesTestConditions('test1');
$conditions->experiences = [
	//3
//	'A' => new Experience(0.3241, 0.010, 16.68),
//	'B' => new Experience(0.3422, 0.010, 17.62),
//	'C' => new Experience(0.2966, 0.010, 17.45),
	//4
//    'A' => new Experience2(0.030, 0.10, 0.25, 0.25),
//    'B' => new Experience2(0.035, 0.10, 0.25, 0.25),
//    'C' => new Experience2(0.035, 0.02, 0.25, 0.25),
    'A' => new Experience2(0.025, 0.10, 0.25, 0.25),
    'B' => new Experience2(0.030, 0.10, 0.25, 0.25),
    'C' => new Experience2(0.030, 0.02, 0.25, 0.25),

//    'D' => new Experience(0.042, 0.01, 1.25),
//    'E' => new Experience(0.045, 0.01, 1.05),
	
];
$conditions->daysPerPeriod = 90;
$conditions->visitsPerDay = 250;
$conditions->iterationsPerStrategy = 1000;
$strategies = [
//    new MyCrStrategy,
//    new PoissonCrStrategy,
//    new AbStrategy,
//    new BanditCrStrategy,
//    new BanditRpcStrategy(33, 0, 1, 1),
//    new BanditRpc2Strategy(33, 1, 1),
//    new BanditRpc3Strategy(33, 1, 1),
    new BanditEpcBulkStrategy(),
    new BanditEpcVictorBulkStrategy(), // Continuous reward bandit, v2
//    new BanditRpcBulkStrategy(75,1,1), // What we use currently in production
//    new BanditRpcBulkStrategy(2),
//    new BanditRpcBulkStrategy(10),
    new BanditRpcBulkStrategy(25), // Best so far (uses priors, alpha+beta)
//    new BanditRpcBulkStrategy(25,3,100), // Experimenting with priors
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

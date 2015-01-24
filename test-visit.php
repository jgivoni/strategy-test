<?php

/**
 * This script will simulate different test strategies and evaluate the result
 * The main result to evaluate will be total revenue 
 */

require_once 'VisitTest.php';
require_once 'VisitTestConditions.php';

$conditions = new VisitTestConditions();
$conditions->experiences = [
    'A' => new Experience(0.03, 0.01, 1),
    'B' => new Experience(0.031, 0.01, 1.1),
];
$conditions->weightingRules = [
    'A' => 1,
    'B' => 1,
];
$test = new VisitTest($conditions);
$results = $test->getResults();

echo "Visit revenue: {$results->revenue}\n";

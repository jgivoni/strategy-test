<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'strategies/Strategy.php';

class BanditCrStrategy extends Strategy
{
    public function getWeights($visits, $conversions, $xSales, $revenue)
    {
        $rCommand = "library(bandit); " . 
            "trials=c(" . implode(',', $visits) . "); " .
            "successes=c(" . implode(',', $conversions) . "); " .
            "best_binomial_bandit(successes,trials)";
        $output = [];
        exec("R -e '{$rCommand}' --slave", $output);
        $weights = array_slice(explode(' ', $output[0]), 1);
        return array_map(function($weight) {
            return (float) $weight * 10000;
        }, $weights);
    }

}

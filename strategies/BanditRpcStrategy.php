<?php

require_once 'strategies/Strategy.php';

class BanditRpcStrategy extends Strategy
{
    public $alpha = 1.5;
    public $beta = 50;
    
    public $name = 'Binomial bandit on RPC';
    
    public $divisor = 50;
    public $addSuccessesToTrials;
    
    public function __construct($divisor = null, $addSuccessesToTrials = true)
    {
        if (isset($divisor)) {
            $this->divisor = $divisor;
        }
        $this->addSuccessesToTrials = $addSuccessesToTrials;
    }
    
    public function getWeights($visits, $conversions, $xSales, $revenue)
    {
        $revenueTransformed = array_map(function($rev){
            return number_format((float)$rev / (float)$this->divisor, 2);
        }, $revenue);
        if ($this->addSuccessesToTrials) {
            array_walk($visits, function(&$value, $key) use ($revenueTransformed) {
                $value += ceil($revenueTransformed[$key]);
            });
        }
        $rCommand = "library(bandit); " . 
            "trials=c(" . implode(',', $visits) . "); " .
            "successes=c(" . implode(',', $revenueTransformed) . "); " .
            "best_binomial_bandit(successes,trials,$this->alpha,$this->beta)";
        $r = new RAdapter();
        $weights = $r->execute($rCommand)[0];
        if (round(array_sum($weights)*10) != 10) {
            $rCommand = "library(bandit); " . 
                "trials=c(" . implode(',', $visits) . "); " .
                "successes=c(" . implode(',', $revenueTransformed) . "); " .
                "best_poisson_bandit(successes,trials)";
            $r = new RAdapter();
            $weights = $r->execute($rCommand)[0];
        }
        return array_map(function($weight) {
            return (float) $weight * 10000;
        }, $weights);
    }

    public function displayConfig()
    {
        return "$this->divisor, " . (int) $this->addSuccessesToTrials . ", $this->alpha, $this->beta";
    }

}

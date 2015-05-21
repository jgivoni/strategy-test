<?php

require_once 'strategies/Strategy.php';

class BanditRpcStrategy extends Strategy
{
    public $alpha;
    public $beta;
    
    public $name = 'Binomial bandit on RPC';
    
    public $divisor = 50;
    public $addSuccessesToTrials;
    
    public function __construct($divisor = null, $addSuccessesToTrials = true, $alpha = 1.5, $beta = 50)
    {
        if (isset($divisor)) {
            $this->divisor = $divisor;
        }
        $this->addSuccessesToTrials = (bool) $addSuccessesToTrials;
        $this->alpha = $alpha;
        $this->beta = $beta;
    }
    
    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev)
    {
        $revenueTransformed = array_map(function($rev){
            return (float)$rev / (float)$this->divisor;
        }, $revenue);
        if ($this->addSuccessesToTrials) {
            array_walk($visits, function(&$value, $key) use ($revenueTransformed) {
                $value += ceil($revenueTransformed[$key]);
            });
        } else {
            // Make sure successes are not bigger than trials
            array_walk($visits, function($value, $key) use (&$revenueTransformed) {
                $revenueTransformed[$key] = min([$value, $revenueTransformed[$key]]);
            });
        }
        $rCommand = "library(bandit); " . 
            "trials=c(" . implode(',', $visits) . "); " .
            "successes=c(" . implode(',', $revenueTransformed) . "); " .
            "best_binomial_bandit(successes,trials,$this->alpha,$this->beta)";
        $r = new RAdapter();
        $weights = $r->execute($rCommand)[0];
        if (!isset($weights) || round(array_sum($weights)*10) != 10) {
            $rCommand = "library(bandit); " . 
                "trials=c(" . implode(',', $visits) . "); " .
                "successes=c(" . implode(',', $revenueTransformed) . "); " .
                "best_poisson_bandit(successes,trials)";
            $r = new RAdapter();
            $weights = $r->execute($rCommand)[0];
            if (!isset($weights)) {
                $weights = array_pad([], count($visits), 10000 / count($visits));
            }
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

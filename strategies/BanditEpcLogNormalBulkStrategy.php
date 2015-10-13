<?php

require_once 'strategies/BanditRpcBulkStrategy.php';

/**
 * Continuous reward bandit, originally by Victor
 * 
 * Uses log of revenue, gaussian (normal distribution) and inverse-gamma for conjugate prior
 */
class BanditEpcLogNormalBulkStrategy extends BanditRpcBulkStrategy
{
    public $name = 'Continuous reward bandit';
    protected $rScript = 'bulk-bandit-continuous-reward.r';
     
    public function __construct()
    {
        
    }

    public function getWeights($visits, $conversions, $xSales, $revenue, $sumSqRev)
    {
        $hash = md5(serialize($visits) .
            "-" . serialize($conversions) .
            "-" . serialize($xSales) .
            "-" . serialize($revenue) .
            "-" . serialize($sumSqRev));

        if (isset($this->weights[$hash])) {
            $weights = $this->weights[$hash];
            unset($this->weights[$hash]);
            return $weights;
        } elseif (!isset($this->subtests[$hash])) {

            $this->subtests[$hash] = [
                'visits' => $visits,
                'conversions' => $conversions,
                'revenue' => array_map(function($r) {
                    return $r > 1 ? log($r) : 0;
                }, $revenue),
                'sumSqRev' => $sumSqRev,
            ];
        } else {
            $this->idleTime++;
        }

        if (count($this->subtests) >= $this->maxBulkSize || $this->idleTime >= count($this->subtests)) {
            $this->calculateBulkWeights();
        }
    }

    public function displayConfig()
    {
        return "lognormal";
    }

}

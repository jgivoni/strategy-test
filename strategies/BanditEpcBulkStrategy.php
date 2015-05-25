<?php

require_once 'strategies/BanditRpcBulkStrategy.php';

/**
 * Calculates the weights in bulk using revenue per visit and standard deviation of revenue
 * 
 * The r-script uses a simulation under the normal distribution technique to find the number of wins for each arm
 */
class BanditEpcBulkStrategy extends BanditRpcBulkStrategy
{
    public $name = 'Bulk normal bandit on EPC';
    protected $rScript = 'bulk-bandit-epc-normal.r';
     
    public function __construct()
    {
        
    }

    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev)
    {
        $hash = md5(serialize($visits) .
            "-" . serialize($conversions) .
            "-" . serialize($xSales) .
            "-" . serialize($revenue) .
            "-" . serialize($stdev));

        if (isset($this->weights[$hash])) {
            $weights = $this->weights[$hash];
            unset($this->weights[$hash]);
            return $weights;
        } elseif (!isset($this->subtests[$hash])) {

            $this->subtests[$hash] = [
                'visits' => $visits,
                'conversions' => $conversions,
                'revenue' => $revenue,
                'stdev' => $stdev,
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
        return "-";
    }

}

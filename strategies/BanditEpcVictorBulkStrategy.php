<?php

require_once 'strategies/BanditRpcBulkStrategy.php';

/**
 * Continuous reward bandit by Victor
 * 
 * The r-script uses a distribution function called sim_post_gaussian
 */
class BanditEpcVictorBulkStrategy extends BanditRpcBulkStrategy
{
    public $name = 'Continuous reward bandit (1)';
    protected $rScript = 'bulk-bandit-continuous-reward.r';
     
    public function __construct()
    {
        
    }

    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev, $sumSqRev)
    {
        $hash = md5(serialize($visits) .
            "-" . serialize($conversions) .
            "-" . serialize($xSales) .
            "-" . serialize($revenue) .
            "-" . serialize($sumSqRev) .
            "-" . serialize($stdev)) .
            "-" . serialize($revPerConvStdev);

        if (isset($this->weights[$hash])) {
            $weights = $this->weights[$hash];
            unset($this->weights[$hash]);
            return $weights;
        } elseif (!isset($this->subtests[$hash])) {

            $this->subtests[$hash] = [
                'visits' => $visits,
                'conversions' => $conversions,
                'revenue' => $revenue,
                'sumSqRev' => $sumSqRev,
                'stdev' => $stdev,
                'revPerConvStdev' => $revPerConvStdev,
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

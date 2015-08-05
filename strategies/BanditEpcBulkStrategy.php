<?php

require_once 'strategies/BanditRpcBulkStrategy.php';

/**
 * Calculates the weights in bulk using revenue per visit and standard deviation of revenue
 * 
 * The r-script uses a simulation under a hybrid beta-gamma distribution
 */
class BanditEpcBulkStrategy extends BanditRpcBulkStrategy
{
    public $name = 'Bulk hybrid bandit on EPC';
    protected $rScript = 'bulk-bandit-epc-hybrid.r';
     
    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev)
    {
        $hash = md5(serialize($visits) .
            "-" . serialize($conversions) .
            "-" . serialize($xSales) .
            "-" . serialize($revenue) .
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

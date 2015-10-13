<?php

require_once 'strategies/Strategy.php';

/**
 * Calculates the weights in bulk based on epc coerced into the beta distribution
 * The coercion and the beta distribution parameters are dynamically calculated
 */
class BanditRpcBeta2BulkStrategy extends Strategy {

    public $name = 'Bulk binomial bandit on CR/RPC with dynamic priors';
    protected $subtests = [];
    protected $weights = [];
    protected $maxBulkSize = 1000;
    protected $idleTime = 0;
    protected $rScript = 'bulk-beta2-bandit.r';

    public function __construct() {
    }

    public function getWeights($visits, $conversions, $xSales, $revenue, $sumSqRev) {
        $hash = md5(serialize($visits) .
                "-" . serialize($conversions) .
                "-" . serialize($xSales) .
                "-" . serialize($revenue));

        if (isset($this->weights[$hash])) {
            $weights = $this->weights[$hash];
            unset($this->weights[$hash]);
            return $weights;
        } elseif (!isset($this->subtests[$hash])) {
            if (array_sum($conversions) > 0) {
                $ltv = array_sum($revenue) / array_sum($conversions);
            } else {
                $ltv = 1;
            }
            $revenueTransformed = array_map(function($rev) use ($ltv) {
                return (float) $rev / (float) $ltv;
            }, $revenue);

            // Make sure successes are not bigger than trials
            array_walk($visits, function($value, $key) use (&$revenueTransformed) {
                $revenueTransformed[$key] = min([$value, $revenueTransformed[$key]]);
            });

            $this->subtests[$hash] = [
                'trials' => $visits,
                'successes' => $revenueTransformed,
            ];
        } else {
            $this->idleTime++;
        }

        if (count($this->subtests) >= $this->maxBulkSize || $this->idleTime >= count($this->subtests)) {
            $this->calculateBulkWeights();
        }
    }

    public function displayConfig() {
        return "dynamic parameters";
    }

    public function calculateBulkWeights() {
        ob_start();
        include $this->rScript;
        $rScript = ob_get_clean();
        $r = new RAdapter;
        $weights = $r->execute($rScript, RAdapter::FORMAT_CSV);
        if (isset($weights)) {
            $this->weights = array_merge($this->weights, array_combine(array_keys($this->subtests), $weights));
            $this->subtests = [];
            $this->idleTime = 0;
        } else {
            die('process halted');
        }
    }

}

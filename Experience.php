<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Experience
{
     /**
     * Number of x-sales shown per visit
     */
    const XSALES = 2;
    
    /**
     * The average rate of conversion per visit
     * @var float
     */
    public $cr;

    /**
     * The average rate of conversion per x-sale per visit
     * @var float
     */
    public $xscr;

    /**
     * Average revenue per visit
     * @var float
     */
    public $rpc;

    /**
     * 
     * @param float $cr
     * @param float $xscr
     * @param float $rpc
     */
    public function __construct($cr, $xscr, $rpc)
    {
        $this->cr = $cr;
        $this->xscr = $xscr;
        $this->rpc = $rpc;
    }

    /**
     * Returns whether or not this visit simulated a conversion
     * 
     * @return bool
     */
    public function isSimulatedConversion()
    {
        return (float) mt_rand() < (float) mt_getrandmax() * $this->cr;
    }

    /**
     * Returns the number of simulated xsales
     * 
     * This depends on if the visit simulated a conversion or not
     * 
     * @param bool $conversion
     * @return int
     */
    public function getSimulatedXSales($conversion)
    {
        $xSales = 0;
        // You can only have x-sales if there is a conversion
        if ($conversion) {
            for ($x = 0; $x < static::XSALES; $x++) {
                // Divide by the converion rate to compensate for the fact that you 
                // can only have xsales if there is a conversion
                if (mt_rand() < mt_getrandmax() * $this->xscr / $this->cr) {
                    $xSales++;
                }
            }
        }
        return $xSales;
    }

    /**
     * Returns the final revenue this visit will gain
     * 
     * This depends on if the visit simulated a conversion or not
     * and how many x-sales were sold
     * 
     * @param bool $conversion
     * @param int $xSales
     * @return int
     */
    public function getSimulatedRevenue($conversion, $xSales)
    {
        $revenue = 0;
        // You can only have revenue if there is a conversion
        if ($conversion) {
            $revenue = $this->bellNumber((float) $this->rpc / (float) $this->cr, (float) 10);
            if ($revenue < 0) {
                $revenue = 0; // Just in case, though this means we could distort the real mean
            }
        }
        return $revenue;
    }

    /**
     * Returns a random number
     * 
     * The random numbers are normally distributed around the 
     * specified mean with the specified standard deviation 
     * 
     * @param float $mean
     * @param float $stdDeviation
     * @return float
     */
    public function bellNumber($mean, $stdDeviation)
    {
        $rand1 = (float) mt_rand() / (float) mt_getrandmax();
        $rand2 = (float) mt_rand() / (float) mt_getrandmax();
        $gaussianNumber = sqrt(-2 * log($rand1)) * cos(2 * M_PI * $rand2);
        $randomNumber = ($gaussianNumber * $stdDeviation) + $mean;
        return $randomNumber;
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Experience
{
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
     * The average rate of conversion
     * @var float
     */
    public $cr;

    /**
     * The average rate of conversion per x-sale
     * @var float
     */
    public $xscr;

    /**
     * Average revenue per visit
     * @var float
     */
    public $rpc;

}

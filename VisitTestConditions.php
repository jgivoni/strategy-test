<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'Experience.php';

class VisitTestConditions
{
    public $key;
    public $experiences;
    public $weightingRules;

    public function __construct($key)
    {
        $this->key = $key;
    }

}

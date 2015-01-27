<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class AbstractTest
{
    protected $_conditions;

    public function __construct($conditions)
    {
        $this->_conditions = $conditions;
    }

    public function log($message)
    {
        $filename = '/var/log/strategy-test/' . $this->_conditions->key . '.txt';
        $f = fopen($filename, 'a');
        fwrite($f, $message);
        fclose($f);
    }

}

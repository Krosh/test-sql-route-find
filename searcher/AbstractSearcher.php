<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 12.10.2017
 * Time: 19:43
 */

abstract class AbstractSearcher {

    protected $graph;

    public function __construct($graph) {
        $this->graph = $graph;
    }

    abstract public function index();

    abstract public function search($from, $to);
}
<?php

namespace Example;

class Router {
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get(string $route) {
        $result = $this->db->get($route);
        return $result;
    }
}